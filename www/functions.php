<?php
include_once "./models/ItemModel.php";
include_once "./models/OrderModel.php";

class Functions {

	private $conn;

	public function requireSSL() {
		if($_SERVER["HTTPS"] != "on") {
		    header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
		    exit();
		}
	}

	public function fetchItems() {
		$this->dbConnect();
		$stmt = $this->conn->query("SELECT * FROM items");
		$stmt->setFetchMode(PDO::FETCH_CLASS, "ItemModel");
        return $stmt->fetchAll();
	}

	public function fetchSelectedItems() {
		$ids = isset($_COOKIE["items"]) ? explode("-", $_COOKIE["items"]) : array();
		$items = array();

		$this->dbConnect();
		$stmt = $this->conn->prepare("SELECT * FROM items WHERE id=?");
		$stmt->setFetchMode(PDO::FETCH_CLASS, "ItemModel");
		foreach ($ids as $id) {
			$stmt->execute(array($id));
			if ($item = $stmt->fetch()) {
				$items[] = $item;
			}
		}
		return $items;
	}

	public function fetchOrder($id) {
		$this->dbConnect();
		$stmt = $this->conn->prepare("SELECT * FROM orders WHERE id=?");
		$stmt->setFetchMode(PDO::FETCH_CLASS, "OrderModel");
		$stmt->execute(array($id));
		if (!$res = $stmt->fetch()) {
			throw new Exception("This receipt does not exist.");
		}
		if ($res->account != $_SESSION["id"]) {
			throw new Exception("This is not your receipt.");
		}
		return $res;
	}

	public function fetchOrderItems($order) {
		$this->dbConnect();
		$stmt = $this->conn->prepare("SELECT id, name, order_items.price, description, img FROM order_items LEFT JOIN items ON (order_items.itemid=items.id) WHERE orderid = ?");
		$stmt->setFetchMode(PDO::FETCH_CLASS, "ItemModel");
		$stmt->execute(array($order->id));
		return $stmt->fetchAll();
	}

	public function checkout() {

		/*****************************************
		 * Check the userdata and make sure everything is filled and valid.
		 *****************************************/
		if ($this->IsNullOrEmptyString($_POST["inputEmail"])) {
			throw new Exception("Please enter an email.");
		}
		if(!filter_var($_POST["inputEmail"], FILTER_VALIDATE_EMAIL)) {
			throw new Exception("The email provided was not valid.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputFirstName"])) {
			throw new Exception("Please enter a first name.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputLastName"])) {
			throw new Exception("Please enter a last name.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputAddress"])) {
			throw new Exception("Please enter an address.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputPostcode"])) {
			throw new Exception("Please enter a postcode.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputCity"])) {
			throw new Exception("Please enter a city.");
		}
		if ($this->IsNullOrEmptyString($_POST["inputPhone"])) {
			throw new Exception("Please enter a phone-number.");
		}

		/*****************************************
		 * Controll the payment details with the bank.
		 * Since this is just a fake shop, we won't implement that
		 *****************************************/
		$verified = true; // Instead we just assume the payment went through.

		/*****************************************
		 * Continue by saving the order into the database
		 * The prepared statements takes care of any SQL-injection attempts
		 *****************************************/
		if ($verified) {
			$itemids = isset($_COOKIE["items"]) ? explode("-", $_COOKIE["items"]) : array();
			$totalPrice = 0;

			$this->dbConnect();
			$stmt = $this->conn->prepare("SELECT price FROM items WHERE id = ?");
			$remove = array();
			$prices = array();
			foreach ($itemids as $key => $itemid) {
				$stmt->execute(array($itemid));
				if ($price = $stmt->fetchColumn()) {
					$prices[$itemid] = $price;
					$totalPrice += $price;
				} else {
					$remove[] = $key;
				}
			}

			foreach ($remove as $key) {
				unset($itemids[$key]);
			}


			$stmt = $this->conn->prepare("INSERT INTO orders (account, email, fname, lname, address, postcode, city, phone, totalPrice, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$this->conn->beginTransaction();
			if (!$stmt->execute(array(
				$_SESSION["id"],
				$_POST["inputEmail"],
				$_POST["inputFirstName"],
				$_POST["inputLastName"],
				$_POST["inputAddress"],
				$_POST["inputPostcode"],
				$_POST["inputCity"],
				$_POST["inputPhone"],
				$totalPrice,
				"Complete" // Confirmed, Shipping, Complete etc... 
			))) {
				throw new Exception("Something went wrong when saving the order to database.");
			}

			$orderid = $this->conn->lastInsertId();

			$stmt = $this->conn->prepare("INSERT INTO order_items (orderid, itemid, price) VALUES (?, ?, ?)");
			
			foreach ($itemids as $itemid) {
				if (!$stmt->execute(array(
					$orderid,
					$itemid,
					$prices[$itemid]
				))) {
					$this->conn->rollback();
					throw new Exception("Something went wrong when saving the order-items to database.");
				}
			}
			$this->conn->commit();
			header("Location: https://".$_SERVER["HTTP_HOST"]."/receipt.php?id=".$orderid);
		}
	}

	public function register($username, $password, $password_repeat) {

		if ($password != $password_repeat) {
	    	throw new Exception("Passwords does not match");
	  	}

	  	if (strlen($password) < 5) {
	    	throw new Exception("Password is too short");
	  	}

		$username = strtoupper($username);
		$salt = $this->unique_md5();
		$hashpass = hash("sha512", $password.$salt);

		$this->dbConnect();
		$stmt = $this->conn->prepare("INSERT INTO accounts (username, hashpass, salt) VALUES (?, ?, ?)");
		if (!$stmt->execute(array(
			$username,
			$hashpass,
			$salt
		))) {
			switch ($stmt->errorCode()) {
				case 23000:
					throw new Exception("An account with that username already exists.");
			}
		}
		return true;
	}

	public function login($username, $password) {
		$username = strtoupper($username);

		$this->dbConnect();
		$stmt = $this->conn->prepare("SELECT salt FROM accounts WHERE username = ?");
		$stmt->execute(array(
			$username
		));
		$res = $stmt->fetch(PDO::FETCH_OBJ);

		$hashpass = hash("sha512", $password.$res->salt);

		$stmt = $this->conn->prepare("SELECT id FROM accounts WHERE username = ? AND hashpass = ?");
		$stmt->execute(array(
			$username,
			$hashpass
		));

		if ($stmt->rowCount() == 1) {
			$_SESSION["id"] = $stmt->fetchColumn(0);
			$_SESSION["username"] = $username;
			$_SESSION["auth"] = true;
			session_regenerate_id();
			$_SESSION["identity"] = $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"];
			return true;
		}
	}

	public function logout() {
		if (!$_SESSION["auth"]) {
			throw new Exception("You have not yet logged in.");
		}

		session_destroy();
	}

	private function dbConnect() {
		if (!$this->conn) {
			$ini = parse_ini_file("../database.ini");
			$this->conn = new PDO("mysql:host=".$ini["host"].";dbname=".$ini["database"], $ini["username"], $ini["password"]);
		}
	}

	private function IsNullOrEmptyString($string){
	    return (!isset($string) || trim($string)==='');
	}

	private function unique_md5() {
	    mt_srand(microtime(true)*100000 + memory_get_usage(true));
	    return md5(uniqid(mt_rand(), true));
	}
}


?>
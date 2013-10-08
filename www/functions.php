<?php
include_once "./models/ItemModel.php";

class Functions {
	private $conn;

	function dbConnect() {
		if (!$this->conn) {
			require_once "../dbconfig.php";
			$this->conn = new PDO("mysql:host=".$host.";dbname=websecurity", $username, $password);
		}
	}

	function fetchItems() {
		$this->dbConnect();
		$query = $this->conn->query("SELECT * FROM items");
		$query->setFetchMode(PDO::FETCH_CLASS, "ItemModel");
        return $query->fetchAll();
	}

	function fetchSelectedItems() {
		$items = isset($_COOKIE["items"]) ? explode("-", $_COOKIE["items"]) : array();

		$this->dbConnect();
		$stmt = $this->conn->prepare("SELECT * FROM items WHERE id IN (".str_pad("",count($items)*2-1,"?,").")");
		$stmt->execute($items);
		$stmt->setFetchMode(PDO::FETCH_CLASS, "ItemModel");
		return $stmt->fetchAll();
	}

	function register($username, $password, $password_repeat) {

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

	function login($username, $password) {
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
			return true;
		}
	}

	function logout() {
		if (!isset($_SESSION["id"])) {
			throw new Exception("You have not yet logged in.");
		}

		session_destroy();
	}

	function unique_md5() {
	    mt_srand(microtime(true)*100000 + memory_get_usage(true));
	    return md5(uniqid(mt_rand(), true));
	}
}


?>
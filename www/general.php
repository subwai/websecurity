<?php

$functions = new Functions();

session_start();
if (!isset($_SESSION["ServGen"])) {
    session_unset();
    session_regenerate_id();
}
if ($_SESSION["identity"] != $_SERVER["REMOTE_ADDR"].":".$_SERVER["HTTP_USER_AGENT"]) {
	session_unset();
	session_regenerate_id();
}
$_SESSION["ServGen"] = true;
$_SESSION["identity"] = isset($_SESSION["identity"]) ? $_SESSION["identity"] : $_SERVER["REMOTE_ADDR"].":".$_SERVER["HTTP_USER_AGENT"];
$_SESSION["auth"] = isset($_SESSION["auth"]) ? $_SESSION["auth"] : false;

function getPostIfIsset($var) {
	return isset($_POST[$var]) ? $_POST[$var] : "";
}

?>
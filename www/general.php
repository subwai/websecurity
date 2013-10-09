<?php

$functions = new Functions();
$functions->requireSSL();

session_start();
$_SESSION["identity"] = isset($_SESSION["identity"]) ? $_SESSION["identity"] : $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"];

if ($_SESSION["identity"] != $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"]) {
	session_destroy();
	session_start();
}
$_SESSION["auth"] = isset($_SESSION["auth"]) ? $_SESSION["auth"] : false;

function getPostIfIsset($var) {
	return isset($_POST[$var]) ? $_POST[$var] : "";
}

?>
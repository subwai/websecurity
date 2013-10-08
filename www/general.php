<?php
include "functions.php";

$functions = new Functions();
session_start();
$_SESSION["auth"] = isset($_SESSION["auth"]) ? $_SESSION["auth"] : false;

?>
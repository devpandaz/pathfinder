<?php

session_start();

if (isset($_POST["login"])) {
    $username = $_POST["username"];     // even it may be that email is being submitted, we are still gonna call it username
    $pwd = $_POST["password"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if (emptyInputLogin($username, $pwd) !== false) {
        $_SESSION["login-error"] = "Username/email and password is required!";
    } else {
        loginUser($conn, $username, $pwd);
    }
}

header("location: ../entry/login.php");
exit;
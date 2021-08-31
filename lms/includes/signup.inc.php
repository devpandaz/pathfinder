<?php

if (isset($_POST['signup'])) {

    $type = $_POST["type"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $pwd = $_POST["password"];
    $pwdRepeat = $_POST["confirm-password"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if (emptyInputSignup($username, $email, $pwd, $pwdRepeat) !== false) {
        $_SESSION["signup-error"] = "Fill in all fields!";
        $_SESSION["signup-temp-username"] = $username;
        $_SESSION["signup-temp-email"] = $email;
    }
    if (invalidUsername($username) !== false) {
        $_SESSION["signup-error"] = "Invalid username!";
        $_SESSION["signup-temp-email"] = $email;
    }
    if (invalidEmail($email) !== false) {
        $_SESSION["signup-error"] = "Invalid email!";
        $_SESSION["signup-temp-username"] = $username;
    }
    if (pwdDontMatch($pwd, $pwdRepeat) !== false) {
        $_SESSION["signup-error"] = "Your passwords don't match!";
        $_SESSION["signup-temp-username"] = $username;
        $_SESSION["signup-temp-email"] = $email;
    }
    if (usernameExists($conn, $username, $email) !== false) {
        $_SESSION["signup-error"] = "Username or email is taken.";
    }

    if (isset($_SESSION["signup-error"])) {
        $_SESSION["temp-type"] = $type;
        header("location: ../entry/signup.php");
        exit;
    } else {
        createUser($conn, $username, $email, $pwd, $type);
    }

} else {
    header("location: ../index.php");
    exit;
}


<?php

include_once 'dbh.inc.php';
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type == "librarian") {
        mysqli_query($conn, "UPDATE users SET usersType = 'member' WHERE usersUsername = '" . $_SESSION["userusername"] . "';");

        // push notification to admin
        $notification = $_SESSION["userusername"] . " quit being a librarian.";
        mysqli_query($conn, "INSERT INTO `notification` (userId, `text`) VALUES ('1', '$notification');");
        
        $_SESSION["quit"] = "You are no longer a librarian anymore. From now on, you are just a normal member. If you wish to become a librarian again the next time, you will have to send a request to the admin again. ";

        header("location: ../profile.php");
        exit();

    } else {
        header("location: ../index.php");
        exit();
    }
} else {
    header("location: ../index.php");
    exit();
}
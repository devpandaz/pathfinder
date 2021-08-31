<?php

include_once 'dbh.inc.php';
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type == "member") {

        mysqli_query($conn, "UPDATE users SET usersType = 'librarian, pending' WHERE usersUsername = '" . $_SESSION["userusername"] . "';");
        
        // then add notification to admin
        $notification = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersUsername"] . " requested to be a librarian.";
        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('1', '$notification');");

        $_SESSION["requested"] = "Your request to become a librarian is successfully sent to the admin!";

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
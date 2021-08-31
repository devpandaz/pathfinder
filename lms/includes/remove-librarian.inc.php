<?php

include_once 'dbh.inc.php';
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type == "admin" and isset($_GET["subject"])) {
        $targetId = $_GET["subject"];
        mysqli_query($conn, "UPDATE users SET usersType = 'member' WHERE usersId = '$targetId';");
        
        // then add notification to user
        $notification = "You have been removed as an librarian. Please contact your library admin for more details.";
        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$targetId', '$notification');");
        
        header("location: ../admin.php");
        exit();
    } else {
        header("location: ../admin.php");
        exit();
    }
} else {
    header("location: ../admin.php");
    exit();
}
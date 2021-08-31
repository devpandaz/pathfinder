<?php

include_once 'dbh.inc.php';
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type == "admin" and isset($_GET["subject"])) {
        $targetId = $_GET["subject"];
        
        // set the user membership type to member only
        mysqli_query($conn, "UPDATE users SET usersType = 'member' WHERE usersId = '$targetId'");

        // then add notification to user
        $notification = "Your request to become a librarian is turned down by the admin, unfortunately. Please contact your library admin for more details. You may request again however as much as you want.";
        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$targetId', '$notification');");
    }
}
header("location: ../admin.php");
exit;
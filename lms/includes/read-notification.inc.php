<?php include_once 'dbh.inc.php';?>

<?php
    $id = $_POST["notificationId"];
    $sql = "UPDATE `Notification` SET `read`='1' WHERE `notificationId`=$id;";
    mysqli_query($conn, $sql);
?>
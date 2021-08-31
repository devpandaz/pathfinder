<?php include_once 'dbh.inc.php';?>
<?php 
    function get_userId($db, $username) {
        $sql = "SELECT usersId FROM users WHERE usersUsername='$username';";
        return mysqli_fetch_assoc(mysqli_query($db, $sql))["usersId"];
    }
    
    $user = $_POST["username"];
    $content = $_POST["notify-content"];

    $id = get_userId($conn, $user);

    // insert new record with the details  
    $sql = "INSERT INTO `Notification`(`userId`, `text`) VALUES($id, '$content');";
    mysqli_query($conn, $sql);
    
    header("location:../notification.php");
?>
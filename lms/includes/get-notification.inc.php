<?php include_once 'dbh.inc.php';?>
<?php

    function get_userId($db, $username) {
        $sql = "SELECT usersId FROM users WHERE usersUsername='$username';";
        return mysqli_fetch_assoc(mysqli_query($db, $sql))["usersId"];
    }

    $userId = $_POST["user"];
    // $userId = get_userId($conn, $username);
    
    $sql = "SELECT * FROM `Notification` WHERE `userId`='$userId' ORDER BY `dateCreated` ASC;";
    $result = mysqli_query($conn, $sql);  
    $notifications = Array();
    
    if(mysqli_num_rows($result)){   
        while($row = mysqli_fetch_assoc($result)){  
            $notifications[] = Array("id" => $row["notificationId"], "text" => $row["text"], "timestamp" => $row["dateCreated"],"read" => $row["read"]);  
        }
    }
    
    /*  
    * now we need to find the activity that relates to the notification  
    * and create a text message that will be displayed to the user  
    * containing the users who are responsible for that particular activity  
    */  
    
    echo(json_encode($notifications)); // convert array to JSON text  

?>
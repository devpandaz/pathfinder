<?php

if (isset($_POST["reset-password-submit"])) {

    $selector = $_POST["selector"];
    $validator = $_POST["validator"];
    $password = $_POST["pwd"];
    $passwordRepeat = $_POST["pwd-repeat"];

    if (empty($password) || empty($passwordRepeat)) {
        header("location: ../create_new_password.php?newpwd=empty&selector=$selector&validator=$validator");
        exit();
    } else if ($password != $passwordRepeat) {
        header("location: ../create_new_password.php?newpwd=pwdnotsame&selector=$selector&validator=$validator");
        exit();
    }

    $currentDate = date("U");

    require_once 'dbh.inc.php';

    $sql = "SELECT * FROM pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >= ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Either the email you used to reset password is not associated with your account, or this link has expired.";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $selector, $currentDate);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$row = mysqli_fetch_assoc($result)) {
            echo "You need to resubmit your reset request.";
            exit();
        } else {
            $tokenBin = hex2bin($validator);
            $tokenCheck = password_verify($tokenBin, $row["pwdResetToken"]);

            if ($tokenCheck === false) {
                echo "You need to resubmit your reset request.";
                exit();
            } else if ($tokenCheck === true) { 
                $tokenEmail = $row["pwdResetEmail"];

                $sql = "SELECT * FROM users WHERE usersEmail=?";

                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "Either the email you used to reset password is not associated with your account, or this link has expired.";
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    if (!$row = mysqli_fetch_assoc($result)) {
                        echo "Either the email you used to reset password is not associated with your account, or this link has expired.";
                        exit();
                    } else {

                        $sql = "UPDATE users SET usersPwd=? WHERE usersEmail=?;";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "Either the email you used to reset password is not associated with your account, or this link has expired.";
                            exit();
                        } else {
                            $newPwdHash = password_hash($password, PASSWORD_DEFAULT);
                            mysqli_stmt_bind_param($stmt, "ss", $newPwdHash, $tokenEmail);
                            mysqli_stmt_execute($stmt);

                            $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?";
                            $stmt = mysqli_stmt_init($conn);
                            if (!mysqli_stmt_prepare($stmt, $sql)) {
                                echo "Either the email you used to reset password is not associated with your account, or this link has expired.";
                                exit();
                            } else {
                                mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                                mysqli_stmt_execute($stmt);

                                // then add notification to user
                                $notification = "You reset your account password. Not you? Contact your library admin as soon as possible.";
                                mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('" . $_SESSION["userid"] . "', '$notification');");

                                header("location: ../entry/login.php");
                            }
                        }
                        
                    }
                }
            }
        }
    }

} else {
    header("location: ../index.php");
    exit();
}
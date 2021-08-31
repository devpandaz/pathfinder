<?php

session_start();

include_once 'dbh.inc.php';
include_once 'functions.inc.php';

$profileURL = $_POST["profile-url"];    // get url from header to see which user is it

$subjectId = substr($profileURL, 8, 1);

// fetch old details
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '$subjectId';"));


if (isset($_POST['btnSubmit'])) {   // one of the buttons in the three forms is clicked

    if (isset($_POST["changeUsername"])) {
        $currentUsername = $user["usersUsername"];
        $newUsername = $_POST["username"];

        if (!empty($newUsername)) {
            if ($newUsername != $currentUsername) {
                if (invalidUsername($newUsername) !== false) {
                    $_SESSION["edit-account-error-username"] = "The username entered is invalid!";
                } else {
                    if (editAccountUsernameExists($conn, $subjectId, $newUsername)) {
                        $_SESSION["edit-account-error-username"] = "Username is taken.";
                    } else {
                        $sql = "UPDATE users SET usersUsername = ? WHERE usersId = '$subjectId'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            $_SESSION["edit-account-error-username"] = "Sorry, something went wrong.";
                        }

                        mysqli_stmt_bind_param($stmt, "s", $newUsername);
                        mysqli_stmt_execute($stmt);

                        mysqli_stmt_close($stmt);

                        $_SESSION["userusername"] = $newUsername;

                        $_SESSION["edit-account-success-msg"] = "Username is updated. ";

                        // then add notification to user
                        $notification = "You changed your username. Not you? Contact the library admin as soon as possible.";
                        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$subjectId', '$notification');");
                    }
                }
            }
        }

    } else if (isset($_POST["changeEmail"])) {
        $currentEmail = $user["usersEmail"];
        $newEmail = $_POST["email"];

        if (!empty($newEmail)) {
            if ($newEmail != $currentEmail) {
                if (invalidEmail($newEmail) !== false) {
                    $_SESSION["edit-account-error-email"] = "The email entered is invalid!";
                } else {
                    if (editAccountEmailExists($conn, $subjectId, $newEmail)) {
                        $_SESSION["edit-account-error-email"] = "Email is taken.";
                    } else {
                        $sql = "UPDATE users SET usersEmail = ? WHERE usersId = '$subjectId'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            $_SESSION["edit-account-error-email"] = "Sorry, something went wrong.";
                        }
    
                        mysqli_stmt_bind_param($stmt, "s", $newEmail);
                        mysqli_stmt_execute($stmt);
    
                        mysqli_stmt_close($stmt);

                        $_SESSION["edit-account-success-msg"] = "Email is updated. ";

                        // then add notification to user
                        $notification = "You changed your email. Not you? Contact the library admin as soon as possible.";
                        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$subjectId', '$notification');");
                    }
                }
            }
        }
    } else if (isset($_POST["changePwd"])) {
        $currentPwd = $user["usersPwd"];
        $oldPwd = $_POST["old-password"];
        $newPwd = $_POST["new-password"];
        $newPwdRepeat = $_POST["confirm-new-password"];

        if (!(empty($oldPwd) || empty($newPwd)|| empty($newPwdRepeat))) {
            if (canChangePwd($conn, $oldPwd, $subjectId)) {
                if (pwdDontMatch($newPwd, $newPwdRepeat)) {
                    $_SESSION["edit-account-error-pwd"] = "Your new passwords don't match!";
                } else {
                    $sql = "UPDATE users SET usersPwd = ? WHERE usersId = '$subjectId'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $_SESSION["edit-account-error-pwd"] = "Sorry, something went wrong.";
                    }
    
                    $hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);
    
                    mysqli_stmt_bind_param($stmt, "s", $hashedPwd);
                    mysqli_stmt_execute($stmt);
    
                    mysqli_stmt_close($stmt);

                    $_SESSION["edit-account-success-msg"] = "Password is updated. ";

                    // then add notification to user
                    $notification = "You changed your password. Not you? Contact the library admin as soon as possible.";
                    mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$subjectId', '$notification');");
                }
            } else {
                $_SESSION["edit-account-error-pwd"] = "Your old password is incorrect!";
            }
        } else {
            $_SESSION["edit-account-error-pwd"] = "Make sure you enter the old password, new password and confirm new password in order to change your password!";
        }
    }

    // after changing one of the three datas...
    header("location: ../edit_account.php?subject=$subjectId");

} else {
    header("location: ../index.php");
    exit();
}
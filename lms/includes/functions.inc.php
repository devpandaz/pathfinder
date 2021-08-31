<?php

session_start();

require '../vendor/autoload.php';   // composer's autoload

function emptyInputSignup($username, $email, $pwd, $pwdRepeat) {
    $result;
    if (empty($username) || empty($email) || empty($pwd) || empty($pwdRepeat)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidUsername($username) {
    $result;
    if (!preg_match("/^[a-zA-Z ]*$/", $username)) {
        if (strlen($username) >= 3 and strlen($username) <= 70) {
            $result = true;
        } else {
            $result = false;
        }
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidEmail($email) {
    $result;
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function pwdDontMatch($pwd, $pwdRepeat) {
    $result;
    if ($pwd !== $pwdRepeat) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function usernameExists($conn, $username, $email) {
    $sql = "SELECT * FROM users WHERE usersUsername = ? or usersEmail = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION["signup-error"] = "Sorry, something went wrong. ";
        header("location: ../entry/signup.php");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        return $row;
    } else {
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function createUser($conn, $username, $email, $pwd, $type) {
    $sql = "INSERT INTO users (usersUsername, usersEmail, usersPwd, usersType) values (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        $_SESSION["signup-error"] = "Sorry, something went wrong.";
        $_SESSION["temp-type"] = $type;
        header("location: ../entry/signup.php");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    if ($type == "librarian") {
        $type = "librarian, pending";
    } else {
        $type = "member";
    }

    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $hashedPwd, $type);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    loginUser($conn, $username, $pwd);
    exit();
}

function emptyInputLogin($username, $pwd) {
    $result = false;
    if (empty($username) || empty($pwd)) {
        $result = true;
    }
    return $result;
}

function loginUser($conn, $username, $pwd) {
    $usernameExists = usernameExists($conn, $username, $username);

    if ($usernameExists === false) {
        $_SESSION["login-error"] = "Incorrect login details";
        header("location: ../entry/login.php");
        exit();
    }

    $pwdHashed = $usernameExists["usersPwd"];
    $checkPwd = password_verify($pwd, $pwdHashed);

    if ($checkPwd === false) {
        $_SESSION["login-error"] = "Incorrect login details";
        header("location: ../entry/login.php");
        exit();
    } else if ($checkPwd === true) {
        session_start();

        // setting session variables to store logged in user data
        $_SESSION["userid"] = $usernameExists["usersId"];
        $_SESSION["userusername"] = $usernameExists["usersUsername"];

        $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];

        if ($type == "admin") {
            header("location: ../admin.php");
            exit();
        }
        header("location: ../index.php");
        exit();
    }
}

function emptyInputAddBook($isbn, $title, $author, $descr, $category, $publisher, $language, $price, $year, $pages, $shelf) {
    $result;
    if (empty($isbn) || empty($title) || empty($author) || empty($category) || empty($publisher) || empty($language) || empty($price) || empty($year) || empty($pages) || empty($shelf)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

function invalidISBN($isbn_no) {
    $isbn = new Isbn\Isbn();
    $result;
    if ($isbn->validation->isbn($isbn_no)) {
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}

function isbnExists($conn, $isbn_no) {
    $result;
    $sql = "SELECT * FROM books WHERE booksISBN = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../index.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $isbn_no);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        $result = true;
    } else {
        $result = false;
    }

    mysqli_stmt_close($stmt);

    return $result;
}

function invalidQuantity($quantity) {
    $result;
    $filter_options = array( 
        'options' => array('min_range' => 1)
    );
    
    if (filter_var($quantity, FILTER_VALIDATE_INT, $filter_options) !== false) {
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}

function invalidPrice($price) {
    $result;
    $filter_options = array( 
        'options' => array('min_range' => 0)
    );
    
    if (filter_var($price, FILTER_VALIDATE_FLOAT, $filter_options) !== false) {
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}

function invalidPages($pages) {
    $result;
    $filter_options = array( 
        'options' => array('min_range' => 1)
    );
    
    if (filter_var($pages, FILTER_VALIDATE_INT, $filter_options) !== false) {
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}

function editAccountUsernameExists($conn, $subjectId, $newUsername) {
    $sql = "SELECT * FROM users WHERE usersUsername = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../edit_account.php?subject=$subjectId&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $newUsername);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        $result = true;
    } else {
        $result = false;
    }

    mysqli_stmt_close($stmt);

    return $result;
}

function editAccountEmailExists($conn, $subjectId, $newEmail) {
    $sql = "SELECT * FROM users WHERE usersEmail = ?;";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("location: ../edit_account.php?subject=$subjectId&error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $newEmail);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($resultData)) {
        $result = true;
    } else {
        $result = false;
    }

    mysqli_stmt_close($stmt);

    return $result;
}

function canChangePwd($conn, $oldPwd, $subjectId) {
    $result;
    $currentPwd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '$subjectId'"))["usersPwd"];
    if (password_verify($oldPwd, $currentPwd)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}
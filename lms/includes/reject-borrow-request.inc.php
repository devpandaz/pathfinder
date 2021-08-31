<?php

include_once 'dbh.inc.php';

session_start();

// check whether link valid
if (!isset($_GET["user"]) || !isset($_GET["index"]) || !isset($_GET["isbn"])) {
    header("location: ../index.php");
} else {
    $usersBorrowPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT borrowPending FROM users WHERE usersId = '" . $_GET["user"] . "';"))["borrowPending"];
    $pendingBookList = explode(">", array_slice(explode(",", $usersBorrowPending), 1)[$_GET["index"]]);
    if (!($pendingBookList[0] == $_GET["isbn"])) {
        header("location: ../index.php");
        exit();
    }
}

// check whether it is librarian or admin who is accessing this page or not
if (isset($_SESSION["userid"])) {

    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type != "admin" && $type != "librarian") {
        header("location: ../index.php");
        exit();
    }

    // delete the borrow pending record
    $usersBorrowPending = explode(",", $usersBorrowPending);
    if (count($usersBorrowPending) == 2) {
        mysqli_query($conn, "UPDATE users SET borrowPending = '' WHERE usersId = '" . $_GET["user"] . "'");
    } else {
        $usersBorrowPending = array_slice($usersBorrowPending, 1);
        unset($usersBorrowPending[$_GET["index"]]);
        array_values($usersBorrowPending);
        mysqli_query($conn, "UPDATE users SET borrowPending = '," . implode(",", $usersBorrowPending) . "' WHERE usersId = '" . $_GET["user"] . "'");
    }

    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '" . $_GET["isbn"] . "';"));
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '" . $_GET["user"] . "';"));

    mysqli_query($conn, "UPDATE books SET booksPending = '" . (int)$book["booksPending"] - 1 . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");

    mysqli_query($conn, "UPDATE users SET bookQuota = '" . (int)$user["bookQuota"] - 1 .  "' WHERE usersId = '" . $_GET["user"] . "';");

    // then add notification to user
    $notification = "Your request to borrow the book named " . $book["booksTitle"] . " (" . $book["booksISBN"] . ") is turned down by the authority, unfortunately. Please contact the librarians for more details. You may request again however as much as you want as long as the book is still in stock.";
    mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('" . $_GET["user"] . "', '$notification');");

    if ($type == "admin") {
        header("location: ../admin.php");
        exit;
    } else {
        header("location: ../librarian.php");
        exit;
    }

} else {
    header("location: ../index.php");
}
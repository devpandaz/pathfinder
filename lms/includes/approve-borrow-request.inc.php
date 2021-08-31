<?php

include_once 'dbh.inc.php';

session_start();

// check whether link valid
if (!(isset($_GET["user"]) && isset($_GET["index"]) && isset($_GET["isbn"]))) {
    header("location: ../index.php");
    exit;
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

    // now do the same process as when librarian and admin borrow books at book.php

    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '" . $_GET["isbn"] . "';"));
    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '" . $_GET["user"] . "';"));

    // update the loaned number of the book
    mysqli_query($conn, "UPDATE books SET booksLoaned = '" . (int)$book["booksLoaned"] + 1 . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");
    mysqli_query($conn, "UPDATE books SET booksPending = '" . (int)$book["booksPending"] - 1 . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");

    // add record to user's borrowedBooks together with the expiry date
    mysqli_query($conn, "UPDATE users SET borrowedBooks = '" . $user["borrowedBooks"] .  "," . $_GET["isbn"] . ">" . date('Y-m-d',strtotime('+14 day',strtotime(date("Y-m-d")))) . "' WHERE usersId = '" . $_GET["user"] . "';");

    $_SESSION["approve-borrow-success-msg"] = $user["usersUsername"] . "'s borrow request of the book named '" . $book["booksTitle"] . "' (" . $book["booksISBN"] . ") approved successfully.";

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

    // then add notification to user
    $notification = "Your request to borrow the book named " . $book["booksTitle"] . " (" . $book["booksISBN"] . ") is accepted by the authority. Remember to return the book in time.";
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
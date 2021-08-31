<?php

include_once 'dbh.inc.php';

session_start();

if (!isset($_SESSION["userid"])) {
    header("location: ../index.php");
}

// check whether link valid
if (!isset($_GET["index"]) || !isset($_GET["isbn"])) {
    header("location: ../index.php");
} else {
    $usersBorrowedBooks = mysqli_fetch_assoc(mysqli_query($conn, "SELECT borrowedBooks FROM users WHERE usersId = '" . $_SESSION["userid"] . "';"))["borrowedBooks"];
    $borrowedBooksList = explode(">", array_slice(explode(",", $usersBorrowedBooks), 1)[$_GET["index"]]);
    if (!($borrowedBooksList[0] == $_GET["isbn"])) {
        header("location: ../index.php");
        exit();
    } else {
        $currentDate = strtotime(date("Y-m-d"));
        $expirydate = strtotime($borrowedBooksList[1]);

        $days = ($expirydate - $currentDate) / 60 / 60 / 24;

        if ($days < 0) {
            $fine = -($days) * 0.5;
        }
    }
}

if (isset($fine)) {
    $_SESSION["fine"] = "RM " . number_format((float)$fine, 2, '.', '');
}

$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '" . $_GET["isbn"] . "';"));
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '" . $_SESSION["userid"] . "';"));

// update the loaned number of the book
mysqli_query($conn, "UPDATE books SET booksLoaned = '" . (int)$book["booksLoaned"] - 1 . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");

$_SESSION["return-book-success-msg"] = "You successfully returned '" . $book["booksTitle"] . "' (" . $book["booksISBN"] . ").";

// update user borrowed books
$usersBorrowedBooks = explode(",", $usersBorrowedBooks);
if (count($usersBorrowedBooks) == 2) {
    mysqli_query($conn, "UPDATE users SET borrowedBooks = '' WHERE usersId = '" . $_SESSION["userid"] . "'");
    mysqli_query($conn, "UPDATE users SET bookQuota = '" . ((int)$user["bookQuota"]) - 1 .  "' WHERE usersId = '" . $_SESSION["userid"] . "';");
} else {
    $usersBorrowedBooks = array_slice($usersBorrowedBooks, 1);
    unset($usersBorrowedBooks[$_GET["index"]]);
    array_values($usersBorrowedBooks);
    mysqli_query($conn, "UPDATE users SET borrowedBooks = '," . implode(",", $usersBorrowedBooks) . "' WHERE usersId = '" . $_SESSION["userid"] . "'");

    // update user quota
    mysqli_query($conn, "UPDATE users SET bookQuota = '" . ((int)$user["bookQuota"]) - 1 .  "' WHERE usersId = '" . $_SESSION["userid"] . "';");
}

// now add the returned book to the first member's reservation pick up cart in book reservation queue
$reservationQueue = array_slice(explode(",", $book["booksReservedQueue"]), 1);
if (!empty($reservationQueue)) {
    $firstUserInTheQueue = explode(">", $reservationQueue[0])[1];
    mysqli_query($conn, "UPDATE users SET reservationCart = '" . $user["reservationCart"] .  "," . $_GET["isbn"] . "' WHERE usersId = '" . $firstUserInTheQueue . "';");

    // delete the user's reservation record in book reservation queue
    unset($reservationQueue[0]);
    array_values($reservationQueue);
    mysqli_query($conn, "UPDATE books SET booksReservedQueue = '" . count($reservationQueue) == 2 ? "," : "" . implode(",", $reservationQueue) . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");

    // update the loaned number of the book
    mysqli_query($conn, "UPDATE books SET booksLoaned = '" . (int)$book["booksLoaned"] + 1 . "' WHERE booksISBN = '" . $_GET["isbn"] . "';");
}

header("location: ../profile.php");
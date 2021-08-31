<?php
include 'dbh.inc.php';
include 'functions.inc.php';

date_default_timezone_set('Asia/Kuala_Lumpur');

session_start();

// check whether is logged in or not
if (!isset($_SESSION["userid"]) || !isset($_GET["id"])) {
    header("location: ../index.php");
    exit();
}

$target = $_GET["id"];

if (invalidISBN($target)) {
    header("location: ../index.php");
    exit();
}

$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$target';"));
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '" . $_SESSION["userid"] . "';"));

if ($user["usersType"] == "librarian" or $user["usersType"] == "admin") {   
    // can straight away borrow, no need approval
    // update the loaned number of the book
    mysqli_query($conn, "UPDATE books SET booksLoaned = '" . (int)$book["booksLoaned"] + 1 . "' WHERE booksISBN = '$target';");

    // add record to user's borrowedBooks together with the expiry date
    mysqli_query($conn, "UPDATE users SET borrowedBooks = '" . $user["borrowedBooks"] .  "," . $target . ">" . date('Y-m-d',strtotime('+14 day',strtotime(date("Y-m-d")))) . "' WHERE usersId = '" . $_SESSION["userid"] . "';");

    $_SESSION["borrow-book-success-msg"] = "Book borrowed successfully.";

} else {
    // add request since this is member, need approval
    mysqli_query($conn, "UPDATE users SET borrowPending = '" . $user["borrowPending"] .  "," . $target . ">Requested at " . date("l Y-m-d h:i:sa") . "' WHERE usersId = '" . $_SESSION["userid"] . "';");
    $_SESSION["borrow-book-success-msg"] = "Borrow request is successfully sent. You will need to wait for any of the librarian's approval in order to bring the book home.";

    mysqli_query($conn, "UPDATE books SET booksPending = '" . (int)$book["booksPending"] + 1 . "' WHERE booksISBN = '$target';");

    // then add notification to admin
    $notification = $user["usersUsername"] . " wants to borrow a book.";

    $authorities = mysqli_query($conn, "SELECT usersId FROM users WHERE usersType = 'admin' or usersType = 'librarian';");
    while ($row = mysqli_fetch_assoc($authorities)) {
        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES (" . $row["usersId"] . ", '$notification');");
    }
}

// update quota for every type of user, even though admin's can be ignored
mysqli_query($conn, "UPDATE users SET bookQuota = '" . (int)$user["bookQuota"] + 1 .  "' WHERE usersId = '" . $_SESSION["userid"] . "';");

header("location: ../book.php?id=$target");
exit();

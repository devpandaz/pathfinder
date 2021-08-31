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

// first, add to reservation queue in books table for that particular book
mysqli_query($conn, "UPDATE books SET booksReservedQueue = '" . $book["booksReservedQueue"] .  "," . $target . ">" . $_SESSION["userid"] . "' WHERE booksISBN = '" . $target . "';");

$_SESSION["book-reserved-success-msg"] = "Book reserved successfully. We will add it to your reservation cart as soon as the book is available. Do remember to pick up (borrow) the book at your reservation cart once notified, within 3 days.";

header("location: ../book.php?id=$target");
exit;
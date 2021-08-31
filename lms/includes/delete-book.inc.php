<?php

include_once 'dbh.inc.php';
include_once 'functions.inc.php';   // to use the isbn validating function
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type != "admin" && $type != "librarian") {
        header("location: ../index.php");
        exit();
    }
}

if (isset($_GET["id"])) {

    $isbn = $_GET["id"];

    // validate id first
    if (!invalidISBN($isbn)) {
        // check whether if copies of book is borrowed already
        $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$isbn';"));
        if ((int) $book["booksLoaned"] != 0) {
            $_SESSION["book-not-deleted"] = "Book named '" . $book["booksTitle"] . " ($isbn)' is removed unsuccessfully. There are still copies of this book who is borrowed and not returned yet. Make sure to only come back and delete once all the copies are returned already.";
        } else {
            mysqli_query($conn, "DELETE FROM books WHERE booksISBN = '$isbn';");
            unlink("../uploads/book-covers/" . $book["booksCoverImage"]);
            $_SESSION["delete-book-success-msg"] = "Book named '" . $book["booksTitle"] . " ($isbn)' is removed successfully.";
        }
    }
}

header("location: ../manage_books.php");
exit();
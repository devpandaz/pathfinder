<?php

session_start();

include_once 'dbh.inc.php';
include_once 'functions.inc.php';

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type != "admin") {
        header("location: index.php");
        exit;
    }
    
    // admin access: ok
    if (isset($_GET["target"])) {
        $targetISBN = $_GET["target"];
        if (invalidISBN($targetISBN)) {
            header("location: ../manage_books.php");
            exit();
        } else {
            // admin access: ok, link with isbn id: ok
            // fetch book details
            $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$targetISBN';"));
            
            // then delete the previous image its name stored in the db
            unlink("../uploads/book-covers/" . $book["booksCoverImage"]);

            // then update the file name stored in the database
            $sql = "UPDATE books SET booksCoverImage = 'default-book-cover.png' WHERE booksISBN = '$targetISBN'";
            mysqli_query($conn, $sql);

            $_SESSION["edit-book-success-msg"] = "Book cover image reset to default.";
            header("location: ../edit_book.php?id=$targetISBN");
        }
    }
} else {
    header("location: ../index.php");
}
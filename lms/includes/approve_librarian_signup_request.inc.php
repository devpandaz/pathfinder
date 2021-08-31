<?php

include_once 'dbh.inc.php';
session_start();

if (isset($_SESSION["userid"])) {
    $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    if ($type == "admin" and isset($_GET["subject"])) {
        $targetId = $_GET["subject"];
        mysqli_query($conn, "UPDATE users SET usersType = 'librarian' WHERE usersId = '$targetId';");
    
        // transfer all pending books that he requested when back then he was a member to his borrowed books list since he is already a librarian and no longer need approval from authorities

        $usersBorrowPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT borrowPending FROM users WHERE usersId = '$targetId';"))["borrowPending"];
        $pendingBookList = array_slice(explode(",", $usersBorrowPending), 1);

        if (!empty($pendingBookList)) {
            foreach ($pendingBookList as $pendingBook) {
                $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '$targetId';"));
                $isbn = explode(">", $pendingBook)[0];
                $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$isbn';"));

                // update the loaned number of the book
                mysqli_query($conn, "UPDATE books SET booksLoaned = '" . (int)$book["booksLoaned"] + 1 . "' WHERE booksISBN = '$isbn';");
                mysqli_query($conn, "UPDATE books SET booksPending = '" . (int)$book["booksPending"] - 1 . "' WHERE booksISBN = '$isbn';");

                // add record to user's borrowedBooks together with the expiry date
                mysqli_query($conn, "UPDATE users SET borrowedBooks = '" . $user["borrowedBooks"] .  ",$isbn>" . date('Y-m-d',strtotime('+14 day',strtotime(date("Y-m-d")))) . "' WHERE usersId = '$targetId';");
            }

            mysqli_query($conn, "UPDATE users SET borrowPending = '' WHERE usersId = '$targetId';");

        }

        // then add notification to user
        $notification = "Your request to become a librarian is accepted by the admin! You are officially now a librarian of this library.";
        mysqli_query($conn, "INSERT INTO Notification (userId, text) VALUES ('$targetId', '$notification');");
    }
}
header("location: ../admin.php");
exit();
<?php
    $dbservername = "localhost";
    $dbusername = "root";
    $dbpassword = "";

    $conn = mysqli_connect($dbservername, $dbusername, $dbpassword);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS pathfinder");

    $dbname = "pathfinder";

    $conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $dbname);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS users (
        usersId int(11) PRIMARY KEY AUTO_INCREMENT NOT null,
        usersUsername TEXT NOT NULL,
        usersEmail TEXT NOT NULL,
        usersPwd TEXT NOT NULL, 
        usersType TEXT NOT NULL,
        borrowedBooks LONGTEXT NOT NULL,
        bookQuota TEXT NOT NULL,
        borrowPending LONGTEXT NOT NULL
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS pwdReset (
        pwdResetId int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
        pwdResetEmail TEXT NOT NULL,
        pwdResetSelector TEXT NOT NULL,
        pwdResetToken LONGTEXT NOT NULL,
        pwdResetExpires TEXT NOT NULL
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS books (
        booksId int(11) PRIMARY KEY AUTO_INCREMENT NOT NULL,
        booksISBN TEXT NOT NULL,
        booksQuantity TEXT NOT NULL,
        booksTitle TEXT NOT NULL,
        booksAuthor TEXT NOT NULL,
        booksCoverImage LONGTEXT NOT NULL,
        booksDescription LONGTEXT NOT NULL,
        booksCategory TEXT NOT NULL,
        booksPublisher TEXT NOT NULL,
        booksLanguage TEXT NOT NULL,
        booksPrice TEXT NOT NULL,
        booksYear TEXT NOT NULL,
        booksPages TEXT NOT NULL,
        booksShelf TEXT NOT NULL,
        booksLoaned TEXT NOT NULL,
        booksPending TEXT NOT NULL,
        dateAdded DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `Notification` (
        `notificationId` int AUTO_INCREMENT NOT NULL,
        `userId` int NOT NULL,
        `text` TEXT NOT NULL,
        `dateCreated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `read` BOOLEAN NOT NULL DEFAULT 0,
        CONSTRAINT PK_Notification PRIMARY KEY (`notificationId`),
        CONSTRAINT FK_Notification FOREIGN KEY (`userId`) REFERENCES users(usersId)
    )");
?>

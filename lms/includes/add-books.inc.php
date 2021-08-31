<?php

if (isset($_POST["add-book"])) {

    session_start();
    
    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    // start validation

    // check for the image
    $all_ok = true;
    if (!empty($_FILES["book-cover"]["name"])) {
        $bookCoverName = time() . '-' . $_FILES["book-cover"]["name"];
        // For image upload
        $target_dir = "../uploads/book-covers/";
        $target_file = $target_dir . basename($bookCoverName);
        $tmp_name = $_FILES["book-cover"]["tmp_name"];

        // VALIDATION
        $ext = pathinfo($_FILES['book-cover']['name'], PATHINFO_EXTENSION);
        if (!(in_array($ext, array("png", "jpg", "jpeg")))) {
            $_SESSION["add-book-error-img"] = "The image you uploaded must be in jpg, jpeg or png format! Please reupload a new one.";
            $all_ok = false;
        } else {
            // validate image size. Size is calculated in Bytes
            if ($_FILES['book-cover']['size'] > 1000000) {
                $_SESSION["add-book-error-img"] = "The file (image) you uploaded is too large. Upload other ones with under 1MB file size. Please reupload a new one.";
                $all_ok = false;
            } else {
                $got_img = true;
            }
        }
    } else {
        $got_img = false;
    }

    // check for isbn
    $isbn = $_POST["isbn"];
    if (empty($isbn)) {
        $_SESSION["add-book-error-isbn"] = "ISBN of the book is required.";
        $all_ok = false;
    } else {
        if (invalidISBN($isbn)) {
            $_SESSION["add-book-error-isbn"] = "The ISBN entered is invalid.";
            $all_ok = false;
        } else {
            if (isbnExists($conn, $isbn)) {
                $_SESSION["add-book-error-isbn"] = "There's a book with the same ISBN number already. Kindly go to the edit books page to add the quantity of that book instead of adding a new record here.";
                $all_ok = false;
            } else {
                $_SESSION["temp-isbn"] = $isbn;
            }
        }
    }

    // check for quantity
    $quantity = $_POST["quantity"];
    if (empty($quantity)) {
        $_SESSION["add-book-error-quantity"] = "Quantity of the book is required.";
        $all_ok = false;
    } else {
        if (invalidQuantity($quantity)) {
            $_SESSION["add-book-error-quantity"] = "The quantity entered is invalid.";
            $all_ok = false;
        } else {
            $_SESSION["temp-quantity"] = $quantity;
        }
    }

    // check for title
    $title = $_POST["title"];
    if (empty($title)) {
        $_SESSION["add-book-error-title"] = "Title of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-title"] = $title;
    }

    // check for author
    $author = $_POST["author"];
    if (empty($author)) {
        $_SESSION["add-book-error-author"] = "Author of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-author"] = $author;
    }

    // there's no need to check for descr
    $descr = $_POST["description"];
    $_SESSION["temp-descr"] = $descr;

    // check for category
    $category = $_POST["category"];
    if (empty($category)) {
        $_SESSION["add-book-error-category"] = "Category of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-category"] = $category;
    }

    // check for publisher
    $publisher = $_POST["publisher"];
    if (empty($publisher)) {
        $_SESSION["add-book-error-publisher"] = "Publisher of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-publisher"] = $publisher;
    }

    // check for lang
    $language = $_POST["language"];
    if (empty($language)) {
        $_SESSION["add-book-error-language"] = "Language of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-language"] = $language;
    }

    // check for price
    $price = str_replace(str_split("$,"), "", $_POST["currency-field"]);
    $price = empty($price) ? '00.00' : $price;
    if (invalidPrice($price)) {
        $_SESSION["add-book-error-price"] = "The price entered is invalid.";
        $all_ok = false;
    } else {
        if ($price != "00.00") {
            $_SESSION["temp-price"] = "$" . $price;
        }
    }

    // check for year
    $year = $_POST["year"];
    if (empty($year)) {
        $_SESSION["add-book-error-year"] = "Publication year of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-year"] = $year;
    }

    // check for pages
    $pages = $_POST["pages"];
    if (empty($pages)) {
        $_SESSION["add-book-error-pages"] = "Number of pages of the book is required.";
        $all_ok = false;
    } else {
        if (invalidPages($pages)) {
            $_SESSION["add-book-error-pages"] = "The number of pages entered is invalid.";
            $all_ok = false;
        } else {
            $_SESSION["temp-pages"] = $pages;
        }
    }

    // check for shelf
    $shelf = $_POST["shelf"];
    if (empty($shelf)) {
        $_SESSION["add-book-error-shelf"] = "Shelf name of the book is required.";
        $all_ok = false;
    } else {
        $_SESSION["temp-shelf"] = $shelf;
    }

    // check all okay? if okay, proceed. if not, redirect back to add_books.php
    if (!$all_ok) {
        if ($got_img) {
            $_SESSION["add-book-error-img"] = "Your previous book cover image has no problem. However, due to some of your input fields below may have invalid values, please reupload the book cover image.<br>Our team is trying very hard to get rid of this inconvenience ASAP.";
        }
        header("location: ../add_books.php");
        exit();
    } else {
        // first save the image to uploads/book-covers directory
        if (!empty($_FILES["book-cover"]["name"])) {
            if (!move_uploaded_file($tmp_name, $target_file)) {
                $_SESSION["add-book-error-img"] = "Something went wrong went uploading the image to our server.";   // in case got error...
                header("location: ../add_books.php");
                exit();
            }
        }

        $sql = "INSERT INTO books (booksISBN, booksQuantity, booksTitle, booksAuthor, booksCoverImage, booksDescription, booksCategory, booksPublisher, booksLanguage, booksPrice, booksYear, booksPages, booksShelf) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION["add-book-error-stmt"] = "Sorry, something went wrong.";
            header("location: ../add_books.php");
            exit();
        }

        $fileName = empty($_FILES["book-cover"]["name"]) ? "default-book-cover.png" : $bookCoverName;

        mysqli_stmt_bind_param($stmt, "sssssssssssss", $isbn, $quantity, $title, $author, $fileName, $descr, $category, $publisher, $language, $price, $year, $pages, $shelf);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

    }

    $_SESSION["add-book-success-msg"] = "Book titled '$title' is added successfully. <a href='book.php?id=$isbn' class='alert-link'>View more</a>";

    header("location: ../add_books.php");
    exit();

} else {
    header("location: ../add_books.php");
}
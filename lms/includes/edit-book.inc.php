<?php

session_start();

include_once 'dbh.inc.php';
include_once 'functions.inc.php';

$bookURL = $_POST["book-url"];    // get url from header to see which user is it

$target = substr($bookURL, 3);

// fetch old details
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$target';"));


if (isset($_POST['btnSubmit'])) {   // one of the buttons in the three forms is clicked

    if (isset($_POST["changeBookCover"])) {

        if (!empty($_FILES["book-cover"]["name"])) {
            $bookCoverName = time() . '-' . $_FILES["book-cover"]["name"];
            // For image upload
            $target_dir = "../uploads/book-covers/";
            $target_file = $target_dir . basename($bookCoverName);
            $tmp_name = $_FILES["book-cover"]["tmp_name"];

            // VALIDATION
            $ext = pathinfo($_FILES['book-cover']['name'], PATHINFO_EXTENSION);
            if (!(in_array($ext, array("png", "jpg", "jpeg")))) {
                $_SESSION["edit-book-error-img"] = "The image you uploaded must be in jpg, jpeg or png format! Please reupload a new one.";
            } else {
                // validate image size. Size is calculated in Bytes
                if ($_FILES['book-cover']['size'] > 1000000) {
                    $_SESSION["edit-book-error-img"] = "The file (image) you uploaded is too large. Upload other ones with under 1MB file size. Please reupload a new one.";
                } else {
                    // image valid already, change it

                    // first save the image to uploads/book-covers directory
                    if (!empty($_FILES["book-cover"]["name"])) {
                        if (!move_uploaded_file($tmp_name, $target_file)) {
                            $_SESSION["edit-book-error-img"] = "Something went wrong went uploading the image to our server.";   // in case got error...
                        } else {
                            // then delete the previous image its name stored in the db
                            unlink("../uploads/book-covers/" . $book["booksCoverImage"]);

                            // then update the file name stored in the database
                            $sql = "UPDATE books SET booksCoverImage = '" . $bookCoverName . "' WHERE booksISBN = '$target'";
                            mysqli_query($conn, $sql);

                            $_SESSION["edit-book-success-msg"] = "Book cover image is updated.";
                        }
                    }
                }
            }
        }

    } else if (isset($_POST["changeISBN"])) {

        // check for isbn
        $isbn = $_POST["isbn"];
        if ($isbn != $book["booksISBN"]) {
            if (empty($isbn)) {
                $_SESSION["edit-book-error-isbn"] = "ISBN of the book is required.";
            } else {
                if (invalidISBN($isbn)) {
                    $_SESSION["edit-book-error-isbn"] = "The ISBN entered is invalid.";
                } else {
                    if (isbnExists($conn, $isbn)) {
                        $_SESSION["edit-book-error-isbn"] = "There's a book with the same ISBN number already.";
                    } else {
                        $sql = "UPDATE books SET booksISBN = ? WHERE booksISBN = '$target'";
                        $stmt = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                        }
    
                        mysqli_stmt_bind_param($stmt, "s", $isbn);
                        mysqli_stmt_execute($stmt);
    
                        mysqli_stmt_close($stmt);
    
                        $target = $isbn;

                        $_SESSION["edit-book-success-msg"] = "ISBN is updated.";
                    }
                }
            }
        }

    } else if (isset($_POST["changeQuantity"])) {
        
        // check for quantity
        $quantity = $_POST["quantity"];
        if ($quantity != $book["booksQuantity"]) {
            if (empty($quantity)) {
                $_SESSION["edit-book-error-quantity"] = "Quantity of the book is required.";
            } else {
                if (invalidQuantity($quantity)) {
                    $_SESSION["edit-book-error-quantity"] = "The quantity entered is invalid.";
                } else {
                    $sql = "UPDATE books SET booksQuantity = ? WHERE booksISBN = '$target'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                    }

                    mysqli_stmt_bind_param($stmt, "s", $quantity);
                    mysqli_stmt_execute($stmt);

                    mysqli_stmt_close($stmt);

                    $_SESSION["edit-book-success-msg"] = "Book quantity is updated.";
                }
            }
        }

    } else if (isset($_POST["changeTitle"])) {

        // check for title
        $title = $_POST["title"];
        if ($title != $book["booksTitle"]) {
            if (empty($title)) {
                $_SESSION["edit-book-error-title"] = "Title of the book is required.";
            } else {
                $sql = "UPDATE books SET booksTitle = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $title);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book title is updated.";
            }
        }

    } else if (isset($_POST["changeAuthor"])) {
        
        // check for author
        $author = $_POST["author"];
        if ($author != $book['booksAuthor']) {
            if (empty($author)) {
                $_SESSION["edit-book-error-author"] = "Author of the book is required.";
            } else {
                $sql = "UPDATE books SET booksAuthor = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $author);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book author is updated.";
            }
        }

    } else if (isset($_POST["changeDescr"])) {
        
        // there's no need to check for description
        $descr = $_POST["description"];
        $sql = "UPDATE books SET booksDescription = ? WHERE booksISBN = '$target'";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
        }

        mysqli_stmt_bind_param($stmt, "s", $descr);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        $_SESSION["edit-book-success-msg"] = "Book description is updated.";

    } else if (isset($_POST["changeCategory"])) {
        
        // check for category
        $category = $_POST["category"];
        if ($category != $book["booksCategory"]) {
            if (empty($category)) {
                $_SESSION["edit-book-error-category"] = "Category of the book is required.";
            } else {
                $sql = "UPDATE books SET booksCategory = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $category);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book category is updated.";
            }
        }

    } else if (isset($_POST["changePublisher"])) {
        
        // check for publisher
        $publisher = $_POST["publisher"];
        if ($publisher != $book["booksPublisher"]) {
            if (empty($publisher)) {
                $_SESSION["edit-book-error-publisher"] = "Publisher of the book is required.";
            } else {
                $sql = "UPDATE books SET booksPublisher = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $publisher);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book publisher is updated.";
            }
        }

    } else if (isset($_POST["changeLanguage"])) {
        
        // check for lang
        $language = $_POST["language"];
        if ($language != $book["booksLanguage"]) {
            if (empty($language)) {
                $_SESSION["edit-book-error-language"] = "Language of the book is required.";
                $all_ok = false;
            } else {
                $sql = "UPDATE books SET booksLanguage = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $language);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book language is updated.";
            }
        }

    } else if (isset($_POST["changePrice"])) {
        
        // check for price
        $price = str_replace(str_split("$,"), "", $_POST["currency-field"]);
        $price = empty($price) ? '00.00' : $price;
        if ($price != $book["booksPrice"]) {
            if (empty($price)) {
                $_SESSION["edit-book-error-price"] = "Price of the book is required.";
                $all_ok = false;
            } else {
                if (invalidPrice($price)) {
                    $_SESSION["edit-book-error-price"] = "The price entered is invalid.";
                    $all_ok = false;
                } else {
                    $sql = "UPDATE books SET booksPrice = ? WHERE booksISBN = '$target'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                    }

                    mysqli_stmt_bind_param($stmt, "s", $price);
                    mysqli_stmt_execute($stmt);

                    mysqli_stmt_close($stmt);

                    $_SESSION["edit-book-success-msg"] = "Book price is updated.";
                }
            }
        }
        
    } else if (isset($_POST["changeYear"])) {
        
        // check for year
        $year = $_POST["year"];
        if ($year != $book["booksYear"]) {
            if (empty($year)) {
                $_SESSION["edit-book-error-year"] = "Publication year of the book is required.";
            } else {
                $sql = "UPDATE books SET booksYear = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $year);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Book publication year is updated.";
            }
        }

    } else if (isset($_POST["changePages"])) {
        
        // check for pages
        $pages = $_POST["pages"];
        if ($pages != $book["booksPages"]) {
            if (empty($pages)) {
                $_SESSION["edit-book-error-pages"] = "Number of pages of the book is required.";
            } else {
                if (invalidPages($pages)) {
                    $_SESSION["edit-book-error-pages"] = "The number of pages entered is invalid.";
                } else {
                    $sql = "UPDATE books SET booksPages = ? WHERE booksISBN = '$target'";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt, $sql)) {
                        $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                    }

                    mysqli_stmt_bind_param($stmt, "s", $pages);
                    mysqli_stmt_execute($stmt);

                    mysqli_stmt_close($stmt);

                    $_SESSION["edit-book-success-msg"] = "Number of pages of book is updated.";
                }
            }
        }

    } else if (isset($_POST["changeShelf"])) {
        
        // check for shelf
        $shelf = $_POST["shelf"];
        if ($shelf != $book["booksShelf"]) {
            if (empty($shelf)) {
                $_SESSION["edit-book-error-shelf"] = "Shelf name of the book is required.";
                $all_ok = false;
            } else {
                $sql = "UPDATE books SET booksShelf = ? WHERE booksISBN = '$target'";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $_SESSION["edit-book-error-stmt"] = "Sorry, something went wrong.";
                }

                mysqli_stmt_bind_param($stmt, "s", $shelf);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

                $_SESSION["edit-book-success-msg"] = "Shelf name where the book is located is updated.";
            }
        }

    }

    header("location: ../edit_book.php?id=$target");

} else {
    header("location: ../manage_books.php");
    exit();
}
<?php
include_once 'header.php';
include_once 'includes/dbh.inc.php';

require 'vendor/autoload.php';

function invalidISBN($isbn_no) {
    $isbn = new Isbn\Isbn();
    $result;
    if ($isbn->validation->isbn($isbn_no)) {
        $result = false;
    } else {
        $result = true;
    }
    return $result;
}
?>

<?php

    // get book details
    $target = $_GET["id"];
    if (empty($target)) {
        header("location: index.php");
        exit();
    }
    if (invalidISBN($target)) {
        header("location: index.php");
        exit();
    }

    $res = mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$target';");
    $book = mysqli_fetch_all($res, MYSQLI_ASSOC)[0];
?>

<link rel="stylesheet" href="stylesheets/book.css">
<link rel="stylesheet" href="stylesheets/alert.css">

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="main-content">
        <div class="content-wrapper">
            <h2><?php echo $book["booksTitle"]?></h2>
            <!-- notices -->
            <?php
                if (isset($_SESSION["borrow-book-success-msg"])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION["borrow-book-success-msg"] . '</div>';
                }
                unset($_SESSION['borrow-book-success-msg']);
            ?>

            <div class="book-cover-container">
                <img class="book-cover" src="<?php if ($book["booksCoverImage"] == "default-book-cover.png") {echo "images/default-book-cover.png";} else {echo "uploads/book-covers/" . $book["booksCoverImage"];}?>" alt="book cover">
            </div>
                
            <div class="data">
                <b>ISBN: </b> <?php echo $book["booksISBN"]?>
            </div>
            <div class="data">
                <b>Author: </b> <?php echo $book["booksAuthor"]?>
            </div>
            <div class="data">
                <b>Description: </b> <br> <?php echo $book["booksDescription"]?>
            </div>
            <div class="data">
                <b>Category: </b> <?php echo $book["booksCategory"]?>
            </div>
            <div class="data">
                <b>Publisher: </b> <?php echo $book["booksPublisher"]?>
            </div>
            <div class="data">
                <b>Language: </b> <?php echo $book["booksLanguage"]?>
            </div>
            <div class="data">
                <b>Price: </b> <?php echo $book["booksPrice"]?>
            </div>
            <div class="data">
                <b>Year: </b> <?php echo $book["booksYear"]?>
            </div>

            <div class="data">
                <b>Pages: </b> <?php echo $book["booksPages"]?>
            </div>
            <div class="data">
                <b>Shelf: </b> <?php echo $book["booksShelf"]?>
            </div>

            <br><br>

            <!-- check for borrow button availability -->
            <?php
            
                if (!isset($_SESSION["userid"])) {
                    echo "Uh-oh, you are not logged in! Log in to borrow the book!<br>";
                    echo "<a class='styled-button' href='login.php'>Log in now</a><br>";
                    echo "Don't have an account? <a class='styled-button' href='signup.php'>Sign up now</a>";
                    exit;
                } else {

                    $maxQuota = 3;

                    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$target';"));
                    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersid = '" . $_SESSION["userid"] . "';"));

                    if (((int)$book["booksQuantity"] - (int)$book["booksLoaned"] - (int)$book["booksPending"] > 0)) {      // to check if book available, that means still got copies of book remained
                        if ($user["usersType"] == "admin" or (int)$user["bookQuota"] < $maxQuota) {
                            echo '<a class="styled-button" href="includes/borrow-book.inc.php?id=' . $target . '">Borrow this book</a><br>';
                        } else {
                            echo '<div class="alert alert-warning" role="alert">You already reached the max quota of books that you can borrow. If you wish to borrow this book, kindly return some books at <a class="styled-button" href="profile.php" class="alert-link">your profile</a> first.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-warning" role="alert">This book is out of stock. Please contact the admin if you think this is a mistake.</div>';
                    }
                }
                echo 'Want to return this book? Return it <a class="styled-button" href="profile.php#">here</a> at your profile.';
            ?>
        </div>
    </div>
</div>

<div id="disqus_thread"></div>
<script>
    /**
    *  RECOMMENDED CONFIGURATION VARIABLES: EDIT AND UNCOMMENT THE SECTION BELOW TO INSERT DYNAMIC VALUES FROM YOUR PLATFORM OR CMS.
    *  LEARN WHY DEFINING THESE VARIABLES IS IMPORTANT: https://disqus.com/admin/universalcode/#configuration-variables    */
    /*
    var disqus_config = function () {
    this.page.url = PAGE_URL;  // Replace PAGE_URL with your page's canonical URL variable
    this.page.identifier = PAGE_IDENTIFIER; // Replace PAGE_IDENTIFIER with your page's unique identifier variable
    };
    */
    (function() { // DON'T EDIT BELOW THIS LINE
    var d = document, s = d.createElement('script');
    s.src = 'https://pathfinderlibms.disqus.com/embed.js';
    s.setAttribute('data-timestamp', +new Date());
    (d.head || d.body).appendChild(s);
    })();
</script>
<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>


<?php include_once 'footer.php';?>
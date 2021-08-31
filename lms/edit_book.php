<?php include_once 'header.php';?>
<?php include_once 'includes/dbh.inc.php';

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


<link rel="stylesheet" href="stylesheets/alert.css">
<link rel="stylesheet" href="stylesheets/add_books.css">
<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="main-content">
        <?php
            if (isset($_SESSION["userid"])) {
                $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
                if ($type != "admin") {
                    header("location: manage_books.php");
                    exit();
                }

                // admin access: ok
                if (isset($_GET["id"])) {

                    $targetISBN = $_GET["id"];

                    if (invalidISBN($targetISBN)) {
                        header("location: manage_books.php");
                        exit();
                    } else {
                        // admin access: ok, link with isbn id: ok
                        ?>

                        <!-- retrieve book current data -->
                        <?php
                            $res = mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$targetISBN';");
                            $book = mysqli_fetch_all($res, MYSQLI_ASSOC)[0];
                        ?>

                        <div class="page-wrapper">
                            <div class="main-content">
                                <!-- notices -->
                                <?php
                                    if (isset($_SESSION["edit-book-success-msg"])) {
                                        echo '<div class="alert alert-success" role="alert">' . $_SESSION["edit-book-success-msg"] . '</div>';
                                        unset($_SESSION['edit-book-success-msg']);
                                    }
                                ?>

                                <h1 class="title">Edit book: <?php echo $book["booksTitle"];?> - <?php echo $targetISBN;?></h1>

                                <!-- edit image -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST" enctype="multipart/form-data">

                                    <h2 class="text-center">Upload book cover</h2>
                                    <div class="form-group text-center">
                                        <span class="img-div">
                                        <div class="text-center img-placeholder" onClick="triggerClick()">
                                            <h4>Upload book cover (file size must not exceed 1MB)</h4>
                                        </div>

                                        <?php
                                            if (isset($_SESSION["edit-book-error-img"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-img"] . '</div>';
                                                unset($_SESSION['edit-book-error-img']);
                                            }
                                        ?>

                                        <img src="<?php if ($book['booksCoverImage'] == "default-book-cover.png") {echo "images/default-book-cover.png";} else {echo "uploads/book-covers/" . $book["booksCoverImage"];}?>" onClick="triggerClick()" id="book-cover-display" width="60%">
                                        </span>
                                        <input type="file" name="book-cover" onChange="displayImage(this)" id="book-cover" class="form-control" style="display: none;" accept=".jpg,.jpeg,.png">
                                        <label>Book Cover</label>

                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeBookCover">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Book Cover</button>
                                        or <a href="includes/reset-book-cover.inc.php?target=<?php echo $targetISBN;?>" onclick="return confirm('Are you sure you want to reset this book\'s image to the default one? ')">reset to default book cover</a>
                                    </div>
                                </form>

                                <!-- edit isbn -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="isbn">ISBN Number: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-isbn"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-isbn"] . '</div>';
                                                unset($_SESSION['edit-book-error-isbn']);
                                            }
                                        ?>
                                        <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN number" value="<?php echo $book['booksISBN'];?>">
                                        <small id="rsp-isbn"></small>

                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeISBN">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change ISBN</button>
                                    </div>
                                </form>

                                <!-- edit quantity -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="quantitiy">Quantity: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-quantity"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-quantity"] . '</div>';
                                                unset($_SESSION['edit-book-error-quantity']);
                                            }
                                            
                                        ?>
                                        <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" value="<?php echo $book['booksQuantity'];?>">
                                        <small id="rsp-quantity"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeQuantity">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Quantity</button>
                                    </div>
                                </form>

                                <!-- edit title -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="title">Title: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-title"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-title"] . '</div>';
                                                unset($_SESSION['edit-book-error-title']);
                                            }
                                            
                                        ?>
                                        <input type="text" id="title" name="title" placeholder="Enter title" value="<?php echo $book['booksTitle'];?>">
                                        <small id="rsp-title"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeTitle">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Title</button>
                                    </div>
                                </form>

                                <!-- edit author -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="author">Author: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-author"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-author"] . '</div>';
                                                unset($_SESSION['edit-book-error-author']);
                                            }
                                            
                                        ?>
                                        <input type="text" id="author" name="author" placeholder="Enter author" value="<?php echo $book['booksAuthor'];?>">
                                        <small id="rsp-author"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeAuthor">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Author</button>
                                    </div>
                                </form>

                                <!-- edit description -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field area">
                                        <label for="description">Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" style="resize: none;"><?php echo $book['booksDescription'];?></textarea>
                                        <small id="rsp-description"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeDescr">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Description</button>
                                    </div>
                                </form>

                                <!-- edit category -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="category">Category: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-category"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-category"] . '</div>';
                                                unset($_SESSION['edit-book-error-category']);
                                            }
                                            
                                        ?>
                                        <input list="category-list" id="category" name="category" placeholder="Enter category"  value="<?php echo $book['booksCategory'];?>">
                                        <datalist id="category-list">
                                            <option value="History"></option>
                                            <option value="Comics"></option>
                                            <option value="Fiction"></option>
                                            <option value="Non-Fiction"></option>
                                            <option value="Biography"></option>
                                            <option value="Medical"></option>
                                            <option value="Fantasy"></option>
                                            <option value="Education"></option>
                                            <option value="Sports"></option>
                                            <option value="Technology"></option>
                                            <option value="Literature"></option>
                                        </datalist>
                                        <small id="rsp-category"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeCategory">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Category</button>
                                    </div>
                                </form>

                                <!-- edit publisher -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="publisher">Publisher: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-publisher"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-publisher"] . '</div>';
                                                unset($_SESSION['edit-book-error-publisher']);
                                            }
                                            
                                        ?>
                                        <input type="text" id="publisher" name="publisher" placeholder="Enter publisher" value="<?php echo $book['booksPublisher'];?>">
                                        <small id="rsp-publisher"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changePublisher">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Publisher</button>
                                    </div>
                                </form>

                                <!-- edit language -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="language">Language: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-language"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-language"] . '</div>';
                                                unset($_SESSION['edit-book-error-language']);
                                            }
                                            
                                        ?>
                                        <input list="language-list" name="language" value="<?php echo $book['booksLanguage'];?>" placeholder="Enter language">
                                        <datalist id="language-list">
                                            <option value="English">
                                            <option value="Mandarin Chinese">
                                            <option value="Hindi">
                                            <option value="Spanish">
                                            <option value="Standard Arabic">
                                            <option value="Bengali">
                                            <option value="French">
                                            <option value="Russian">
                                            <option value="Portuguese">
                                            <option value="Urdu">
                                            <option value="Indonesian">
                                            <option value="Standard German">
                                            <option value="Japanese">
                                            <option value="Marathi">
                                            <option value="Telugu">
                                            <option value="Turkish">
                                            <option value="Tamil">
                                            <option value="Korean">
                                        </datalist>
                                        <small id="rsp-language"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeLanguage">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Language</button>
                                    </div>
                                </form>

                                <!-- edit price -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="price">Price: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-price"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-price"] . '</div>';
                                                unset($_SESSION['edit-book-error-price']);
                                            }
                                            
                                        ?>
                                        <input type="text" name="currency-field" id="currency-field" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" placeholder="$00.00" value="$<?php echo $book['booksPrice'];?>">
                                        <small id="rsp-currency-field"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changePrice">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Price</button>
                                    </div>
                                </form>

                                <!-- edit year -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="year">Year: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-year"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-year"] . '</div>';
                                                unset($_SESSION['edit-book-error-year']);
                                            }
                                            
                                        ?>
                                        <select name="year" id="year"></select>
                                        <small id="rsp-year"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeYear">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Publication Year</button>
                                    </div>
                                </form>

                                <!-- edit pages -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="pages">Pages: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-pages"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-pages"] . '</div>';
                                                unset($_SESSION['edit-book-error-pages']);
                                            }
                                            
                                        ?>
                                        <input type="number" id="pages" name="pages" placeholder="Enter pages" value="<?php echo $book['booksPages'];?>">
                                        <small id="rsp-pages"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changePages">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Number of Pages</button>
                                    </div>
                                </form>

                                <!-- edit shelf -->
                                <form class="book-info" action="includes/edit-book.inc.php" method="POST">
                                    <div class="form-field">
                                        <label for="shelf">Shelf: </label>
                                        <?php
                                            if (isset($_SESSION["edit-book-error-shelf"])) {
                                                echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-book-error-shelf"] . '</div>';
                                                unset($_SESSION['edit-book-error-shelf']);
                                            }
                                            
                                        ?>
                                        <input type="text" id="shelf" name="shelf" placeholder="Enter shelf" value="<?php echo $book['booksShelf'];?>">
                                        <small id="rsp-shelf"></small>
                                        <input type="hidden" name="book-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">
                                        <input type="hidden" name="changeShelf">
                                        <button type="submit" id="btnSubmit" name="btnSubmit">Change Shelf</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php
                    }
                } else {
                    header("location: manage_books.php");
                    exit();
                }
            } else {
                header("location: manage_books.php");
                exit();
            }
        ?>
    </div>
</div>
    

<script>
    function triggerClick(e) {
        document.getElementById('book-cover').click();
    }
    function displayImage(e) {
        if (e.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e){
                document.querySelector('#book-cover-display').setAttribute('src', e.target.result);
            }
            reader.readAsDataURL(e.files[0]);
        }
    }
    document.getElementById('book-cover-display').addEventListener("error", function() {
        this.src = "uploads/book-covers/<?php echo $book['booksCoverImage']; ?>";
    });
</script>

<script>
    const start = 1900;
    const end = new Date().getFullYear();
    let options = "";
    for(let year = start ; year <=end; year++){
        options += "<option>"+ year +"</option>";
    }
    document.getElementById("year").innerHTML = options;
    document.getElementById("year").value = "<?php echo $book['booksYear'];?>";
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="scripts/price_input.js"></script>

<?php include_once 'footer.php';?>
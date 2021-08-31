<?php include_once 'header.php';?>

    <?php
        include_once 'includes/dbh.inc.php';
        if (isset($_SESSION["userid"])) {
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"));
            if (!($user["usersType"] == "admin")) {
                header("location: ./index.php");
                exit;
            }
        } else {
            header("location: ./index.php");
            exit;
        }
    ?>

    <link rel="stylesheet" href="stylesheets/add_books.css">
    <link rel="stylesheet" href="stylesheets/alert.css">
    <div class="page-wrapper">
        <?php include_once 'sidebar.php'?>
        <div class="main-content">
            <!-- notices -->
            <?php
                if (isset($_SESSION["add-book-success-msg"])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION["add-book-success-msg"] . '</div>';
                    
                    unset($_SESSION['add-book-success-msg']);
                }
            ?>

            <h1 class="title">Add Book</h1>

            <form class="book-info" action="includes/add-books.inc.php" method="POST" enctype="multipart/form-data">
                <h2 class="text-center">Upload book cover</h2>
                <div class="form-field text-center">
                    <span class="img-div">
                    <div class="text-center img-placeholder"  onClick="triggerClick()">
                        <h4>Upload book cover (file size must not exceed 1MB)</h4>
                    </div>

                    <?php
                        if (isset($_SESSION["add-book-error-img"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-img"] . '</div>';
                            unset($_SESSION['add-book-error-img']);
                        }
                    ?>

                    <img src="images/default-book-cover.png" onClick="triggerClick()" id="book-cover-display" width="60%">
                    </span>
                    <input type="file" name="book-cover" onChange="displayImage(this)" id="book-cover" class="form-control" style="display: none;" accept=".jpg,.jpeg,.png">
                    <label>Book Cover</label>
                </div>

                <div class="form-field">
                    <label for="isbn">ISBN Number: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-isbn"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-isbn"] . '</div>';
                            unset($_SESSION['add-book-error-isbn']);
                        }
                    ?>
                    <input type="text" id="isbn" name="isbn" placeholder="Enter ISBN number" value="<?php echo !isset($_SESSION["add-book-error-isbn"]) ? (isset($_SESSION["temp-isbn"]) ? $_SESSION["temp-isbn"] : '') : ''; ?>">
                    <small id="rsp-isbn"></small>
                </div>
                <div class="form-field">
                    <label for="quantitiy">Quantity: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-quantity"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-quantity"] . '</div>';
                            unset($_SESSION['add-book-error-quantity']);
                        }
                        
                    ?>
                    <input type="number" id="quantity" name="quantity" placeholder="Enter quantity" value="<?php echo !isset($_SESSION["add-book-error-quantity"]) ? (isset($_SESSION["temp-quantity"]) ? $_SESSION["temp-quantity"] : '') : ''; ?>">
                    <small id="rsp-quantity"></small>
                </div>
                <div class="form-field">
                    <label for="title">Title: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-title"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-title"] . '</div>';
                            unset($_SESSION['add-book-error-title']);
                        }
                        
                    ?>
                    <input type="text" id="title" name="title" placeholder="Enter title" value="<?php echo !isset($_SESSION["add-book-error-title"]) ? (isset($_SESSION["temp-title"]) ? $_SESSION["temp-title"] : '') : ''; ?>">
                    <small id="rsp-title"></small>
                </div>
                <div class="form-field">
                    <label for="author">Author: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-author"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-author"] . '</div>';
                            unset($_SESSION['add-book-error-author']);
                        }
                        
                    ?>
                    <input type="text" id="author" name="author" placeholder="Enter author" value="<?php echo !isset($_SESSION["add-book-error-author"]) ? (isset($_SESSION["temp-author"]) ? $_SESSION["temp-author"] : '') : ''; ?>">
                    <small id="rsp-author"></small>
                </div>
                <div class="form-field area">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" cols="30" rows="3" style="resize: none;"><?php echo isset($_SESSION["temp-descr"]) ? $_SESSION["temp-descr"] : ''; ?></textarea>
                    <small id="rsp-description"></small>
                </div>
                <div class="form-field">
                    <label for="category">Category: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-category"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-category"] . '</div>';
                            unset($_SESSION['add-book-error-category']);
                        }
                        
                    ?>
                    <input list="category-list" id="category" name="category" placeholder="Enter category" value="<?php echo !isset($_SESSION["add-book-error-isbn"]) ? (isset($_SESSION["temp-category"]) ? $_SESSION["temp-category"] : '') : ''; ?>">
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
                </div>
                <div class="form-field">
                    <label for="publisher">Publisher: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-publisher"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-publisher"] . '</div>';
                            unset($_SESSION['add-book-error-publisher']);
                        }
                        
                    ?>
                    <input type="text" id="publisher" name="publisher" placeholder="Enter publisher" value="<?php echo !isset($_SESSION["add-book-error-isbn"]) ? (isset($_SESSION["temp-publisher"]) ? $_SESSION["temp-publisher"] : '') : ''; ?>">
                    <small id="rsp-publisher"></small>
                </div>
                <div class="form-field">
                    <label for="language">Language: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-language"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-language"] . '</div>';
                            unset($_SESSION['add-book-error-language']);
                        }
                        
                    ?>
                    <input list="language-list" name="language" value="<?php echo !isset($_SESSION["add-book-error-language"]) ? (isset($_SESSION["temp-language"]) ? $_SESSION["temp-language"] : '') : ''; ?>" placeholder="Enter language">
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
                </div>
                <div class="form-field">
                    <label for="price">Price: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-price"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-price"] . '</div>';
                            unset($_SESSION['add-book-error-price']);
                        }
                        
                    ?>
                    <input type="text" name="currency-field" id="currency-field" pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$" data-type="currency" placeholder="$00.00" value="<?php echo !isset($_SESSION["add-book-error-price"]) ? (isset($_SESSION["temp-price"]) ? $_SESSION["temp-price"] : '') : ''; ?>">
                    <small id="rsp-currency-field"></small>
                </div>
                <div class="form-field">
                    <label for="year">Year: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-year"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-year"] . '</div>';
                            unset($_SESSION['add-book-error-year']);
                        }
                        
                    ?>
                    <select name="year" id="year"></select>
                    <small id="rsp-year"></small>
                </div>
                <div class="form-field">
                    <label for="pages">Pages: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-pages"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-pages"] . '</div>';
                            unset($_SESSION['add-book-error-pages']);
                        }
                        
                    ?>
                    <input type="number" id="pages" name="pages" placeholder="Enter pages" value="<?php echo !isset($_SESSION["add-book-error-pages"]) ? (isset($_SESSION["temp-pages"]) ? $_SESSION["temp-pages"] : '') : ''; ?>">
                    <small id="rsp-pages"></small>
                </div>
                <div class="form-field">
                    <label for="shelf">Shelf: </label>
                    <?php
                        if (isset($_SESSION["add-book-error-shelf"])) {
                            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["add-book-error-shelf"] . '</div>';
                            unset($_SESSION['add-book-error-shelf']);
                        }
                        
                    ?>
                    <input type="text" id="shelf" name="shelf" placeholder="Enter shelf" value="<?php echo !isset($_SESSION["add-book-error-shelf"]) ? (isset($_SESSION["temp-shelf"]) ? $_SESSION["temp-shelf"] : '') : ''; ?>">
                    <small id="rsp-shelf"></small>
                </div>
                <button type="submit" name="add-book">Add book</button>
            </form>
        </div>
    </div>
    
    <script>
        function triggerClick(e) {
            document.querySelector('#book-cover').click();
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
            this.src = "images/default-book-cover.png";
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
        document.getElementById("year").value = "<?php echo !isset($_SESSION["add-book-error-year"]) ? (isset($_SESSION["temp-year"]) ? $_SESSION["temp-year"] : '2021') : ''; ?>";
    </script>
    <script>
        window.onload = function() {
          document.getElementById("currency-field").focus();
          document.getElementById("currency-field").blur();
        };
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="scripts/price_input.js"></script>

    <?php
        unset($_SESSION["temp-isbn"]);
        unset($_SESSION["temp-quantity"]);
        unset($_SESSION["temp-title"]);
        unset($_SESSION["temp-author"]);
        unset($_SESSION["temp-descr"]);
        unset($_SESSION["temp-category"]);
        unset($_SESSION["temp-publisher"]);
        unset($_SESSION["temp-language"]);
        unset($_SESSION["temp-price"]);
        unset($_SESSION["temp-year"]);
        unset($_SESSION["temp-pages"]);
        unset($_SESSION["temp-shelf"]);
    ?>

<?php include_once 'footer.php'?>

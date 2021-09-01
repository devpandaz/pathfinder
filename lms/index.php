<?php
include_once 'header.php';
include_once 'includes/dbh.inc.php';
?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" integrity="sha512-17EgCFERpgZKcm0j0fEq1YCJuyAWdz9KUtv1EjVuaOz8pDnh/0nZxmU6BBXwaaxqoi9PQXnRWqlcDB027hgv9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" integrity="sha512-yHknP1/AwR+yx26cB1y0cjvQUMvEa2PFzt1c9LlS4pRQ5NOTZFWbhBig+X9G9eYW/8m0/4OXNx8pxJ6z57x0dw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <div class="page-wrapper">
        <?php include_once 'sidebar.php'?>

    <?php
        if (isset($_SESSION["userid"])) {
            $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];

            echo '<h1 class="welcome">Welcome back, ' . $_SESSION["userusername"] . '</h1>';

            if (!isset($_SESSION["reminderPushed"])) {

                $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "';"));

                // list of books borrowed
                $borrowedBooks = array_slice(explode(",", $user["borrowedBooks"]), 1);

                if (!empty($borrowedBooks)) {
                    foreach ($borrowedBooks as $borrowedBook) {
                        $notification = "";
                        $tmp = explode(">", $borrowedBook);
                        $isbn = $tmp[0];
                        $expirydate = $tmp[1];

                        $diff = (strtotime($expirydate) - strtotime('today')) / 24 / 60 / 60;

                        if ($diff < 0) {
                            $status = "late";
                        } else if ($diff == 0) {
                            $status = "today";
                        } else if ($diff == 1) {
                            $status = "tomorrow";
                        }

                        $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$isbn';"));
                        $title = $book["booksTitle"];

                        if (isset($status)) {
                            if ($status == "late") {
                                $notification = "$title ($isbn) has already reached the deadline. Please return it at your profile. ";
                            } elseif ($status == "today" || $status == "tomorrow") {
                                $notification = "$title ($isbn) has is due by $status. Remember to return it.";
                            }
                            mysqli_query($conn, "INSERT INTO `notification` (userId, `text`) VALUES (" . $_SESSION["userid"] . ", '$notification');");
                        }

                        unset($status);
                    }
                }

                $_SESSION["reminderPushed"] = true;
            }
        }
    ?>

        <link rel="stylesheet" href="stylesheets/homepage.css" />
        <div class="main-content">
            <section class="content search-box">
                <img class="search-form-background" src="images/map.jpeg" alt>
                <form id="search-form" action="books.php">
                    <div class="form-field">
                        <input type="text" name="searchValue" id="searchValue" placeholder="Enter your search here...">
                        <small class="respond" id="rsp_searchValue"></small>
                    </div> 
                    <button type="submit" class="search-submit"><i class="fas fa-search"></i></button><br>
                </form>
            </section>
            <section class="content events">
                <div class="content-wrapper">
                    <section class="recent-news">
                        <div class="news__title">
                            <h2 class="news-title">Recent News</h2>
                            <div class="news-title-page">
                                <a href="/all-news">See Full list</a>
                            </div>
                        </div>
                        <div class="news__content">
                            <div class="main__content">
                                <img src="images/demo.jpg" alt="">
                                <div class="description">
                                    <h3>Homecoming of NILAM</h3>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. In molestias dolore rem nemo repudiandae deleniti natus, unde perspiciatis eum, ab fugit nisi laborum placeat soluta quasi id doloribus impedit odio.</p>
                                    <a href="">For more information</a>
                                </div>
                            </div>
                            <div class="sub__content">
                                <div>
                                    <img src="images/demo.jpg" alt="">
                                    <div class="description">
                                        <h3>Quiz Competition</h3>
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                        <a href="">For more information</a>
                                    </div>
                                </div>
                                <div>
                                    <img src="images/demo.jpg" alt="">
                                    <div class="description">
                                        <h3>Quiz Competition</h3>
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                        <a href="">For more information</a>
                                    </div>
                                </div>
                                <div>    
                                    <img src="images/demo.jpg" alt=""">
                                    <div class="description">
                                        <h3>Quiz Competition</h3>
                                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                        <a href="">For more information</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="upcoming-events">
                        <div class="events__title">
                            <h2 class="events-title">Upcoming news events</h2>
                            <div class="events-title-page">
                                <a href="/all-events">See Full list</a>
                            </div>
                        </div>
                        
                        <div class="events__content">
                            <div>
                                <h4><a href="">Nation Independence Day</a></h4>
                                <span>31/8/2021</span>
                                <a href="">Details</a>
                            </div>
                            <div>
                                <h4><a href="">Speech By Uni Prof</a></h4>
                                <span>25/8/2021</span>
                                <a href="">Details</a>
                            </div>
                            <div>
                                <h4><a href="">Event</a></h4>
                                <span>Date</span>
                                <a href="">Details</a>
                            </div>
                        </div>
                    </section>
            </section>

            


            <section class="content recent-books">
                <div class="title-description">
                    <h2>Books Release</h2>
                    <p>
                        Find and explore your new favourite books handpicked by our staff. Our collection is wide and we add on to it very regularly.
                    </p>
                </div>

                <div class="carousel">
                    <div class="carousel-wrapper">
                        <div class="slider">
                        <?php
                            $sql = "SELECT booksCoverImage FROM books WHERE booksCoverImage != 'default-book-cover.png' ORDER BY dateAdded LIMIT 10;";
                            
                            if($result = mysqli_query($conn, $sql)) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $image = $row["booksCoverImage"];
                                    echo "<div class='slide-item'>
                                        <div class='image-container'>
                                        <img src='uploads/book-covers/$image' alt=''>
                                        </div>
                                    </div>";
                                }
                            }
                        ?>
                            
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js" integrity="sha512-HGOnQO9+SP1V92SrtZfjqxxtLmVzqZpjFFekvzZVWoiASSQgSr4cw9Kqd2+l8Llp4Gm0G8GIFJ4ddwZilcdb8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $(".slider").slick({
            autoplay: true,
            autoplaySpeed: 4000,
            slidesToShow: 1,
            speed: 500,
            dots: true,
            prevArrow: '<i class="fas fa-angle-left prev-arrow"></i>',
            nextArrow: '<i class="fas fa-angle-right next-arrow"></i>'
        });
    </script>
    <script>
        const searchForm = document.getElementById("search-form");
        const errorRespond = document.getElementById("rsp_searchValue");
        searchForm.addEventListener("submit", function(event) {
            event.preventDefault();
            if(this.searchValue.value) {
                location.assign("books.php?searchValue=" + this.searchValue.value);
            } else {
                errorRespond.textContent = "A value is needed";
            }
        });
    </script>
    <div class="slogan">
        <img class="image" src="images/slogan.jpg">
        <h1>Always visit the library whenever in doubt</h1>
    </div>
    
<?php include_once 'footer.php'?>
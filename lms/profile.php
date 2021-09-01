<?php
    include_once 'header.php';
    if (!isset($_SESSION["userid"])) {
        header("location: index.php");
        exit();
    }
    include_once 'includes/dbh.inc.php';
?>

<link rel="stylesheet" type="text/css" href="stylesheets/styled_table.css">
<link rel="stylesheet" type="text/css" href="stylesheets/animation/loading.css">
<link rel="stylesheet" type="text/css" href="stylesheets/profile.css">

    <div class="page-wrapper">
        <?php include_once 'sidebar.php'?>
        <div class="main-content">
            <div class="modal-box">
                <div class="modal-content">
                </div>
            </div>
        <?php
            // initialise for later use...
            $username = $_SESSION["userusername"];
            $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"));
            $type = $user["usersType"];

            echo "<p>Welcome back $username. This is your profile. </p>";

            // upgrade or quit being a librarian...
            if (isset($_SESSION["requested"])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION["requested"] . '</div>';
                unset($_SESSION['requested']);
            }
            if (isset($_SESSION["quit"])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION["quit"] . '</div>';
                unset($_SESSION['quit']);
            }
            

            if ($type == "member") {
                echo "<a href='includes/member-to-librarian.inc.php' onclick='return confirm(&quot;Upgrade to librarian? A request will be sent to and viewed by the admin. Your request will have to be accepted before you can access librarian privileges. Before that, you can still log in as normal member.&quot;)'>Request to be librarian</a><br>";
            }

            if ($type == "librarian") {
                echo "<a href='includes/librarian-to-member.inc.php' onclick='return confirm(&quot;Are you sure you want to quit being a librarian? You will need to be approved again if you want to upgrade to librarian next time. &quot;)'>Quit being librarian</a><br>";
            }

            if ($type == "librarian, pending") {
                echo "<hr><p>Ongoing: Your request to become a librarian is yet to be approved. </p>";
            }

            // ongoing pending borrow request
            $usersBorrowPending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT borrowPending FROM users WHERE usersId = '" . $_SESSION["userid"] . "';"))["borrowPending"];
            $usersBorrowPending = array_slice(explode(",", $usersBorrowPending), 1);

            if (!empty($usersBorrowPending)) {
                echo '<p>Ongoing borrow requests: </p>
                <div class="table-wrapper">
                <table class="styled-table">
                    <thead>
                        <th>Book</th>
                        <th>ISBN</th>
                        <th>Requested at</th>
                    </thead>
                    <tbody>';
                foreach ($usersBorrowPending as $borrowPending) {
                    $tmp = explode(">", $borrowPending);
                    echo '<tr>
                    <td>' . mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '" . $tmp[0] . "'"))["booksTitle"] . '</td>
                    <td>' . $tmp[0] . '</td>
                    <td>' . str_replace("Requested at", "", $tmp[1]) . '</td>
                    </tr>
                    ';
                }
                echo '</tbody>
                </table>
                </div>
                ';
            }

            // edit profile
            echo "<a href='edit_account.php?subject=" . $_SESSION["userid"] . "'>Edit account</a><br>";

            echo "<hr>";

            // notices
            if (isset($_SESSION["return-book-success-msg"])) {
                echo '<div class="alert alert-success" role="alert">' . $_SESSION["return-book-success-msg"] . '</div>';
            }
            unset($_SESSION['return-book-success-msg']);

            // list of books borrowed
            $borrowedBooks = array_slice(explode(",", $user["borrowedBooks"]), 1);
            if (empty($borrowedBooks)) {
                echo "<h3>You haven't borrow any books yet. Go to our digital library to explore some!</h3>";
            } else {

                echo "<h3>Books that you borrowed: </h3>";
                echo "<div class='table-wrapper'>
                    <table class='styled-table'>
                        <thead>
                            <tr>
                                <th class='col_name'>Book Name</th>
                                <th class='col_isbn'>ISBN</th>
                                <th class='col_expiry'>Expiry date</th>
                                <th class='col_status'>Status</th>
                                <th class='col_command'></th>
                            </tr>
                        </thead>
                        <tbody>";
                $i = 0;
                foreach ($borrowedBooks as $borrowedBook) {
                    $tmp = explode(">", $borrowedBook);
                    $isbn = $tmp[0];
                    $expirydate = $tmp[1];

                    $diff = (strtotime($expirydate) - strtotime('today')) / 24 / 60 / 60;
                    $fine = 0;

                    if ($diff < 0) {
                        $fine = (float) (abs($diff) * 0.5);
                        $status = "Late";
                    } else if ($diff == 0) {
                        $status = "Today";
                    } else if ($diff == 1) {
                        $status = "Tomorrow";
                    } else{
                        $status = "$diff days";
                    }

                    // just to get the book title given the isbn number
                    $book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE booksISBN = '$isbn';"));

                    $title = $book["booksTitle"];
                    $image = $book["booksCoverImage"];
                    $price = $book["booksPrice"];

                    echo "<tr>
                        <td class='col_name'>$title</td>
                        <td class='col_isbn'>$isbn</td>
                        <td class='col_expiry'>$expirydate</td>
                        <td class='col_status'>$status</td>
                        <td class='col_command'>
                            <a href='includes/return-book.inc.php?index=$i&isbn=$isbn' class='return' data-title='$title' data-price='$price' data-image='$image' data-fine='$fine' data-days='$diff'>Return</a>
                        </td>
                    </tr>";
                    $i++;
                }
                echo "</tbody>
                </table>
                </div>";
            }
        ?>
        </div>
    </div>
    <script>
        const returnBook = document.getElementsByClassName("return");
        const modalBox = document.getElementsByClassName("modal-box")[0];
        const modalContent = document.getElementsByClassName("modal-content")[0];

        Array.from(returnBook).forEach(element => {
            element.addEventListener("click", function(event) {
                event.preventDefault();
                let src;
                let title = this.dataset.title;
                let fine = this.dataset.fine;
                let days = Math.abs(this.dataset.days);
                let image = this.dataset.image;
                let price = this.dataset.price;

                if (image == "default-book-cover.png") {
                    src = `images/${image}`
                } else {
                    src = `uploads/book-covers/${image}`
                }

                fine = parseInt(fine);
                let content;
                if(fine) {
                    content = `
                        <div class="book-cover-container">
                            <div class="lds-container">
                                <div class="lds-ring">
                                    <div></div><div></div><div></div><div></div>
                                </div>
                            </div>
                            <img src="${src}" class="book-cover">
                        </div>
                        <div class="right">
                            <h4>You have a fine...</h4>
                            <p style="margin: 0;">You have been late for ${days} days</p>
                            <p style="margin: 0;">Pay fine of RM${fine.toFixed(2)} due to late return of the book \"${title}\"?</p>
                            <p class="note">Note: If you lost this book, please pay RM${price}</p>
                            <button class="modal-close">Cancel</button>
                            <button class="modal-confirm">Confirm</button>
                        </div>
                    `;
                } else {
                    content = `
                        <div class="book-cover-container">
                            <div class="lds-container">
                                <div class="lds-ring">
                                    <div></div><div></div><div></div><div></div>
                                </div>
                            </div>
                            <img src="${src}" class="book-cover">
                        </div>
                        <div class="right">
                            <h4>Are you sure...</h4>
                            <p style="margin: 0;">You want to return \"${title}\"?</p>
                            <p class="note">Note: If you lost this book, please pay extra RM${price}</p>
                            <button class="modal-close">Cancel</button>
                            <button class="modal-confirm">Confirm</button>
                        </div>
                    `;
                }
                modalContent.innerHTML = content;
                modalBox.style.display = "block";
                modalBox.dataset.direct = element.href;

                document.getElementsByClassName("modal-close")[0].addEventListener("click", function() {
                    modalBox.style.display = "none";
                    modalContent.innerHTML = "";
                    modalBox.dataset.direct = "";
                });

                document.getElementsByClassName("modal-confirm")[0].addEventListener("click", function() {
                    modalBox.style.display = "none";
                    window.location.assign(modalBox.dataset.direct);
                });

                document.getElementsByClassName("book-cover")[0].addEventListener("load", function() {
                    document.getElementsByClassName("lds-container")[0].style.display = "none"
                });
            });
        });
        
        window.onclick = function(event) {
            if (event.target == modalBox) {
                modalBox.style.display = "none";
                modalContent.innerHTML = "";
                modalBox.dataset.direct = "";
            }
        }
            
        
    </script>
<?php include_once 'footer.php';?>
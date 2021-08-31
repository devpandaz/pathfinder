<?php include_once 'includes/dbh.inc.php';?>

<?php 
    if(isset($_SESSION["userid"])) {
        $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
    } else {
        $type = '';
    }
?>

<div class="app-sidebar sidebar-shadow fixed closed">
    <div class="scrollbar-wrapper">
        <div class="scrollbar-sidebar">
            <div class="app-sidebar__inner">
                <ul class="vertical-nav-menu">
                    <li class="menu-expand">
                        <a href="#">
                            <i class="fas fa-book"></i>
                            Books
                            <i class="arrow down"></i>
                        </a>
                        <ul class="collapse">
                            <li>
                                <a href="books.php" class="">
                                    Search Books
                                </a>
                            </li>
    
                            <?php 
                                if ($type == "admin") {
                                    echo '<li>
                                        <a href="add_books.php" class="">
                                            Add Books
                                        </a>
                                    </li>
                                    <li>
                                        <a href="manage_books.php" class="">
                                            Manage Books
                                        </a>
                                    </li>';
                                }
                            ?>
                        </ul>
                    </li>
                    <?php 
                        if($type == 'admin') {
                            echo '<li class="seperate">
                                <a href="admin.php" class="">
                                    <i class="fas fa-chart-line"></i>
                                    Dashboard
                                </a>
                                <a href="notification.php">
                                    <i class="fas fa-bell"></i>
                                    Send Notification
                                </a>
                            </li>
                            ';
                        }

                        if ($type == 'librarian') {
                            echo '<li class="seperate">
                                <a href="librarian.php" class="">
                                    <i class="fas fa-chart-line"></i>
                                    Dashboard
                                </a>
                            </li>
                            ';
                        }
                    ?>
                    <li class="seperate">
                        <a href="feedback/index.php">
                            <i class="fas fa-comment-dots"></i>
                            Feedback
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="scripts/DOM_header.js"></script>
<script src="perfect-scrollbar-master/dist/perfect-scrollbar.js"></script>
<script src="scripts/accordion.js"></script>
<script src="scripts/DOM_sidebar.js"></script>
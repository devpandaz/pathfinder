<?php include_once 'includes/dbh.inc.php';?>

<link rel="stylesheet" type="text/css" href="stylesheets/admin.css">

<?php include_once 'header.php'; ?>

<?php
    if (isset($_SESSION["userid"])) {
        $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
        if ($type != "librarian") {
            header("location: index.php");
            exit();
        }
    } else {
        header("location: index.php");
        exit();
    }
?>

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="main-content">

        <?php define('dashboard', TRUE); ?>
        <?php include_once 'dashboard.php';?>

    </div>
</div>

<script>
    const tableRows = document.querySelectorAll("table tbody tr");
    Array.from(tableRows).forEach(element => {
        element.addEventListener("click",  function(){
            const prevActive = element.parentElement.querySelector(".active-row");
            if(prevActive) {
                prevActive.classList.remove("active-row");
            }
            element.classList.add("active-row");
        })
    })
</script>

<?php include_once 'footer.php';?>
<?php include_once 'header.php';?>
<?php
    include_once 'includes/dbh.inc.php';

    if (isset($_SESSION["userid"])) {
        $type = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE usersId = '" . $_SESSION["userid"] . "'"))["usersType"];
        if ($type != "admin") {
            header("location: index.php");
            exit;
        }
    } else {
        header("location: index.php");
        exit();
    }
?>

<link rel="stylesheet" type="text/css" href="stylesheets/styled_table.css">
<link rel="stylesheet" type="text/css" href="stylesheets/admin.css">

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="main-content">
        <hr>
        <h1>Pending librarian sign up requests</h1>

        <?php
            $results = mysqli_query($conn, "SELECT * FROM users WHERE usersType = 'librarian, pending' ORDER BY usersUsername;");
            $pendingLibrarians = mysqli_fetch_all($results, MYSQLI_ASSOC);
        ?>

        <?php if (!empty($pendingLibrarians)): ?>
            <p>Below are users which requested to sign up as librarian: </p>
            <div class="table-wrapper">
                <table class="styled-table">
                    <thead>
                        <th>Username</th>
                        <th>Email</th>
                        <th colspan="2">Action</th>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingLibrarians as $pendingLibrarian => $details): ?>
                            <tr>
                                <td><?php echo $details["usersUsername"];?></td>
                                <td><?php echo $details["usersEmail"];?></td>
                                <td><?php echo "<a href='includes/approve_librarian_signup_request.inc.php?subject=" . $details["usersId"] . "' onclick='return confirm(&quot;Approve " . $details["usersUsername"] . " (" . $details["usersEmail"] . ")?&quot;)'>Approve</a>";?></td>
                                <td><?php echo "<a href='includes/reject_librarian_signup_request.inc.php?subject=" . $details["usersId"] . "' onclick='return confirm(&quot;Reject " . $details["usersUsername"] . " (" . $details["usersEmail"] . ")?&quot;)'>Reject</a>";?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>There are no pending librarian sign up requests currently. </p>
        <?php endif; ?>

        <hr>

        <!-- list of librarians -->
        <?php
            $results = mysqli_query($conn, "SELECT * FROM users WHERE usersType = 'librarian' ORDER BY usersUsername;");
            $librarians = mysqli_fetch_all($results, MYSQLI_ASSOC);
        ?>

        <?php if (!empty($librarians)): ?>
            <h1>List of librarians</h1>
            <div class="table-wrapper">
                <table class="styled-table">
                    <thead>
                        <th>Username</th>
                        <th>Email</th>
                        <th colspan="2">Action</th>
                    </thead>
                    <tbody>
                        <?php foreach ($librarians as $librarian => $details): ?>
                            <tr>
                                <td><?php echo $details["usersUsername"];?></td>
                                <td><?php echo $details["usersEmail"];?></td>
                                <td><?php echo "<a href='includes/remove-librarian.inc.php?subject=" . $details["usersId"] . "' onclick='return confirm(&quot;Remove librarian privileges for " . $details["usersUsername"] . " (" . $details["usersEmail"] . ")?&quot;)'>Remove librarian</a>";?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>There are no librarians currently. </p>
        <?php endif; ?>

        <hr>

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
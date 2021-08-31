<!-- this is the managing borrow requests and the list of the issued books panel for both admin and librarian in admin.php and librarian.php -->

<?php
if(!defined('dashboard')) {
   header("location: index.php");
   exit;
}
?>

<!-- notices -->
<?php
    if (isset($_SESSION["approve-borrow-success-msg"])) {
        echo '<div class="alert alert-success" role="alert">' . $_SESSION["approve-borrow-success-msg"] . '</div>';
    }
    unset($_SESSION['approve-borrow-success-msg']);
?>

<!-- list of borrow request from normal members (librarian and admin no need borrow approval)-->
<?php
    $res = mysqli_query($conn, "SELECT * FROM users WHERE borrowPending <> '';");
    $borrowRequests = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>

<?php if (!empty($borrowRequests)): ?>
    <h1>Borrow requests</h1>

    <ul style="all: unset;">
        <?php foreach ($borrowRequests as $key => $details): ?>

            <?php echo $details["usersUsername"];?>
            <?php $i = 0;?>

            <!-- table here -->
            <div class="table-wrapper">
                <table class="styled-table">
                    <thead>
                        <th>Book</th>
                        <th>ISBN</th>
                        <th>Requested at</th>
                        <th colspan="2">Action</th>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice(explode(",", $details["borrowPending"]), 1) as $borrowRequest): ?>
                            <?php $tmp = explode(">", $borrowRequest);?>
                            <tr>
                                <td><?php echo mysqli_fetch_assoc(mysqli_query($conn, 'SELECT booksTitle FROM books WHERE booksISBN = "' . $tmp[0] . '";'))["booksTitle"];?></td>
                                <td><?php echo $tmp[0];?></td>
                                <td><?php echo str_replace("Requested at", "", $tmp[1]);?></td>
                                <td><?php echo '<a href="includes/approve-borrow-request.inc.php?user=' . $details["usersId"] . '&index=' . $i . '&isbn=' . $tmp[0] . '">Approve</a>';?></td>
                                <td><?php echo '<a href="includes/reject-borrow-request.inc.php?user=' . $details["usersId"] . '&index=' . $i . '&isbn=' . $tmp[0] . '">Reject</a>';?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php $i++;?>

        <?php endforeach; ?>
    </ul>

<?php else: ?>
    <p>There are no borrow request from members currently.</p>
<?php endif; ?>

<hr>

<!-- list of issued books -->
<?php
    $res = mysqli_query($conn, "SELECT usersUsername, borrowedBooks FROM users WHERE borrowedBooks <> '' ORDER BY usersUsername;");
    $issuedBooks = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>

<?php if (!empty($issuedBooks)): ?>
    <h1>Issued books</h1>
    <?php foreach ($issuedBooks as $issuedBook => $details): ?>
        <?php echo $details["usersUsername"];?>
            <div class="table-wrapper">
                <table class="styled-table">
                    <thead>
                        <th>Book</th>
                        <th>ISBN</th>
                        <th>Expiry date</th>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice(explode(",", $details["borrowedBooks"]), 1) as $detail): ?>
                            <?php $tmp = explode(">", $detail);?>
                            <tr>
                                <td><?php echo mysqli_fetch_assoc(mysqli_query($conn, 'SELECT booksTitle FROM books WHERE booksISBN = "' . $tmp[0] . '";'))["booksTitle"];?></td>
                                <td><?php echo $tmp[0];?></td>
                                <td><?php echo $tmp[1];?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
    <?php endforeach;?>

<?php else: ?>
<p>There are no issued books currently.</p>

<?php endif;?>
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
    <p>Borrow requests from members: </p>

    <ul>
        <?php foreach ($borrowRequests as $key => $details): ?>

            <?php echo $details["usersUsername"];?>
            <?php $i = 0;?>

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
                                <td></td>
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
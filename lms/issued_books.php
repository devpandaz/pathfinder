<?php
if(!defined('dashboard2')) {
   header("location: index.php");
   exit;
}
?>

<!-- list of issued books -->
<?php
    $res = mysqli_query($conn, "SELECT usersUsername, borrowedBooks FROM users WHERE borrowedBooks <> '';");
    $issuedBooks = mysqli_fetch_all($res, MYSQLI_ASSOC);
?>

    <?php foreach ($issuedBooks as $issuedBook => $details): ?>
        <?php echo $details["usersUsername"];?>
            <!-- table here -->
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
                                <td></td>
                                <td><?php echo $tmp[0];?></td>
                                <td><?php echo $tmp[1];?></td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
    <?php endforeach;?>

<?php require "header.php"?>

    <div class="page-wrapper">
        <?php include_once 'sidebar.php'?>
        <div class="main-content">
            <form action="includes/reset-password.inc.php" method="post">

                <input type="hidden" name="selector" value="<?php echo $_GET["selector"];?>">
                <input type="hidden" name="validator" value="<?php echo $_GET["validator"];?>">
                <div class="form-field">
                    <label for="isbn">New Password: </label>
                    <input type="password" name="pwd" placeholder="Enter new password">
                </div>
                <div class="form-field">
                    <label for="isbn">Confirm new password: </label>
                    <input type="password" name="pwd-repeat" placeholder="Confirm new passsword">
                </div>
                <button type="submit" name="reset-password-submit">Submit</button>
            </form>

            <?php if (isset($_GET["newpwd"])) {
                if ($_GET["newpwd"] == "empty") {
                    echo "Fill in all fields!";
                } else if ($_GET["newpwd"] == "pwdnotsame") {
                    echo "Passwords don't match!";
                } else if ($_GET["newpwd"] == "passwordupdated") {
                    echo "Password updated!";
                }
            }
            ?>
        </div>
    </div>
<?php require "footer.php"?>
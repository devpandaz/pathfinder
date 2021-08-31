<?php require "header.php"?>

    <div class="page-wrapper">
        <?php include_once 'sidebar.php'?>
        <div class="main-content">
            <section>
                <h1>Reset your password</h1>
                <p>An e-mail will be send to you with instructions on how to reset your password.</p>
                <form action="includes/reset-request.inc.php" method="post">
                <input type="text" name="email" placeholder="Enter your email address...">
                <button type="submit" name="reset-request-submit">Reset password</button>
                </form>

                <?php
                    if (isset($_GET["reset"])) {
                        if ($_GET["reset"] == "success") {
                            echo '<p>Check your email! If it is not in your inbox, check your spam folder. </p>';
                        }
                    }
                ?>
                
            </section>
        </div>
    </div>

    <!-- need to add error handling later... -->

<?php require "footer.php"?>
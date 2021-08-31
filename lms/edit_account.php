<?php include_once 'header.php';?>
<?php include_once 'includes/dbh.inc.php';?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<div class="page-wrapper">
    <?php include_once 'sidebar.php'?>
    <div class="main-content">
        <?php
            if (isset($_SESSION["userid"])) {
                if (isset($_GET["subject"])) {
                    $targetId = $_GET["subject"];

                    if ($_SESSION["userid"] != $targetId) {
                        header("location: index.php");
                        exit();
                    }

                    $res = mysqli_query($conn, "SELECT * FROM users WHERE usersId = '$targetId'");
                    $user = mysqli_fetch_all($res, MYSQLI_ASSOC)[0];
                    
                } else {
                    header("location: index.php");
                    exit();
                }
            } else {
                header("location: index.php");
                exit();
            }
        ?>

        <div class="container">

            <!-- notices -->
            <?php
                if (isset($_SESSION["edit-account-success-msg"])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION["edit-account-success-msg"] . '</div>';
                }
                unset($_SESSION['edit-account-success-msg']);
            ?>

            <!-- three forms below, each of changing username, email and password -->

            <form method="post" action="includes/edit-account.inc.php">
                <hr><h3>Edit username</h3>

                <!-- errors -->
                <?php
                    if (isset($_SESSION["edit-account-error-username"])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-account-error-username"] . '</div>';
                    }
                    unset($_SESSION['edit-account-error-username']);
                ?>

                <div class="form-field">
                    <label for="username">Username: </label>
                    <input type="text" id="username" name="username" placeholder="Enter username" value="<?php echo $user["usersUsername"]?>">
                    <small class="respond" id="rsp-username"></small>
                </div>

                <input type="hidden" name="profile-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">     <!--to send the query url in the header so that in form action file can determine which user is it editing his/her acc-->

                <input type="hidden" name="changeUsername">
                <button type="submit" class="btnSubmit" name="btnSubmit">Change Username</button>

            </form>

            <!-- change email -->
            <form method="post" action="includes/edit-account.inc.php">
                <hr><h3>Edit email</h3>

                <!-- errors -->
                <?php
                    if (isset($_SESSION["edit-account-error-email"])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-account-error-email"] . '</div>';
                    }
                    unset($_SESSION['edit-account-error-email']);
                ?>

                <div class="form-field">
                    <label for="email">E-mail: </label>
                    <input type="text" id="email" name="email" placeholder="Enter Email" value="<?php echo $user["usersEmail"]?>">
                    <small class="respond" id="rsp-email"></small>
                </div>

                <input type="hidden" name="profile-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">     <!--to send the query url in the header so that in form action file can determine which user is it editing his/her acc-->

                <input type="hidden" name="changeEmail">
                <button type="submit" class="btnSubmit" name="btnSubmit">Change Email</button>

            </form>

            <!-- change password -->
            <form method="post" id="" action="includes/edit-account.inc.php">
                <hr><h3>Edit password</h3>

                <!-- errors -->
                <?php
                    if (isset($_SESSION["edit-account-error-pwd"])) {
                        echo '<div class="alert alert-danger" role="alert">' . $_SESSION["edit-account-error-pwd"] . '</div>';
                    }
                    unset($_SESSION['edit-account-error-pwd']);
                ?>

                <div class="form-field">
                    <label for="password">Old Password: </label>
                    <input type="password" id="old-password" name="old-password" placeholder="Enter Password">
                    <button type="button" id="view-old-password" class="view" data-view="false">
                        <i class="far fa-eye-slash"></i>
                    </button>
                    <small id="rsp-pwd"></small>
                </div>

                <div class="form-field">
                    <label for="password">New Password: </label>
                    <input type="password" id="new-password" name="new-password" placeholder="Enter Password">
                    <button type="button" id="view-new-password" class="view" data-view="false">
                        <i class="far fa-eye-slash"></i>
                    </button>
                    <small id="rsp-pwd"></small>
                </div>

                <div class="form-field">
                    <label for="confirm-password">Confirm New Password: </label>
                    <input type="password" id="confirm-new-password" name="confirm-new-password" placeholder="Repeat Password">
                    <button type="button" id="view-confirm-new-password" class="view" data-view="false">
                        <i class="far fa-eye-slash"></i>
                    </button>
                    <small id="rsp-con-apwd"></small>
                </div>

                <input type="hidden" name="profile-url" value="<?php echo $_SERVER['QUERY_STRING'];?>">     <!--to send the query url in the header so that in form action file can determine which user is it editing his/her acc-->

                <input type="hidden" name="changePwd">
                <button type="submit" class="btnSubmit" name="btnSubmit">Change Password</button>
            </form>
        </div>
    </div>
</div>

    

    <!-- js code for live validation -->
    <script type="text/javascript" src="scripts/validation.js"></script>
    <script>

        const vision_btns = document.getElementsByClassName("container-respond");
        Array.from(document.getElementsByClassName("view")).forEach(item => {
            item.addEventListener('click', event => {
                console.log("hello")
                // target and currentTarget beware of this
                const target = event.currentTarget;
                const input = target.parentElement.querySelector("input");
                const icon = target.querySelector("i");

                if (target.dataset.view == "true") {
                    // change the image
                    icon.className = "far fa-eye-slash";
                    input.type = "password";
                    target.dataset.view = "false";
                } else { 
                    icon.className = "far fa-eye";
                    input.type = "text";
                    target.dataset.view = "true";
                }
            })
        })

    </script>

<?php include_once 'footer.php';?>
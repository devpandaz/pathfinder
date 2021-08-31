<?php
    session_start();
    if (isset($_SESSION["userid"])) {
        header("location: ../index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign up for Pathfinder</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/7f15b6993d.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	<div class="btn-group">
    
		<input type="radio" name="type" id="option-1" value="student" <?php if (isset($_SESSION["temp-type"])) { if ($_SESSION["temp-type"] == "member") {echo "checked";}} else {echo 'checked';}?>>
		<input type="radio" name="type" id="option-2" value="librarian" <?php if (isset($_SESSION["temp-type"])) { if ($_SESSION["temp-type"] == "librarian") {echo "checked";}}?>>

		<label for="option-1" class="option option-1">
			<div class="dot"></div>
			<span>Student</span>
			</label>
		<label for="option-2" class="option option-2">
			<div class="dot"></div>
			<span>Librarian</span>
		</label>
	</div>
    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                <div class="login100-pic js-tilt" data-tilt>
                    <a href="../index.php"><img src="../images/librarian-logo.jpg" alt="Sign Up"></a>
                    Yok Bin Library
                </div>

                <form class="login100-form validate-form" method="POST" action="../includes/signup.inc.php">
                    <span class="login100-form-title">Sign up</span>

                    <!-- notices -->
                    <?php
                        if (isset($_SESSION["signup-error"])) {
                            echo '<div class="alert alert-success" role="alert">' . $_SESSION["signup-error"] . '</div>';
                        }
                        unset($_SESSION['signup-error']);
                    ?>

                    <div class="wrap-input100 validate-input" data-validate="Required">
                        <input class="input100" type="text" name="username" placeholder="Username" value="<?php if (isset($_SESSION["signup-temp-username"])) {$tmp = $_SESSION["signup-temp-username"]; unset($_SESSION["signup-temp-username"]); echo $tmp;} else {echo '';}?>">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Required">
                        <input class="input100" type="email" name="email" placeholder="Email" value="<?php if (isset($_SESSION["signup-temp-email"])) {$tmp = $_SESSION["signup-temp-email"]; unset($_SESSION["signup-temp-email"]); echo $tmp;} else {echo '';}?>">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-envelope" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Required">
                        <input class="input100" type="password" name="password" placeholder="Password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input" data-validate="Required">
                        <input class="input100" type="password" name="confirm-password" placeholder="Confirm Password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock" aria-hidden="true"></i>
                        </span>
                    </div>

                    <input type="hidden" name="type" id="type" value="<?php if (isset($_SESSION["temp-type"])) { if ($_SESSION["temp-type"] == "student") {echo "member"; } else {echo "librarian";} unset($_SESSION["temp-type"]);} else {echo 'student';}?>">
					
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" name="signup" type="submit">Sign Up</button>
                    </div>

                    <div class="text-center p-t-136">
                        <a class="txt2" href="login.php">
                            Log in
                            <i class="fa fa-long-arrow-right m-l-5" aria-hidden="true"></i>
                        </a>
                    </div>
                    
                    
                </form>
            </div>
        </div>
    </div>
    </body>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="vendor/tilt/tilt.jquery.min.js"></script>
    <script >
        $('.js-tilt').tilt({
            scale: 1.1
        })
    </script>
    <script>
      const option1 = document.getElementById("option-1");
      const option2 = document.getElementById("option-2");
      const role = document.getElementById("type");
      [option1, option2].forEach(element => {
        element.addEventListener("click", function(){
          role.value = element.value;
        });
      });
    </script>
  <script src="js/main.js"></script>
</body>
</html>
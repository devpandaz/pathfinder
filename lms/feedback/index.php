<?php session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Feedback</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="images/icons/favicon.ico">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
	<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
	<link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>

	<div class="bg-contact100" style="background-image: url('images/bg-01.jpg');">
		<div class="container-contact100">
			<div class="wrap-contact100">
				<div class="contact100-pic js-tilt" data-tilt>
					<img src="images/img-01.png" alt="IMG">
				</div>

				<form class="contact100-form validate-form" method="post" action="../includes/feedback.inc.php">
					<span class="contact100-form-title">
						Your feedback
					</span>

					<?php
						if (isset($_SESSION["feedback-submitted-success-msg"])) {
							echo '<div class="alert alert-success" role="alert">' . $_SESSION["feedback-submitted-success-msg"] . '</div>';
							unset($_SESSION["feedback-submitted-success-msg"]);
						}

						if (isset($_SESSION["give-feedback-error"])) {
							echo '<div class="alert alert-danger" role="alert">' . $_SESSION["give-feedback-error"] . '</div>';
							unset($_SESSION["give-feedback-error"]);
						}
					?>

					<div class="wrap-input100 validate-input" data-validate="Required">
						<input class="input100" type="text" name="name" placeholder="Name">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-user" aria-hidden="true"></i>
						</span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Required">
						<input class="input100" type="email" name="email" placeholder="Email">
						<span class="focus-input100"></span>
						<span class="symbol-input100">
							<i class="fa fa-envelope" aria-hidden="true"></i>
						</span>
					</div>

					<div class="form-field">
						<div class="emoji-wrapper">
							<label class="emoji-container">
								<input type="radio" id="emoji" class="sad" name="emoji" value="Bad">
								<i class="far fa-frown"></i>
							</label>
							<label class="emoji-container">
								<input type="radio" id="emoji" class="average" name="emoji" value="Average">
								<i class="far fa-meh"></i>
							</label>
							<label class="emoji-container">
								<input type="radio" id="emoji" class="happy" name="emoji" value="Good">
								<i class="far fa-smile"></i>
							</label>
						</div>
					</div>

					<div class="wrap-input100 validate-input">
						<textarea class="input100 feedback" name="feedback" placeholder="Feedback (Optional)"></textarea>
						<span class="focus-input100"></span>
					</div>

					<div class="container-contact100-form-btn">
						<button class="contact100-form-btn" name="submit-feedback">
							Submit
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="vendor/select2/select2.min.js"></script>
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>

	<script>
		(function ($) {
			"use strict";

		
			/*==================================================================
			[ Validate ]*/
			var input = $('.validate-input .input100').not(".feedback");

			$('.validate-form').on('submit',function(){
				var check = true;

				for(var i=0; i<input.length; i++) {
					if(validate(input[i]) == false){
						showValidate(input[i]);
						check=false;
					}
				}

				return check;
			});


			$('.validate-form .input100').each(function(){
				$(this).focus(function(){
				hideValidate(this);
				});
			});

			function validate (input) {
				if($(input).val().trim() == ''){
					return false;
				}
			}

			function showValidate(input) {
				var thisAlert = $(input).parent();

				$(thisAlert).addClass('alert-validate');
			}

			function hideValidate(input) {
				var thisAlert = $(input).parent();

				$(thisAlert).removeClass('alert-validate');
			}
		
		})(jQuery);

	</script>

</body>
</html>

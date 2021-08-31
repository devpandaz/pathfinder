<?php
	//Load Composer's autoloader
	require '../vendor/autoload.php';

	// use phpdotenv
	$dotenv = Dotenv\Dotenv::createImmutable(str_replace("\\error", "", __DIR__));
	$dotenv->load();

	$status = $_SERVER['REDIRECT_STATUS'];
	$codes = array(
		403 => array('403', 'The server has refused to fulfill your request.'),
		404 => array('404', 'The document/file requested was not found on this server.'),
		405 => array('405', 'The method specified in the Request-Line is not allowed for the specified resource.'),
		408 => array('408', 'Your browser failed to send a request in the time allowed by the server.'),
		500 => array('500', 'The request was unsuccessful due to an unexpected condition encountered by the server.'),
		502 => array('502', 'The server received an invalid response from the upstream server while trying to fulfill the request.'),
		504 => array('504', 'The upstream server failed to send a request in the time allowed by the server.'),
	);

	$code = $codes[$status][0];
	$message = $codes[$status][1];
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<base href="<?php echo $_SERVER["DOMAIN"] . "/pathfinder/";?>">

	<title>Oops!</title>

	<link href="https://fonts.googleapis.com/css?family=Montserrat:700,900" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="error/css/font-awesome.min.css">
	<link type="text/css" rel="stylesheet" href="error/css/style.css">

</head>

<body>

	<!-- Messenger Chat plugin Code -->
	<div id="fb-root"></div>

	<!-- Your Chat plugin code -->
	<div id="fb-customer-chat" class="fb-customerchat">
	</div>

	<script>
	  var chatbox = document.getElementById('fb-customer-chat');
	  chatbox.setAttribute("page_id", "102055062196215");
	  chatbox.setAttribute("attribution", "biz_inbox");

	  window.fbAsyncInit = function() {
		FB.init({
		  xfbml            : true,
		  version          : 'v11.0'
		});
	  };

	  (function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
		fjs.parentNode.insertBefore(js, fjs);
	  }(document, 'script', 'facebook-jssdk'));
	</script>

	<div id="notfound">
		<div class="notfound-bg"></div>
		<div class="notfound">
			<div class="notfound-404">
				<h1><?php echo $code;?></h1>
			</div>
			<h2><?php echo $message;?></h2>
			<a href="./index.php" class="home-btn">Go to Home</a>
			<div class="notfound-social">
				<a href="https://www.facebook.com/Pathfinder-102055062196215/" target="_blank"><i class="fa fa-facebook"></i></a>
				<a href="https://www.instagram.com/pathfinderlibms/" target="_blank"><i class="fa fa-instagram"></i></a>
			</div>
		</div>
	</div>

</body>

</html>

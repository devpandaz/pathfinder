<?php

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

// use phpdotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (isset($_POST["submit-feedback"])) {
    session_start();

    $name = $_POST["name"];
    $email = $_POST["email"];
    $emoji = $_POST["emoji"];
    $feedback = $_POST["feedback"];

    // validation
    include_once 'functions.inc.php';
    if (empty($name) || empty($email) || empty($emoji)) {
        $_SESSION["give-feedback-error"] = "Fill in all fields.";
        if (empty($emoji)) {
            $_SESSION["give-feedback-error"] .= "<br>Select one of the emoji.";
        }
    }
    if (invalidUsername($name)) {
        $_SESSION["give-feedback-error"] = "The name entered is invalid!";
    }
    if (invalidEmail($email)) {
        $_SESSION["give-feedback-error"] = "The email entered is invalid!";
    }

    if (isset($_SESSION["give-feedback-error"])) {
        header("location: ../feedback/index.php");
        exit;
    }

    // now send the feedback to ourself, to our email
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'pathfinderlibms@gmail.com';                     //SMTP username
        $mail->Password   = $_SERVER["EMAIL_PASSWORD"];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('pathfinderlibms@gmail.com');
        $mail->addAddress('pathfinderlibms@gmail.com');

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Feedback from user';
        $mail->Body    = "Feedback from '$name' ($email). <br>User experience: $emoji<br>Comment: $feedback";

        $mail->send();

        $_SESSION["feedback-submitted-success-msg"] = "Feedback submitted. We appreciate it and promise will read through them so that we can provide you a better user experience next time!";

        // reply the user here based on his reaction whether it's good, average, or bad
        $mail->clearAllRecipients();
        $mail->addAddress($email);
        //Content
        $mail->Subject = 'Thanks for your feedback!';
        if ($emoji == "Good") {
            $mail->Body = "Thanks for your kind response. We will work harder to meet up your expectations!";
        } elseif ($emoji == "Average") {
            $mail->Body = "Thanks for your kind response. We will update our website regularly to provide better service to our customers.Thank you!";
        } else {
            $mail->Body = "We are so sorry for letting you down. Your advice would be engraved on our heart. Stay tuned for better updates.";
        }
        $mail->send();

        header("location: ../feedback/index.php");
        exit;

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
} else {
    header("location: ../index.php");
    exit;
}
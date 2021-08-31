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

if (isset($_POST["reset-request-submit"])) {

    $selector = bin2hex(random_bytes(8));
    $token = random_bytes(32);

    $domain = $_SERVER["DOMAIN"];     // this is the current link of the temporary server, need to change it in .env every time a new server is started...uwu

    $url = "$domain/pathfinder/create_new_password.php?selector=$selector&validator=" . bin2hex($token);

    $expires = date("U") + 600;    // change the 1800 to ten mins time

    require_once 'dbh.inc.php';

    $userEmail = $_POST["email"];

    $sql = "DELETE FROM pwdReset WHERE pwdResetEmail=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!";
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $userEmail);
        mysqli_stmt_execute($stmt);
    }

    $sql = "INSERT INTO pwdReset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";

    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo "There was an error!";
        exit();
    } else {
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
        mysqli_stmt_execute($stmt);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

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
        $mail->setFrom('pathfinderlibms@gmail.com', 'Pathfinder');
        $mail->addAddress($userEmail);

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Reset your password';
        $mail->Body    = '<p>We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email. Here is your password reset link: <a href = "' . $url . '">' . $url . '</a><br></p>';
        $mail->AltBody = 'We received a password reset request. The link to reset your password is below. If you did not make this request, you can ignore this email. Here is your password reset link: ' . $url;

        $mail->send();
        // echo 'Message has been sent';
        // sleep('1');
        header("location: ../reset_password.php?reset=success");
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    
} else {
    header("location: ../index.php");
}
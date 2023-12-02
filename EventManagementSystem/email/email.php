<?php
session_start();

$to = "angelavarmena@gmail.com";
$subject = "AdU-EMS OTP Verification";
$message = "HELLO";
$header = "From: ME";

if(mail($to, $subject, $message, $header)) {
    echo "email sent successfully";
} else {
    echo "email failed";
}
?>
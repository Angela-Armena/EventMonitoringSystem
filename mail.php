<?php
  use PHPMailer\PHPMailer\PHPMailer;
  use PHPMailer\PHPMailer\Exception;
  require 'PHPMailer-master/src/Exception.php';
  require 'PHPMailer-master/src/PHPMailer.php';
  require 'PHPMailer-master/src/SMTP.php';

function send_mail($recipient, $subject, $message)
{
  $mail = new PHPMailer(true);
  $mail->IsSMTP();
  $mail->SMTPOptions = array(
    'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
    )
    );
  $mail->SMTPDebug  = 0;  
  $mail->SMTPAuth   = TRUE; 
  $mail->SMTPSecure = "tls";
  $mail->Port       = 587;
  $mail->Host       = "smtp.gmail.com";
  //$mail->Host       = "smtp.mail.yahoo.com";
  $mail->Username   = "godswillokpanku@gmail.com"; //Enter your email here
  $mail->Password   = "zjgw vuyq lchm vinq"; //Enter the generated password

  $mail->IsHTML(true);
  $mail->AddAddress($recipient, "Esteemed user");
  $mail->SetFrom("godswillokpanku@gmail.com", "AdU-EMS"); // Enter the same email here and then enter your random username at the 'xyz'
  //$mail->AddReplyTo("reply-to-email", "reply-to-name"); 
  //$mail->AddCC("cc-recipient-email", "cc-recipient-name");
  $mail->Subject = $subject;
  $content = $message;

  $mail->MsgHTML($content); 
  if(!$mail->Send()) {
    echo "Error while sending Email.";
    echo "<pre>";
    var_dump($mail);
    //return false;
  } else {
    //echo "Email sent successfully";
    //return true;
  }

}

?>

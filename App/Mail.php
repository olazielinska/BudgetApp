<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mail{

  public static function send($to, $subject, $text, $html){
  
    $mail = new PHPMailer(true);

    try {                    
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                     
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'aleksandra.zielinska.programista@gmail.com';                    
        $mail->Password = Config::PHPMAILER_KEY;                                     
        $mail->Port       = 587;   

        $mail->setFrom(Config::PHPMAILER_EMAIL);
        $mail->addAddress($to);    
        $mail->addReplyTo($to);
    
        $mail->isHTML(true);    

        $mail->Subject = $subject;
        $mail->Body    = $html;
        $mail->AltBody = $text;
    
        $mail->send();

        $sent = true;
    } catch (Exception $e) {
        echo "Wiadomość nie została wysłana. Błąd: {$mail->ErrorInfo}";
    }
}
}
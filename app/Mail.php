<?php
namespace App;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    public $subjet;
    public $body;

    public function __construct($subject, $body, $name, $email)
    {
        $this->subject = $subject;
        $this->body = $body;
        $this->name = $name;
        $this->email = $email;
    }
    public function send_mail()
    {
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            //To use SMTP
            if($_ENV["SMTP_ENABLED"] === "YES"){
                //Server settings
                $mail->isSMTP(); //Send using SMTP
                $mail->Host = $_ENV["SMTP_HOST"]; //Set the SMTP server to send through
                $mail->SMTPAuth = true; //Enable SMTP authentication
                $mail->Username = $_ENV["SMTP_USER"]; //SMTP username
                $mail->Password = $_ENV["SMTP_PASS"]; //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = $_ENV["SMTP_PORT"]; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            }
            // To use regular email sending
            if($_ENV["SMTP_ENABLED"] === "NO"){
                $mail->SMTPDebug = 0;                       //Enable verbose debug output
                $mail->isSMTP(); //Send using SMTP
                $mail->Host = 'localhost';
                $mail->SMTPAuth = false;
                $mail->SMTPAutoTLS = false; 
                $mail->Port = 25;
            }
            //Recipients
            $mail->setFrom($_ENV["FROM"], utf8_decode($this->name));
            $mail->addAddress($_ENV["TO"]); // Add a recipient
            $mail->addReplyTo($this->email);
            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $this->subject;
            $mail->Body = $this->body;

            $mail->send();
            $response = array(
                "status" => 'success',
                "title" => '¡Recibimos su solicitud!',
                "body" => 'Nos pondremos en contacto con usted para ofrecerle atención personalizada a sus requerimientos.',
            );
            return json_encode($response);
        } catch (Exception $e) {
            //Error Message
            header("HTTP/1.1 500 Internal Server Error");
            return json_encode(array(
                "body" => "No fue posible enviar su mensaje {$mail->ErrorInfo}", //Mailer Error: {$mail->ErrorInfo}
                "status" => "error",
                "title" => "Error"
                )
            );
        }
    }

}
<?php
namespace App;

use App\Mail;
use Respect\Validation\Validator as v;

class Request
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    public function set_email_data()
    {
        $subject = utf8_decode($this->data["name"] . $_ENV["SUBJECT"]);
        $body = $this->body();
        $mail_data = new Mail($subject, $body, $this->data["name"], $this->data["email"]);
        echo $mail_data->send_mail();

    }
    //Search for errors and add them into an Array
    public function check_and_send()
    {
        $errors = array();
        if ($this->check_names($this->data["name"], "Nombre")) {
            array_push($errors, $this->check_names($this->data["name"], "Nombre"));
        }
        if ($this->check_email($this->data["email"], "Email")) {
            array_push($errors, $this->check_email($this->data["email"], "Email"));
        }
        if ($this->check_phone($this->data["phone"], "Telefono")) {
            array_push($errors, $this->check_phone($this->data["phone"], "Teléfono"));
        }
        if ($errors) {
            $response = "";
            foreach ($errors as $error) {
                $response .= $error . PHP_EOL;
            }
            //Response Message
            $message = array(
                "status" => "warning",
                "title" => "Aviso",
                "body" => $response,
            );
            header('HTTP/1.1 400 Bad Request');
            echo json_encode($message);
            return false;
        }
        $this->set_email_data();
    }
    public function check_names($names, $title)
    {
        $errors = array();
        if (!v::alpha(' ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'í', 'Ó', 'Ú', 'ñ')->validate($names)) {
            array_push($errors, 'Solo introduzca letras y espacios');
        }
        return $this->string_errors($errors, $title);

    }
    public function check_email($email, $title)
    {
        $errors = array();
        if (!v::email()->validate($email)) {
            array_push($errors, 'Indique un email válido');
        }
        return $this->string_errors($errors, $title);
    }
    public function check_phone($phone, $title)
    {
        $errors = array();
        if (!v::phone()->validate($phone) && v::stringType()->notEmpty()->validate($phone)) {
            array_push($errors, 'Indique un teléfono válido');
        }
        return $this->string_errors($errors, $title);
    }
    //Function to setup te error message
    public function string_errors($errors, $title)
    {
        if ($errors) {
            $response = $title . PHP_EOL;
            foreach ($errors as $error) {
                $response .= $error . PHP_EOL;
            }
            return $response;
        }
        return false;

    }
    //Function to add fields info into de body text
    public function setField($data, $title, $show){
      if($data !== "" && $show){
        return 
        "
        <h3 style='color:#fec31f;'>
          $title
        </h3>
        <h3 style='color:#333333;'>
          $data
        </h3>
        </br>
        ";
      }else{
        return '';
      }
    }
    public function body()
    {
        $data = $this->data;
        return "
      <h3 style='color:#fec31f;'>
        Nombre Completo:
      </h3>
      <h3 style='color:#333333;' >
        {$data['name']}
      </h3>
      </br>
      <h3 style='color:#fec31f;'>
        Email:
      </h3>
      <h3 style='color:#333333;'>
      {$data['email']}
      </h3>
      </br>
      "
      .$this->setField($data['city'], 'Ciudad:', true) 
      .$this->setField($data['phone'], 'Teléfono:', true) .
      "
      <h3 style='color:#fec31f;'>
        Productos seleccionados:
      </h3>
      <ul style='color:#333333;'>
        {$data['selectedProducts']}
      </ul>
      </br>
      ". $this->setField($data['otherPrd'], 'Otra variedad deseada no disponible en el listado:', true) .
      "
      <h3 style='color:#fec31f;'>
        Ya trabaja con otro distribuidor:
      </h3>
      <h3 style='color:#333333;'>
      {$data['otherDis']}
      </h3>
      </br>
      ". $this->setField($data['otherDisComent'], 'Distribuidor con el que trabaja:', true) .
      $this->setField($data['coment'], 'Comentario adicional:', true)
    
    ;
    }
}
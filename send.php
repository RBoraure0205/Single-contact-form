<?php
require_once 'vendor/autoload.php';

use App\Request;

//load Env Variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Send data and return a message
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request = new Request($_POST); //Check if it is only text or images too
    return $request->check_and_send();
}

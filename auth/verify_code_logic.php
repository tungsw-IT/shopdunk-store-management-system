<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = $_POST["code"];
    if ($_SESSION['reset_code'] == $code) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Mã xác thực không chính xác.";
    }
}
?>







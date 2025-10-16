<?php
$server = "localhost";
$user   = "root";
$pass   = "";
$db     = "data_stack";

$conn = new mysqli($server, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>

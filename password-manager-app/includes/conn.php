<?php 

$servername = "localhost";
$username = "u510162695_mccalumni_root";
$password = "1Mccalumni_root";
$db = "u510162695_mccalumni";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}

?>
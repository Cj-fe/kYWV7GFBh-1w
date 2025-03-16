<?php 

$servername = "localhost";
$username = "u510162695_mccalumni";
$password = "u510162695_mccalumni_root";
$db = "1Mccalumni_root";


try {
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Failed " . $e->getMessage();
}

?>
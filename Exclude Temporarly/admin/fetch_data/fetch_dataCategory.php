<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

// Retrieve category data
$data = $firebase->retrieve("category");
$data = json_decode($data, true);

if (is_array($data)) {
    foreach ($data as $id => $category) {
        // Assuming categories have a name and description; adjust as needed
        $category_name = $category['category_name'];

        echo "<tr>
                <td>{$category_name}</td>
                <td>
                    <a class='btn btn-success btn-sm btn-flat open-modal' data-id='$id'>EDIT</a>
                    <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }
}
?>

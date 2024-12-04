<?php
// Calculate the initial hash of index.php
$originalHash = hash_file('sha256', 'index.php');

// Store the hash in a file
file_put_contents('index_hash.txt', $originalHash);

echo "Original hash calculated and stored.";
?>
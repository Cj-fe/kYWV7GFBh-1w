<?php
// Path to the index.php file
$indexFilePath = 'index.php';

// Path to the file storing the original hash
$hashFilePath = 'index_hash.txt';

// Check if the hash file exists
if (!file_exists($hashFilePath)) {
    die('Hash file not found. Please generate the original hash.');
}

// Read the stored original hash
$originalHash = file_get_contents($hashFilePath);

// Calculate the current hash of index.php
$currentHash = hash_file('sha256', $indexFilePath);

// Compare the hashes
if ($currentHash !== $originalHash) {
    // Log the modification
    file_put_contents('modification_log.txt', "index.php modified on " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

    // Redirect to backup.php
    header('Location: backup.php');
    exit();
} else {
    echo "No modification detected.";
}
?>
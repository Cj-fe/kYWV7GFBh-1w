
<?php
  include 'conn.php';

  header('Content-Type: application/json');

  $response = [
      'status' => 'error',
      'message' => 'An unknown error occurred.'
  ];

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      $passwordId = $_POST['password_id']; // Assuming the ID is passed in the POST request

      try {
          $stmt = $conn->prepare("DELETE FROM tbl_save_passwords WHERE id = :id");
          $stmt->bindParam(':id', $passwordId);

          if ($stmt->execute()) {
              $response['status'] = 'success';
              $response['message'] = 'Password deleted successfully!';
          } else {
              $response['message'] = 'Failed to delete password.';
          }
      } catch (PDOException $e) {
          $response['message'] = 'Error: ' . $e->getMessage();
      }
  }

  echo json_encode($response);
  ?>

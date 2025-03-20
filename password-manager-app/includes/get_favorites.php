
<?php
  require_once 'conn.php';
  
  try {
      $sql = "SELECT p.* FROM tbl_favorites f 
              JOIN tbl_save_passwords p ON f.password_id = p.id";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      
      $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      echo json_encode(['status' => 'success', 'favorites' => $favorites]);
  } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => 'Failed to fetch favorites: ' . $e->getMessage()]);
  }
  ?>

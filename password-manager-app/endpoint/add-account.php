<?php
include ('../conn/conn.php');

session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $accountName = $_POST['account_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $link = $_POST['link'];
        $description = $_POST['description'];

        try {
            $stmt = $conn->prepare("SELECT `username` FROM `tbl_accounts` WHERE `username` = :username");
            $stmt->execute([
                'username' => $username
            ]);
            $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($userExists)) {
                $conn->beginTransaction();

                $insertStmt = $conn->prepare("INSERT INTO `tbl_accounts` (`tbl_account_id`, `tbl_user_id`, `account_name`, `username`, `password`, `link`, `description`) VALUES (NULL, :user_id, :account_name, :username, :password, :link, :description)");
                $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $insertStmt->bindParam(':account_name', $accountName, PDO::PARAM_STR);
                $insertStmt->bindParam(':username', $username, PDO::PARAM_STR);
                $insertStmt->bindParam(':password', $password, PDO::PARAM_STR);
                $insertStmt->bindParam(':link', $link, PDO::PARAM_STR);
                $insertStmt->bindParam(':description', $description, PDO::PARAM_STR);
                $insertStmt->execute();

                echo "
                <script>
                    alert('Registered Successfully');
                    window.location.href = 'http://localhost/password-manager-app/home.php';
                </script>
                ";

                $conn->commit();
            } else {
                echo "
                <script>
                    alert('User Already Exists');
                    window.location.href = 'http://localhost/password-manager-app/home.php';
                </script>
                ";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "
        <script>
            alert('Account Registration Failed!');
            window.location.href = 'http://localhost/password-manager-app/home.php';
        </script>
        ";
    }
} else {
    echo "User not logged in. Please log in before adding an account.";
}
?>

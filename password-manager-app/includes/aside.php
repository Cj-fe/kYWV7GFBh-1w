<?php
// Start session or necessary setup

// Determine the current script name without extension
$currentFile = basename($_SERVER['PHP_SELF'], '.php');

// Map current script name to the page identifier used for the active class
$pageMap = [
    'dashboard' => 'dashboard',
    'favorites_page' => 'favorites',
    'websites_page' => 'websites',
    'payment_cards_page' => 'payment_cards',
    'bank_accounts_page' => 'bank_accounts',
    'secure_notes_page' => 'secure_notes',
    'personal_info_page' => 'personal_info',
    'password_health_page' => 'password_health',
    'password_generator_page' => 'password_generator',
    'settings_page' => 'settings',
];

// Assign the current page key based on the script name
$currentPage = $pageMap[$currentFile] ?? 'dashboard'; // Default to 'dashboard' if undefined
?>

<aside class="sidebar">
    <button class="add-button">
        <i class="bi bi-plus-lg"></i>
        Add New Item
    </button>

    <div class="collapsible-content">
        <button class="collapsible-item">
            <i class="bi bi-file-earmark-plus"></i>
            Add Password
        </button>
        <button class="collapsible-item">
            <i class="bi bi-folder"></i>
            Add Folder
        </button>
        <button class="collapsible-item">
            <i class="bi bi-file-earmark-text"></i>
            Add Secure Note
        </button>
    </div>

    <div class="categories">
        <h3>Categories</h3>
        <div class="category-item <?php if ($currentPage === 'dashboard')
            echo 'active'; ?>" data-page="dashboard.php">
            <i class="bi bi-grid-fill"></i>
            <span>All Items</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'favorites')
            echo 'active'; ?>"
            data-page="favorites_page.php">
            <i class="bi bi-star-fill"></i>
            <span>Favorites</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'websites')
            echo 'active'; ?>"
            data-page="websites_page.php">
            <i class="bi bi-globe"></i>
            <span>Websites</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'payment_cards')
            echo 'active'; ?>"
            data-page="payment_cards_page.php">
            <i class="bi bi-credit-card-fill"></i>
            <span>Payment Cards</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'bank_accounts')
            echo 'active'; ?>"
            data-page="bank_accounts_page.php">
            <i class="bi bi-bank"></i>
            <span>Bank Accounts</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'secure_notes')
            echo 'active'; ?>"
            data-page="secure_notes_page.php">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Secure Notes</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'personal_info')
            echo 'active'; ?>"
            data-page="personal_info_page.php">
            <i class="bi bi-person-badge-fill"></i>
            <span>Personal Info</span>
        </div>
    </div>

    <div class="categories">
        <h3>Folders</h3>
        <?php
        // Check if there are any folders
        $sql = "SELECT folder_id, folder_name FROM tbl_folder";
        $result = $conn->query($sql);

        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="category-item" data-folder-id="' . $row['folder_id'] . '">';
                echo '<i class="bi bi-folder-fill"></i>';
                echo '<span>' . htmlspecialchars($row['folder_name']) . '</span>';
                echo '<input type="hidden" value="' . htmlspecialchars($row['folder_id']) . '">';
                echo '<div class="menu-icon">';
                echo '<i class="bi bi-three-dots-vertical"></i>';
                echo '<div class="dropdown-menu">';
                echo '<div class="dropdown-item">Trash</div>';
                echo '<div class="dropdown-item">Rename</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>No folders available</p>';
        }
        ?>
    </div>

    <div class="categories">
        <h3>Tools</h3>
        <div class="category-item <?php if ($currentPage === 'password_health')
            echo 'active'; ?>"
            data-page="password_health_page.php">
            <i class="bi bi-shield-check"></i>
            <span>Password Health</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'password_generator')
            echo 'active'; ?>"
            data-page="password_generator_page.php">
            <i class="bi bi-key-fill"></i>
            <span>Password Generator</span>
        </div>
        <div class="category-item <?php if ($currentPage === 'settings')
            echo 'active'; ?>"
            data-page="settings_page.php">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
        </div>
    </div>
</aside>

<script>
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', function () {
            const page = this.getAttribute('data-page');
            window.location.href = page;
        });
    });
</script>
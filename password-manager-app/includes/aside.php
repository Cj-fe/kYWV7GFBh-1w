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
        <div class="category-item active">
            <i class="bi bi-grid-fill"></i>
            <span>All Items</span>
        </div>
        <div class="category-item">
            <i class="bi bi-star-fill"></i>
            <span>Favorites</span>
        </div>
        <div class="category-item">
            <i class="bi bi-globe"></i>
            <span>Websites</span>
        </div>
        <div class="category-item">
            <i class="bi bi-credit-card-fill"></i>
            <span>Payment Cards</span>
        </div>
        <div class="category-item">
            <i class="bi bi-bank"></i>
            <span>Bank Accounts</span>
        </div>
        <div class="category-item">
            <i class="bi bi-file-earmark-text-fill"></i>
            <span>Secure Notes</span>
        </div>
        <div class="category-item">
            <i class="bi bi-person-badge-fill"></i>
            <span>Personal Info</span>
        </div>
    </div>

    <div class="categories">
    <h3>Folders</h3>

    <?php
    // Check if there are any folders
    // Fetch folder data from the database
    $sql = "SELECT folder_id, folder_name FROM tbl_folder";
    $result = $conn->query($sql);

    if ($result->rowCount() > 0) {
        // Output each folder
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="category-item" data-folder-id="' . $row['folder_id'] . '">';
            echo '<i class="bi bi-folder-fill"></i>';
            echo '<span>' . htmlspecialchars($row['folder_name']) . '</span>';
            echo '<input type="hidden" value="' . htmlspecialchars($row['folder_id']) . '">';
            echo '<div class="menu-icon">';
            echo '<i class="bi bi-three-dots-vertical"></i>';
            echo '<div class="dropdown-menu">'; // Start of dropdown menu
            echo '<div class="dropdown-item">Trash</div>';
            echo '<div class="dropdown-item">Rename</div>';
            echo '</div>'; // End of dropdown menu
            echo '</div>'; // End of menu-icon
            echo '</div>'; // End of category-item
        }
    } else {
        echo '<p>No folders available</p>';
    }
    ?>
</div>
   
    <div class="categories">
        <h3>Tools</h3>
        <div class="category-item">
            <i class="bi bi-shield-check"></i>
            <span>Password Health</span>
        </div>
        <div class="category-item">
            <i class="bi bi-key-fill"></i>
            <span>Password Generator</span>
        </div>
        <div class="category-item">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
        </div>
    </div>
</aside>

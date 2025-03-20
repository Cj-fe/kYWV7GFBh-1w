<?php require_once 'includes/auth.php'; ?>
<?php require_once 'includes/conn.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SecureVault - Password Manager Dashboard</title>
    <?php include 'includes/header.php'; ?>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <main>
        <div class="container">

            <?php include 'includes/aside.php'; ?>

            <section class="content">
                <div class="password-header">
                    <h2>All Items</h2>
                    <div class="view-toggle">
                        <?php
                        // Fetch the display option from the database
                        $sql = "SELECT `option` FROM `display_option` WHERE `id` = 'd451fd0d04c62ea543de70d48fc436c83e998995c6d85fb2ccedc6b8a0febce4/27160c038fd64bcb2e4533f224b55c3933ceb2d9d244f8536585355e993d66b1'";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);

                        $displayOption = $result['option'] ?? 0; // Default to grid view if not set
                        
                        // Determine which button should be active
                        $gridActiveClass = $displayOption === 0 ? 'active' : '';
                        $listActiveClass = $displayOption === 1 ? 'active' : '';
                        ?>
                        <button class="<?php echo $gridActiveClass; ?>">
                            <i class="bi bi-grid"></i>
                            Grid View
                        </button>
                        <button class="<?php echo $listActiveClass; ?>">
                            <i class="bi bi-list-ul"></i>
                            List View
                        </button>
                    </div>
                </div>

                <div class="password-grid" style="display: <?php echo $displayOption === 0 ? 'grid' : 'none'; ?>;">
                    <?php
                    // Fetch and sort favorite password items by website_name
                    $sql = "SELECT p.id, p.website_name, p.username, p.password, p.website_url, p.folder, p.icon_image, 
                                   p.icon_file_name, p.date_created, f.folder_name
                            FROM tbl_save_passwords p
                            INNER JOIN tbl_favorites fav ON p.id = fav.password_id
                            LEFT JOIN tbl_folder f ON p.folder = f.folder_id
                            ORDER BY p.website_name"; // Sort by website_name
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();
                    $passwords = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($passwords as $row) {
                        ?>
                        <div class="password-card" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                            <div class="card-header">
                                <div class="list-item-icon">
                                    <i class="bi bi-<?php echo htmlspecialchars($row['icon_file_name']); ?>"></i>
                                </div>
                                <div class="card-title">
                                    <h3><?php echo htmlspecialchars($row['website_name']); ?></h3>
                                </div>
                                <?php if ($row['folder_name']) { ?>
                                    <div class="folder-badge">
                                        <i class="bi bi-folder"></i>
                                        <?php echo htmlspecialchars($row['folder_name']); ?>
                                    </div>
                                <?php } ?>

                                <!-- Menu icon button -->
                                <button class="menu-icon-btn" title="Menu">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <!-- Dropdown menu -->
                                <div class="card-actions-dropdown">
                                    <div class="card-dropdown-action" title="View">
                                        <i class="bi bi-eye"></i>
                                        View
                                    </div>
                                    <div class="card-dropdown-action" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                        Update
                                    </div>
                                    <div class="card-dropdown-action" title="Add to favorites">
                                        <i class="bi bi-star"></i>
                                        Add to favorites
                                    </div>
                                    <div class="card-dropdown-action" title="Delete">
                                        <i class="bi bi-trash"></i>
                                        Delete
                                    </div>
                                </div>
                            </div>

                            <div class="password-details">
                                <div class="detail-item">
                                    <div class="detail-label">Username:</div>
                                    <div class="detail-value">
                                        <div class="username-field"
                                            data-username="<?php echo htmlspecialchars($row['username']); ?>">
                                            <span class="username-dots">••••••••</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Password:</div>
                                    <div class="detail-value">
                                        <div class="password-field"
                                            data-password="<?php echo htmlspecialchars($row['password']); ?>">
                                            <span class="password-dots">••••••••••••</span>
                                            <button class="copy-btn">
                                                <i class="bi bi-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Website:</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($row['website_url']); ?></div>
                                </div>
                            </div>
                            <div class="card-actions">
                                <button class="action-btn" title="Eye">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn" title="Add to favorites">
                                    <i class="bi bi-star"></i>
                                </button>
                                <button class="action-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="password-list" style="display: <?php echo $displayOption === 1 ? 'flex' : 'none'; ?>;">
                    <?php foreach ($passwords as $row) { ?>
                        <div class="password-list-item" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                            <div class="list-item-content">
                                <div class="list-item-icon">
                                    <i class="bi bi-<?php echo htmlspecialchars($row['icon_file_name']); ?>"></i>
                                </div>
                                <div class="list-item-details">
                                    <h3><?php echo htmlspecialchars($row['website_name']); ?></h3>
                                </div>
                            </div>
                            <!-- Desktop action buttons -->
                            <div class="list-item-actions desktop-only">
                                <button class="action-btn" title="Eye">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="action-btn" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="action-btn" title="Add to favorites">
                                    <i class="bi bi-star"></i>
                                </button>
                                <button class="action-btn" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <!-- Mobile menu button -->
                            <button class="menu-icon-btn mobile-only" title="Menu">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <div class="card-actions-dropdown">
                                <div class="card-dropdown-action" title="View">
                                    <i class="bi bi-eye"></i>
                                    View
                                </div>
                                <div class="card-dropdown-action" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                    Update
                                </div>
                                <div class="card-dropdown-action" title="Add to favorites">
                                    <i class="bi bi-star"></i>
                                    Add to favorites
                                </div>
                                <div class="card-dropdown-action" title="Delete">
                                    <i class="bi bi-trash"></i>
                                    Delete
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </section>
        </div>
    </main>

    <?php include 'partials/modal.php'; ?>

    <footer>
        &copy; 2025 SecureVault Password Manager. All rights reserved.
    </footer>


</body>

<!-- Custom Script -->
<script type="module" src="assets/script.js"></script>
<script type="module" src="assets/aside.js"></script>
<script src="assets/notificationManager.js"></script>

</html>
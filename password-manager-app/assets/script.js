
document.addEventListener('DOMContentLoaded', function () {
    /*============ Mobile Menu Toggle Start Here ============*/
    document.querySelector('.menu-button').addEventListener('click', function () {
        document.querySelector('.sidebar').classList.toggle('open');
        document.querySelector('.sidebar-overlay').classList.toggle('active');
    });
    /*============ Mobile Menu Toggle End Here ============*/

     /* ============ Username Reveal Functionality Start Here ============ */
     document.querySelectorAll('.username-field').forEach(field => {
        field.addEventListener('click', function () {
            const dots = field.querySelector('.username-dots');
            const actualUsername = field.getAttribute('data-username');

            if (dots.textContent.includes('•')) {
                dots.textContent = actualUsername; // Show the full username
                setTimeout(() => {
                    dots.textContent = "••••••••"; // Revert to ellipsis after 3 seconds
                }, 3000);
            }
        });
    });
    /* ============ Username Reveal Functionality End Here ============ */
    

    /*============ Close Sidebar on Outside Click Start Here ============*/
    document.addEventListener('click', function (event) {
        const sidebar = document.querySelector('.sidebar');
        const menuButton = document.querySelector('.menu-button');
        const overlay = document.querySelector('.sidebar-overlay');

        if (sidebar.classList.contains('open') &&
            !sidebar.contains(event.target) &&
            event.target !== menuButton &&
            !menuButton.contains(event.target)) {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    });
    /*============ Close Sidebar on Outside Click End Here ============*/

    /*============ View Toggle Functionality Start Here ============*/
    const gridButton = document.querySelector('.view-toggle button:first-child');
    const listButton = document.querySelector('.view-toggle button:last-child');
    const passwordGrid = document.querySelector('.password-grid');
    const passwordList = document.querySelector('.password-list');

    gridButton.addEventListener('click', function () {
        gridButton.classList.add('active');
        listButton.classList.remove('active');
        passwordGrid.style.display = 'grid';
        passwordList.style.display = 'none';
        updatePagination();
    });

    listButton.addEventListener('click', function () {
        listButton.classList.add('active');
        gridButton.classList.remove('active');
        passwordList.style.display = 'flex';
        passwordGrid.style.display = 'none';
        updatePagination();
    });

    // New functionality: Close dropdowns on screen resize
    window.addEventListener('resize', function () {
        document.querySelectorAll('.card-actions-dropdown.active').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });
    /*============ View Toggle Functionality End Here ============*/

    /*============ Copy Button Functionality Start Here ============*/
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', function () {
            const passwordField = button.closest('.password-field');
            const actualPassword = passwordField.getAttribute('data-password');

            // Use the Clipboard API to copy the password
            navigator.clipboard.writeText(actualPassword).then(() => {
                $.notify("Password copied to clipboard!", "success");
            }).catch(err => {
                console.error('Failed to copy password: ', err);
                $.notify("Failed to copy password.", "error");
            });
        });
    });
    /*============ Copy Button Functionality End Here ============*/

    /*============ Simulate Password Reveal Start Here ============*/
    document.querySelectorAll('.password-field').forEach(field => {
        field.addEventListener('click', function (e) {
            if (!e.target.closest('.copy-btn')) {
                const dots = field.querySelector('.password-dots');
                const actualPassword = field.getAttribute('data-password'); // Fetch the actual password from a data attribute

                if (dots.textContent.includes('•')) {
                    dots.textContent = actualPassword; // Display the actual password
                    setTimeout(() => {
                        dots.textContent = "••••••••••••"; // Revert back to dots after 3 seconds
                    }, 3000);
                }
            }
        });
    });
    /*============ Simulate Password Reveal End Here ============*/

    /*============ Toggle Collapsible Content Start Here ============*/
    document.querySelector('.add-button').addEventListener('click', function () {
        const collapsibleContent = document.querySelector('.collapsible-content');
        if (collapsibleContent.style.maxHeight) {
            collapsibleContent.style.maxHeight = null;
        } else {
            collapsibleContent.style.maxHeight = collapsibleContent.scrollHeight + "px";
        }
    });
    /*============ Toggle Collapsible Content End Here ============*/

    /*============ Toggle Password Visibility Start Here ============*/
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const icon = togglePassword.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
    });
    /*============ Toggle Password Visibility End Here ============*/

    /*============ Toggle Profile Dropdown Start Here ============*/
    const profileButton = document.querySelector('.profile');
    const profileDropdown = document.querySelector('.profile-dropdown');

    profileButton.addEventListener('click', (e) => {
        e.stopPropagation();
        profileDropdown.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (profileDropdown.classList.contains('active') &&
            !profileDropdown.contains(e.target) &&
            !profileButton.contains(e.target)) {
            profileDropdown.classList.remove('active');
        }
    });
    /*============ Toggle Profile Dropdown End Here ============*/

    /*============ Toggle Icon Input Start Here ============*/
    document.getElementById('toggleIconInput').addEventListener('click', function (event) {
        event.preventDefault();
        var fileInput = document.getElementById('iconUpload');
        var textInput = document.getElementById('iconUrl');
        if (fileInput.style.display === 'none') {
            fileInput.style.display = 'block';
            textInput.style.display = 'none';
        } else {
            fileInput.style.display = 'none';
            textInput.style.display = 'block';
        }
    });
    /*============ Toggle Icon Input End Here ============*/

    /* ============ Password Detail Modal Functionality Start Here ============ */
    function setupListItemClickHandlers() {
        document.querySelectorAll('.password-card').forEach(card => {
            card.addEventListener('click', function (e) {
                if (!e.target.closest('.card-actions') && !e.target.closest('.password-field')) {
                    const passwordId = this.getAttribute('data-id');
                    openPasswordDetailModal(passwordId);
                }
            });
        });
    }

    function openPasswordDetailModal(passwordId) {
        console.log("Opening modal for password ID:", passwordId);
        // Fetch password details using AJAX
        fetch('includes/get_password_details.php?id=' + passwordId)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    populatePasswordDetailModal(data.password);
                    document.getElementById('passwordDetailModal').classList.add('active');
                    document.getElementById('editPasswordBtn').setAttribute('data-id', passwordId);
                } else {
                    $.notify(data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.notify("An error occurred while fetching password details.", "error");
            });
    }

    function populatePasswordDetailModal(password) {
        const content = document.getElementById('passwordDetailContent');

        // Create HTML for the password details
        let html = `
        <div class="detail-modal-content">
            <div class="detail-header">
                <div class="detail-icon">
                    <i class="bi bi-${password.icon_file_name}"></i>
                </div>
                <div class="detail-title">
                    <h3>${password.website_name}</h3>
                    ${password.folder_name ? `<div class="folder-badge"><i class="bi bi-folder"></i> ${password.folder_name}</div>` : ''}
                </div>
            </div>
            
            <div class="detail-grid">
                <div class="detail-row">
                    <div class="detail-label">Username:</div>
                    <div class="detail-value">${password.username}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Password:</div>
                    <div class="detail-value">
                        <div class="password-field" data-password="${password.password}">
                            <span class="password-dots">••••••••••••</span>
                            <button class="copy-btn">
                                <i class="bi bi-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Website URL:</div>
                    <div class="detail-value">
                        <a href="${password.website_url}" target="_blank">${password.website_url}</a>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Notes:</div>
                    <div class="detail-value">${password.notes || 'No notes available'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Created:</div>
                    <div class="detail-value">${password.date_created || 'Not available'}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Last Updated:</div>
                    <div class="detail-value">${password.updated_at || 'Not available'}</div>
                </div>
            </div>
        </div>
    `;

        content.innerHTML = html;

        // Setup copy button functionality
        setupCopyButtonInModal();

        // Setup password reveal functionality
        setupPasswordRevealInModal();
    }

    function setupCopyButtonInModal() {
        document.querySelectorAll('#passwordDetailContent .copy-btn').forEach(button => {
            button.addEventListener('click', function () {
                const passwordField = this.closest('.password-field');
                const actualPassword = passwordField.getAttribute('data-password');

                navigator.clipboard.writeText(actualPassword).then(() => {
                    $.notify("Password copied to clipboard!", "success");
                }).catch(err => {
                    console.error('Failed to copy password: ', err);
                    $.notify("Failed to copy password.", "error");
                });
            });
        });
    }

    function setupPasswordRevealInModal() {
        document.querySelectorAll('#passwordDetailContent .password-field').forEach(field => {
            field.addEventListener('click', function (e) {
                if (!e.target.closest('.copy-btn')) {
                    const dots = field.querySelector('.password-dots');
                    const actualPassword = field.getAttribute('data-password');

                    if (dots.textContent.includes('•')) {
                        dots.textContent = actualPassword;
                        setTimeout(() => {
                            dots.textContent = "••••••••••••";
                        }, 3000);
                    }
                }
            });
        });
    }

    // Close the detail modal functions
    function setupDetailModalCloseHandlers() {
        const closeDetailModalButton = document.getElementById('closeDetailModal');
        const closeDetailBtn = document.getElementById('closeDetailBtn');
        const passwordDetailModal = document.getElementById('passwordDetailModal');

        closeDetailModalButton.addEventListener('click', closeDetailModal);
        closeDetailBtn.addEventListener('click', closeDetailModal);

        passwordDetailModal.addEventListener('click', function (event) {
            if (event.target === passwordDetailModal) {
                closeDetailModal();
            }
        });

        // Edit button handler
        document.getElementById('editPasswordBtn').addEventListener('click', function () {
            const passwordId = this.getAttribute('data-id');
            // Close this modal
            closeDetailModal();
            // Open edit modal with this ID
            openEditPasswordModal(passwordId);
        });
    }

    function closeDetailModal() {
        const passwordDetailModal = document.getElementById('passwordDetailModal');
        passwordDetailModal.classList.remove('active');
    }

    // Function to open edit modal (you can implement this later)
    function openEditPasswordModal(passwordId) {
        // This would be implemented separately to open the edit modal
        // For now, we'll just show a notification
        $.notify("Edit functionality would open here for password ID: " + passwordId, "info");
    }
    // Initialize all the modal functionality
    setupDetailModalCloseHandlers();

    /* ============ Password Detail Modal Functionality End Here ============ */

    // New code for menu icon buttons and dropdowns
    // Get all menu icon buttons
    const menuButtons = document.querySelectorAll('.menu-icon-btn');

    // Add click event to each menu button
    menuButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent event from bubbling up

            // Find the dropdown for this specific card
            const dropdown = this.parentNode.querySelector('.card-actions-dropdown');

            // Toggle the active class for this dropdown
            dropdown.classList.toggle('active');

            // Close other open dropdowns
            document.querySelectorAll('.card-actions-dropdown.active').forEach(openDropdown => {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('active');
                }
            });
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function () {
        document.querySelectorAll('.card-actions-dropdown.active').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });

    // Add click event to each dropdown action
    document.querySelectorAll('.card-dropdown-action').forEach(action => {
        action.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent event from bubbling up

            // Get the action type from the title attribute
            const actionType = this.getAttribute('title');

            // Get the card ID from the parent card
            const cardId = this.closest('.password-card, .password-list-item').getAttribute('data-id');

            // Handle the action based on type
            console.log(`Action ${actionType} clicked for card ${cardId}`);

            // If the action is "View", open the password detail modal
            if (actionType === "View") {
                openPasswordDetailModal(cardId);
            }

            // If the action is "Edit", open the edit password modal
            if (actionType === "Edit") {
                openEditPasswordModal(cardId);
            }

            if (actionType === "Delete") {
                openDeleteModal(cardId);
            }

            // Close the dropdown after action
            this.closest('.card-actions-dropdown').classList.remove('active');
        });
    });

    /* ============ Eye Button Functionality Start Here ============ */
    function setupEyeButtonClickHandlers() {
        document.querySelectorAll('.action-btn[title="Eye"]').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevent event from bubbling up

                // Get the card ID from the parent card
                const cardId = this.closest('.password-list-item').getAttribute('data-id');

                // Open the password detail modal with this ID
                openPasswordDetailModal(cardId);
            });
        });
    }

    // Call this function to set up the event listeners
    setupEyeButtonClickHandlers();

    // Ensure the "View" button in both grid and list views can open the password detail modal
    function setupViewButtonClickHandlers() {
        // For grid view
        document.querySelectorAll('.password-card .action-btn[title="Eye"]').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevent event from bubbling up
                const cardId = this.closest('.password-card').getAttribute('data-id');
                openPasswordDetailModal(cardId);
            });
        });

        // For list view
        document.querySelectorAll('.password-list-item .action-btn[title="Eye"]').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation(); // Prevent event from bubbling up
                const listItemId = this.closest('.password-list-item').getAttribute('data-id');
                openPasswordDetailModal(listItemId);
            });
        });
    }

    // Call this function to set up the event listeners
    setupViewButtonClickHandlers();

    /* ============ Eye Button Functionality End Here ============ */


    /*============ Edit Password Modal Functionality Start Here ============*/
    const editPasswordModal = document.getElementById('editPasswordModal');
    const closeEditPasswordModal = document.getElementById('closeEditPasswordModal');
    const cancelEditButton = document.getElementById('cancelEditPasswordBtn');
    const saveEditButton = document.querySelector('#editPasswordModal .save-button');

    // Function to open the edit modal
    function openEditPasswordModal(passwordId) {
        console.log("Opening edit modal for password ID:", passwordId);
        // Fetch password details using AJAX
        fetch('includes/get_password_details.php?id=' + passwordId)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    populateEditPasswordModal(data.password);
                    editPasswordModal.classList.add('active');
                } else {
                    $.notify(data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.notify("An error occurred while fetching password details.", "error");
            });
    }

    // Function to populate the edit modal with password details
    function populateEditPasswordModal(password) {
        document.getElementById('editWebsiteName').value = password.website_name;
        document.getElementById('editUsername').value = password.username;
        document.getElementById('editPassword').value = password.password;
        document.getElementById('editWebsiteUrl').value = password.website_url;
        document.getElementById('editFolder').value = password.folder;
        document.getElementById('editNotes').value = password.notes || '';

        // Check icon_option and display icon_file_name accordingly
        if (password.icon_option === 2) {
            document.getElementById('editIconUrl').value = password.icon_file_name;
            document.getElementById('editIconUrl').style.display = 'block';
            document.getElementById('editIconUpload').style.display = 'none';
        } else {
            document.getElementById('editIconUpload').style.display = 'block';
            document.getElementById('editIconUrl').style.display = 'none';
        }

        // Store the password ID in a hidden input or as a data attribute
        saveEditButton.setAttribute('data-id', password.id);
    }


    // Close the edit modal
    function closeEditModal() {
        editPasswordModal.classList.remove('active');
    }

    closeEditPasswordModal.addEventListener('click', closeEditModal);
    cancelEditButton.addEventListener('click', closeEditModal);

    editPasswordModal.addEventListener('click', function (event) {
        if (event.target === editPasswordModal) {
            closeEditModal();
        }
    });

    // Add event listener to pencil icon buttons
    document.querySelectorAll('.action-btn[title="Edit"]').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent event from bubbling up

            // Get the card ID from the parent card
            const cardId = this.closest('.password-card, .password-list-item').getAttribute('data-id');

            // Open the edit modal with this ID
            openEditPasswordModal(cardId);
        });
    });

    // Save changes and update the password
    saveEditButton.addEventListener('click', function (event) {
        event.preventDefault();

        const passwordId = this.getAttribute('data-id');
        const formData = new FormData(document.getElementById('editPasswordForm'));
        formData.append('id', passwordId);

        if (!formData.get('editWebsiteName') || !formData.get('editUsername') || !formData.get('editPassword')) {
            $.notify("Please fill out all required fields", "error");
            return;
        }

        fetch('update_password.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $.notify(data.message, "success");
                    closeEditModal();
                    refreshPasswordList(); // Refresh the list after updating
                } else {
                    $.notify(data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                $.notify("An error occurred while updating the password.", "error");
            });

    });
    /*============ Edit Password Modal Functionality End Here ============*/

    /*============ Toggle Edit Icon Input Start Here ============*/
    document.getElementById('toggleEditIconInput').addEventListener('click', function (event) {
        event.preventDefault();
        var fileInput = document.getElementById('editIconUpload');
        var textInput = document.getElementById('editIconUrl');
        if (fileInput.style.display === 'none') {
            fileInput.style.display = 'block';
            textInput.style.display = 'none';
        } else {
            fileInput.style.display = 'none';
            textInput.style.display = 'block';
        }
    });
    /*============ Toggle Edit Icon Input End Here ============*/


    /*============ Grid Button Click Event Start ============*/
    gridButton.addEventListener('click', function () {
        gridButton.classList.add('active');
        listButton.classList.remove('active');
        passwordGrid.style.display = 'grid';
        passwordList.style.display = 'none';

        // Update display option to grid (0)
        updateDisplayOption(0);
    });
    /*============ Grid Button Click Event End ============*/

    /*============ List Button Click Event Start ============*/
    listButton.addEventListener('click', function () {
        listButton.classList.add('active');
        gridButton.classList.remove('active');
        passwordList.style.display = 'flex';
        passwordGrid.style.display = 'none';

        // Update display option to list (1)
        updateDisplayOption(1);
    });
    /*============ List Button Click Event End ============*/

    /*============ Update Display Option Function Start ============*/
    function updateDisplayOption(option) {
        fetch('includes/update_display_option.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `option=${option}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    console.error('Failed to update display option:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    /*============ Update Display Option Function End ============*/


    /*============ Refresh Password List Function Start ============*/
    function refreshPasswordList() {
        fetch('includes/get_passwords.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    let passwords = data.passwords;
    
                    // Sort passwords alphabetically by website name
                    passwords.sort((a, b) => {
                        return a.website_name.localeCompare(b.website_name);
                    });
    
                    const passwordGrid = document.querySelector('.password-grid');
                    const passwordList = document.querySelector('.password-list');
    
                    console.log('Fetched and sorted passwords: ', passwords); // Debug: Check the fetched and sorted data
    
                    // Clear existing items to prevent duplicates
                    passwordGrid.innerHTML = '';
                    passwordList.innerHTML = '';
    
                    // Populate grid and list views with sorted data
                    passwords.forEach(row => {
                        console.log('Processing row: ', row); // Debug: Check each loop iteration
    
                        const passwordCard = createPasswordCard(row);
                        const passwordListItem = createPasswordListItem(row);
    
                        passwordGrid.appendChild(passwordCard);
                        passwordList.appendChild(passwordListItem);
                    });
    
                    // Re-setup event handlers for all functionalities
                    setupListItemClickHandlers();
                    setupEyeButtonClickHandlers();
                    setupViewButtonClickHandlers();
                    setupEditButtonClickHandlers();
                    setupDeleteButtonClickHandlers();
                    setupFavoritesButtonClickHandlers();
                    updatePagination();
                } else {
                    console.error('Failed to fetch passwords:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Expose the function globally
    window.refreshPasswordList = refreshPasswordList;

    // Function setups are maintained to seamlessly attach to current DOM.

    function setupEditButtonClickHandlers() {
        document.querySelectorAll('.action-btn[title="Edit"]').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                const cardId = this.closest('.password-card, .password-list-item').getAttribute('data-id');
                openEditPasswordModal(cardId);
            });
        });
    }

    function setupDeleteButtonClickHandlers() {
        document.querySelectorAll('.action-btn[title="Delete"]').forEach(button => {
            button.addEventListener('click', function (e) {
                e.stopPropagation();
                const passwordId = this.closest('.password-card, .password-list-item').getAttribute('data-id');
                openDeleteModal(passwordId);
            });
        });
    }

    function setupFavoritesButtonClickHandlers() {
        document.querySelectorAll('.action-btn[title="Add to favorites"]').forEach(button => {
            button.addEventListener('click', function () {
                const passwordId = this.closest('.password-card, .password-list-item').getAttribute('data-id');
                const iconElement = this.querySelector('i');

                if (iconElement.classList.contains('bi-star-fill')) {
                    removeFromFavorites(passwordId, iconElement);
                } else {
                    addToFavorites(passwordId, iconElement);
                }
            });
        });
    }

    setupEditButtonClickHandlers();
    setupDeleteButtonClickHandlers();
    setupFavoritesButtonClickHandlers();

    function createPasswordCard(row) {
        const card = document.createElement('div');
        card.className = 'password-card';
        card.setAttribute('data-id', row.id);
        card.innerHTML = `
            <div class="card-header">
                <div class="password-icon">
                    <i class="bi bi-${row.icon_file_name}"></i>
                </div>
                ${row.folder_name ? `<div class="folder-badge"><i class="bi bi-folder"></i> ${row.folder_name}</div>` : ''}
                <button class="menu-icon-btn" title="Menu">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <div class="card-actions-dropdown">
                    <div class="card-dropdown-action" title="View"><i class="bi bi-eye"></i> View</div>
                    <div class="card-dropdown-action" title="Edit"><i class="bi bi-pencil"></i> Update</div>
                    <div class="card-dropdown-action" title="Add to favorites"><i class="bi bi-star"></i> Add to favorites</div>
                    <div class="card-dropdown-action" title="Delete"><i class="bi bi-trash"></i> Delete</div>
                </div>
            </div>
            <div class="card-title">
                <h3>${row.website_name}</h3>
                <p>${row.username}</p>
            </div>
            <div class="password-details">
                <div class="detail-item">
                    <div class="detail-label">Username:</div>
                    <div class="detail-value">${row.username}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Password:</div>
                    <div class="detail-value">
                        <div class="password-field" data-password="${row.password}">
                            <span class="password-dots">••••••••••••</span>
                            <button class="copy-btn"><i class="bi bi-copy"></i></button>
                        </div>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Website:</div>
                    <div class="detail-value">${row.website_url}</div>
                </div>
            </div>
            <div class="card-actions">
                <button class="action-btn" title="Eye"><i class="bi bi-eye"></i></button>
                <button class="action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="action-btn" title="Add to favorites"><i class="bi bi-star"></i></button>
                <button class="action-btn" title="Delete"><i class="bi bi-trash"></i></button>
            </div>
        `;
        return card;
    }

    function createPasswordListItem(row) {
        const listItem = document.createElement('div');
        listItem.className = 'password-list-item';
        listItem.setAttribute('data-id', row.id);
        listItem.innerHTML = `
            <div class="list-item-content">
                <div class="list-item-icon">
                    <i class="bi bi-${row.icon_file_name}"></i>
                </div>
                <div class="list-item-details">
                    <h3>${row.website_name}</h3>
                    <p>${row.username}</p>
                </div>
            </div>
            <div class="list-item-actions desktop-only">
                <button class="action-btn" title="Eye"><i class="bi bi-eye"></i></button>
                <button class="action-btn" title="Edit"><i class="bi bi-pencil"></i></button>
                <button class="action-btn" title="Add to favorites"><i class="bi bi-star"></i></button>
                <button class="action-btn" title="Delete"><i class="bi bi-trash"></i></button>
            </div>
            <button class="menu-icon-btn mobile-only" title="Menu">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <div class="card-actions-dropdown">
                <div class="card-dropdown-action" title="View"><i class="bi bi-eye"></i> View</div>
                <div class="card-dropdown-action" title="Edit"><i class="bi bi-pencil"></i> Update</div>
                <div class="card-dropdown-action" title="Add to favorites"><i class="bi bi-star"></i> Add to favorites</div>
                <div class="card-dropdown-action" title="Delete"><i class="bi bi-trash"></i> Delete</div>
            </div>
        `;
        return listItem;
    }
    /*============ Refresh Password List Function End ============*/

    /*============ Pagination Logic Start Here ============*/
    const itemsPerPageGrid = 6;
    const itemsPerPageList = 9;
    let currentPage = 1;
    let totalItems = 0;
    let totalPages = 0;

    function updatePagination() {
        const isGridView = passwordGrid.style.display === 'grid';
        const itemsPerPage = isGridView ? itemsPerPageGrid : itemsPerPageList;
        const items = isGridView ? passwordGrid.children : passwordList.children;

        totalItems = items.length;
        totalPages = Math.ceil(totalItems / itemsPerPage);

        // Hide all items
        Array.from(items).forEach(item => item.style.display = 'none');

        // Show items for the current page
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        Array.from(items).slice(start, end).forEach(item => item.style.display = '');

        // Update pagination controls
        document.querySelector('.pagination-info').textContent = `Page ${currentPage} of ${totalPages}`;
        document.querySelector('.prev-page').disabled = currentPage === 1;
        document.querySelector('.next-page').disabled = currentPage === totalPages;
    }

    function setupPaginationControls() {
        const paginationContainer = document.createElement('div');
        paginationContainer.className = 'pagination-controls';
        paginationContainer.innerHTML = `
            <button class="prev-page">Previous</button>
            <span class="pagination-info"></span>
            <button class="next-page">Next</button>
        `;

        document.querySelector('.content').appendChild(paginationContainer);

        document.querySelector('.prev-page').addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
            }
        });

        document.querySelector('.next-page').addEventListener('click', function () {
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
            }
        });
    }

    // Initialize pagination
    setupPaginationControls();
    updatePagination();
    /*============ Pagination Logic End Here ============*/


    /*============ Delete Logic Start Here ============*/
    // Function to open the delete confirmation modal
    function openDeleteModal(passwordId) {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.classList.add('active');

        // Attach passwordId to confirm button's dataset
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        confirmDeleteButton.setAttribute('data-id', passwordId);
    }

    // Function to close the delete modal
    function closeDeleteModal() {
        const deleteModal = document.getElementById('deleteModal');
        deleteModal.classList.remove('active');
    }

    // Handling the delete confirmation
    document.getElementById('confirmDeleteButton').addEventListener('click', function () {
        const passwordId = this.getAttribute('data-id');

        // Make an AJAX request to delete the item
        fetch('includes/delete_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `password_id=${passwordId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    $.notify(data.message, "success");
                    refreshPasswordList();
                } else {
                    $.notify(data.message, "error");
                }
                closeDeleteModal();
            })
            .catch(error => {
                console.error('Error:', error);
                $.notify("An error occurred while deleting the password.", "error");
                closeDeleteModal();
            });
    });

    // Close modal event listeners
    document.getElementById('closeDeleteModal').addEventListener('click', closeDeleteModal);

    // Adding event to each trash icon button
    document.querySelectorAll('.action-btn[title="Delete"]').forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation(); // Prevent event from bubbling up

            // Get the card ID from the parent card
            const passwordId = this.closest('.password-card, .password-list-item').getAttribute('data-id');

            // Open the delete modal with this ID
            openDeleteModal(passwordId);
        });
    });

    /*============ Delete Logic End Here ============*/


    // JS snippet for handling adding to favorites
    function addToFavorites(passwordId, iconElement) {
        fetch('includes/add_to_favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `password_id=${passwordId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                $.notify(data.message, "success");
                // Toggles the star icon to filled style
                iconElement.classList.remove('bi-star');
                iconElement.classList.add('bi-star-fill');
            } else {
                $.notify(data.message, "info");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            $.notify("An error occurred while adding to favorites.", "error");
        });
    }

    function removeFromFavorites(passwordId, iconElement) {
      fetch('includes/remove_from_favorites.php', {
          method: 'POST',
          headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: `password_id=${passwordId}`
      })
          .then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  $.notify(data.message, "success");
                  iconElement.classList.remove('bi-star-fill');
                  iconElement.classList.add('bi-star');
              } else {
                  $.notify(data.message, "error");
              }
          })
          .catch(error => {
              console.error('Error:', error);
              $.notify("An error occurred while removing from favorites.", "error");
          });
  }

    fetch('includes/fetch_favorites.php')
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            const favoriteIds = data.data;
            document.querySelectorAll('.password-card, .password-list-item').forEach(item => {
                const passwordId = item.getAttribute('data-id');
                const starIcon = item.querySelector('.action-btn[title="Add to favorites"] i');
                if (favoriteIds.includes(passwordId)) {
                    starIcon.classList.remove('bi-star');
                    starIcon.classList.add('bi-star-fill'); // Fill the star icon for favorites
                }
            });
        } else {
            console.error('Failed to fetch favorites:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    /*============ Favorites Button Click Handler Start ============*/
    function setupFavoritesButtonClickHandlers() {
        document.querySelectorAll('.action-btn[title="Add to favorites"]').forEach(button => {
            button.addEventListener('click', function () {
                const passwordId = this.closest('.password-card, .password-list-item').getAttribute('data-id');
                const iconElement = this.querySelector('i');

                if (iconElement.classList.contains('bi-star-fill')) {
                    removeFromFavorites(passwordId, iconElement);
                } else {
                    addToFavorites(passwordId, iconElement);
                }
            });
        });
    }
    /*============ Favorites Button Click Handler End ============*/

    // Ensure to call the setup function after DOM content is loaded
    document.addEventListener('DOMContentLoaded', () => {
        setupFavoritesButtonClickHandlers();
    });
});




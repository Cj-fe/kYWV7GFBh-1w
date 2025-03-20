
document.addEventListener('DOMContentLoaded', function() {
    /*============ Add Folder Modal Functionality ============*/
    const addFolderModal = document.getElementById('addFolderModal');
    const closeAddFolderModal = document.getElementById('closeAddFolderModal');
    const addFolderButton = document.querySelector('.collapsible-item:nth-child(2)');
    const cancelFolderButton = document.getElementById('cancelFolderBtn');
    const folderForm = document.getElementById('folderForm');

    addFolderButton.addEventListener('click', function() {
      addFolderModal.classList.add('active');
    });

    function closeFolderModal() {
      addFolderModal.classList.remove('active');
    }

    closeAddFolderModal.addEventListener('click', closeFolderModal);
    cancelFolderButton.addEventListener('click', closeFolderModal);

    addFolderModal.addEventListener('click', function(event) {
      if (event.target === addFolderModal) {
        closeFolderModal();
      }
    });

    folderForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const folderName = folderForm.querySelector('input[name="folderName"]').value;

      if (!folderName) {
        $.notify("Folder name is required.", "error");
        return;
      }

      const formData = new FormData(folderForm);

      fetch('includes/insert_folder.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          try {
            const jsonData = JSON.parse(data);
            if (jsonData.status === 'success') {
              $.notify(jsonData.message, "success");
              closeFolderModal();
              folderForm.reset();
              refreshFolderList();
              refreshPasswordList(); // Refresh password list as well
            } else {
              $.notify(jsonData.message, "error");
            }
          } catch (error) {
            console.warn("Non-JSON response or parse error:", error);
            console.log("Response data:", data);
            $.notify("An unexpected server response was received.", "error");
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          $.notify("An error occurred while adding the folder.", "error");
        });
    });

    /*============ Make Folders Clickable ============*/
    function makeFoldersClickable() {
      const folderItems = document.querySelectorAll('.category-item[data-folder-id]');

      folderItems.forEach(folderItem => {
        folderItem.addEventListener('click', function() {
          const folderId = folderItem.dataset.folderId;
          window.location.href = `folder_page.php?id=${folderId}`;
        });
      });
    }

    /*============ Refresh Folder List ============*/
    function refreshFolderList() {
      const categories = document.querySelectorAll('.categories');
      let folderContainer = null;

      categories.forEach(category => {
        if (category.querySelector('h3') && category.querySelector('h3').textContent.trim() === "Folders") {
          folderContainer = category;
        }
      });

      if (!folderContainer) {
        console.error('Folder container does not exist in the DOM.');
        return;
      }

      fetch('includes/get_all_folders.php')
        .then(response => response.json())
        .then(data => {
          folderContainer.innerHTML = '<h3>Folders</h3>';

          if (data.length > 0) {
            data.forEach(folder => {
              const folderItem = document.createElement('div');
              folderItem.className = 'category-item';
              folderItem.dataset.folderId = folder.folder_id;
              folderItem.innerHTML = `
                <i class="bi bi-folder-fill"></i>
                <span>${folder.folder_name}</span>
                <input type="hidden" value="${folder.folder_id}">
                <div class="menu-icon">
                  <i class="bi bi-three-dots-vertical"></i>
                  <div class="dropdown-menu">
                    <div class="dropdown-item">Trash</div>
                    <div class="dropdown-item">Rename</div>
                  </div>
                </div>
              `;

              folderContainer.appendChild(folderItem);
            });
            initializeMenuIcons();
            makeFoldersClickable(); // Add click event for redirection
          } else {
            folderContainer.innerHTML += '<p>No folders available</p>';
          }
        })
        .catch(error => console.error('Error fetching folders:', error));
    }

    /*============ Initialize Menu Icons ============*/
    function initializeMenuIcons() {
      const menuIcons = document.querySelectorAll('.menu-icon');

      menuIcons.forEach(icon => {
        icon.addEventListener('click', function(event) {
          event.stopPropagation();

          document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== this.querySelector('.dropdown-menu')) {
              menu.style.display = 'none';
            }
          });

          const dropdown = this.querySelector('.dropdown-menu');
          dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';

          const renameOption = dropdown.querySelector('.dropdown-item:nth-child(2)');
          renameOption.addEventListener('click', function() {
            const folderId = icon.closest('.category-item').dataset.folderId;
            openEditFolderModal(folderId);
          });
        });
      });

      document.querySelectorAll('.dropdown-item:nth-child(1)').forEach(item => {
        item.addEventListener('click', function() {
          const categoryItem = item.closest('.category-item');
          const folderId = categoryItem.dataset.folderId;
          const folderName = categoryItem.querySelector('span').textContent;
          openDeleteFolderModal(folderId, folderName);
        });
      });
    }

    /*============ Password Modal Functionality ============*/
    const passwordModal = document.getElementById('passwordModal');
    const closePasswordModal = document.getElementById('closePasswordModal');
    const addPasswordButton = document.querySelector('.collapsible-item:first-child');
    const cancelButton = document.querySelector('.cancel-button');

    addPasswordButton.addEventListener('click', function() {
      passwordModal.classList.add('active');
    });

    function closeModal() {
      passwordModal.classList.remove('active');
    }

    closePasswordModal.addEventListener('click', closeModal);
    cancelButton.addEventListener('click', closeModal);

    passwordModal.addEventListener('click', function(event) {
      if (event.target === passwordModal) {
        closeModal();
      }
    });

    const saveButton = document.querySelector('.save-button');
    saveButton.addEventListener('click', function(event) {
      event.preventDefault();

      const formData = new FormData(document.getElementById('passwordForm'));

      if (!formData.get('websiteName') || !formData.get('username') || !formData.get('password')) {
        $.notify("Please fill out all required fields", "error");
        return;
      }

      fetch('insert_password.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            $.notify(data.message, "success");
            closeModal();
            document.getElementById('passwordForm').reset();
            refreshPasswordList();
          } else {
            $.notify(data.message, "error");
          }
        })
        .catch(error => {
          console.error('Error:', error);
          $.notify("An error occurred while saving the password.", "error");
        });
    });

    /*============ Initialize and Set Up ============*/
    function openEditFolderModal(folderId) {
      fetch(`includes/get_folder.php?folder_id=${folderId}`)
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            const folder = data.folder;
            const editFolderModal = document.getElementById('editFolderModal');
            const editFolderInput = document.getElementById('editFolderId');
            const editFolderName = document.getElementById('editFolderName');

            editFolderInput.value = folder.folder_id;
            editFolderName.value = folder.folder_name;

            editFolderModal.classList.add('active');
          } else {
            $.notify(data.message, "error");
          }
        })
        .catch(error => {
          console.error('Error fetching folder data:', error);
          $.notify("An error occurred while retrieving folder data.", "error");
        });
    }

    const editFolderForm = document.getElementById('editFolderForm');
    editFolderForm.addEventListener('submit', function(event) {
      event.preventDefault();

      const formData = new FormData(editFolderForm);

      fetch('includes/update_folder.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            $.notify(data.message, "success");
            closeEditModal();
            editFolderForm.reset();
            refreshFolderList();
            refreshPasswordList(); // Refresh password list
          } else {
            $.notify(data.message, "error");
          }
        })
        .catch(error => {
          console.error('Error updating folder:', error);
          $.notify("An error occurred while updating the folder.", "error");
        });
    });

    const closeEditFolderModal = document.getElementById('closeEditFolderModal');
    const cancelEditFolderBtn = document.getElementById('cancelEditFolderBtn');

    function closeEditModal() {
      document.getElementById('editFolderModal').classList.remove('active');
    }

    closeEditFolderModal.addEventListener('click', closeEditModal);
    cancelEditFolderBtn.addEventListener('click', closeEditModal);

    /*============ Delete Folder Modal Functionality ============*/
    const deleteFolderModal = document.getElementById('deleteFolderModal');
    const deleteFolderForm = document.getElementById('deleteFolderForm');
    const closeDeleteFolderModal = document.getElementById('closeDeleteFolderModal');
    const cancelDeleteFolderBtn = document.getElementById('cancelDeleteFolderBtn');
    const confirmDeleteFolder = document.getElementById('confirmDeleteFolder');

    function openDeleteFolderModal(folderId, folderName) {
      const deleteFolderIdInput = document.getElementById('deleteFolderId');
      const deleteFolderNameDisplay = document.getElementById('deleteFolderName');

      deleteFolderIdInput.value = folderId;
      deleteFolderNameDisplay.textContent = folderName;

      deleteFolderModal.classList.add('active');
    }

    function closeDeleteModal() {
      deleteFolderModal.classList.remove('active');
    }

    closeDeleteFolderModal.addEventListener('click', closeDeleteModal);
    cancelDeleteFolderBtn.addEventListener('click', closeDeleteModal);

    confirmDeleteFolder.addEventListener('click', function(event) {
      event.preventDefault();

      const formData = new FormData(deleteFolderForm);

      fetch('includes/delete_folder.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.status === 'success') {
            $.notify(data.message, "success");
            closeDeleteModal();
            deleteFolderForm.reset();
            refreshFolderList();
            refreshPasswordList(); // Refresh password list
          } else {
            $.notify(data.message, "error");
          }
        })
        .catch(error => {
          console.error('Error deleting folder:', error);
          $.notify("An error occurred while deleting the folder.", "error");
        });
    });

    // Initialize the initial folder list and attach click handlers
    makeFoldersClickable();
    refreshFolderList();
  });

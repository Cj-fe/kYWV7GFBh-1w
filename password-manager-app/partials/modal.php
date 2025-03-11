<div class="modal-overlay" id="passwordModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Add Password</h2>
            <button class="close-button" id="closePasswordModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="passwordForm" enctype="multipart/form-data" method="POST">
                
                    <div class="form-group">
                        <label for="websiteName">Website Name</label>
                        <input type="text" id="websiteName" name="websiteName" placeholder="Enter website name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="username">Username / Email</label>
                        <input type="text" id="username" name="username" placeholder="Enter username or email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password*</label>
                        <div class="password-input-container">
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                            <button type="button" class="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="websiteUrl">Website URL</label>
                        <input type="text" id="websiteUrl" name="websiteUrl" placeholder="Enter website URL">
                    </div>
                    <div class="form-group">
                        <label for="folder">Folder</label>
                        <select id="folder" name="folder">
                            <option value="">Select folder</option>
                            <?php include './includes/folder_options.php'; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="iconUpload">Attach Icon/Image/SVG</label>
                        <div class="input-container">
                            <input type="file" id="iconUpload" name="iconUpload" accept="image/*">
                            <input type="text" id="iconUrl" name="iconUrl" placeholder="Enter icon URL"
                                style="display: none;">
                            <a href="#" id="toggleIconInput">Try another option</a>
                        </div>
                    </div>
                    <!-- New Textarea Input for Additional Notes -->
                    <div class="form-group">
                        <label for="notes">Additional Notes</label>
                        <textarea id="notes" name="notes" placeholder="Enter any additional notes" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="cancel-button" id="cancelPasswordBtn">Cancel</button>
                    <button type="submit" class="save-button">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal-overlay" id="editPasswordModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Edit Password</h2>
            <button class="close-button" id="closeEditPasswordModal">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="editPasswordForm" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                    <label for="editWebsiteName">Website Name</label>
                    <input type="text" id="editWebsiteName" name="editWebsiteName" placeholder="Enter website name" required>
                </div>
                <div class="form-group">
                    <label for="editUsername">Username / Email</label>
                    <input type="text" id="editUsername" name="editUsername" placeholder="Enter username or email" required>
                </div>
                <div class="form-group">
                    <label for="editPassword">Password*</label>
                    <div class="password-input-container">
                        <input type="password" id="editPassword" name="editPassword" placeholder="Enter password" required>
                        <button type="button" class="toggle-password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editWebsiteUrl">Website URL</label>
                    <input type="text" id="editWebsiteUrl" name="editWebsiteUrl" placeholder="Enter website URL">
                </div>
                <div class="form-group">
                    <label for="editFolder">Folder</label>
                    <select id="editFolder" name="editFolder">
                        <option value="">Select folder</option>
                        <?php include './includes/folder_options.php'; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editIconUpload">Attach Icon/Image/SVG</label>
                    <div class="input-container">
                        <input type="file" id="editIconUpload" name="editIconUpload" accept="image/*">
                        <input type="text" id="editIconUrl" name="editIconUrl" placeholder="Enter icon URL" style="display: none;">
                        <a href="#" id="toggleEditIconInput">Try another option</a>
                    </div>
                </div>
                <div class="form-group">
                    <label for="editNotes">Additional Notes</label>
                    <textarea id="editNotes" name="editNotes" placeholder="Enter any additional notes" rows="4"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="cancel-button" id="cancelEditPasswordBtn">Cancel</button>
            <button type="submit" class="save-button">Save</button>
        </div>
    </div>
</div>

<div id="passwordDetailModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Password Details</h2>
            <button id="closeDetailModal" class="close-button">×</button>
        </div>
        <div class="modal-body" id="passwordDetailContent">
            <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
            <button class="cancel-button" id="closeDetailBtn">Close</button>
            <button class="save-button" id="editPasswordBtn">Update</button>
        </div>
    </div>
</div>

<div id="deleteModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <button class="close-button" id="closeDeleteModal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this item?</p>
        </div>
        <div class="modal-footer">
            <button class="save-button" id="confirmDeleteButton">Delete</button>
        </div>
    </div>
</div>
<!-- Add -->
<div class="modal fade" id="profile">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Admin Profile</b></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST"
                    action="update_profile.php?return=<?php echo basename($_SERVER['PHP_SELF']); ?>"
                    enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="username" class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?php echo $user['user']; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firstname" class="col-sm-3 control-label">Firstname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="firstname" name="firstname"
                                value="<?php echo $user['firstname']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="col-sm-3 control-label">Lastname</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="lastname" name="lastname"
                                value="<?php echo $user['lastname']; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="photo" class="col-sm-3 control-label">Photo:</label>
                        <div class="col-sm-9">
                            <input type="file" id="photo" name="photo">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Leave blank to keep current password. Change if necessary">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="curr_password" class="col-sm-3 control-label">Current Password:</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="curr_password" name="curr_password"
                                placeholder="Input current password to save changes" required>
                        </div>
                    </div>
                    <!-- Modified authentication toggle switches -->
                    <div class="form-group">
                        <label for="tfa_switch" class="col-sm-3 control-label">2FA</label>
                        <div class="col-sm-9">
                            <label class="switch">
                                <input type="checkbox" id="tfa_switch" name="lockscreen" <?php echo $user['lockscreen'] ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mfa_switch" class="col-sm-3 control-label">MFA</label>
                        <div class="col-sm-9">
                            <label class="switch">
                                <input type="checkbox" id="mfa_switch" name="mfa" <?php echo $user['mfa'] ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
                <button type="submit" class="btn btn-success btn-flat" name="save"><i class="fa fa-check-square-o"></i>
                    Save</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
   document.addEventListener('DOMContentLoaded', function () {
    // Get references to form elements
    const profileForm = document.querySelector('form');
    const tfaSwitch = document.getElementById('tfa_switch');
    const mfaSwitch = document.getElementById('mfa_switch');
    const saveButton = document.querySelector('button[name="save"]');

    // Initial states storage
    const initialStates = {
        tfa: tfaSwitch.checked,
        mfa: mfaSwitch.checked
    };

    // Function to create and show notification
    function showNotification(message, type = 'info') {
        // Remove any existing notifications
        const existingNotification = document.querySelector('.auth-notification');
        if (existingNotification) {
            existingNotification.remove();
        }

        // Create notification container if it doesn't exist
        let notificationContainer = document.querySelector('.notification-container');
        if (!notificationContainer) {
            notificationContainer = document.createElement('div');
            notificationContainer.className = 'notification-container';
            document.body.appendChild(notificationContainer);
        }

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `auth-notification ${type}`;

        // Create notification content
        const notificationContent = document.createElement('div');
        notificationContent.className = 'notification-content';
        notificationContent.textContent = message;

        // Create close button
        const closeButton = document.createElement('button');
        closeButton.className = 'notification-close';
        closeButton.innerHTML = '&times;';
        closeButton.onclick = () => notification.remove();

        // Assemble notification
        notification.appendChild(notificationContent);
        notification.appendChild(closeButton);

        // Add notification to container
        notificationContainer.appendChild(notification);

        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Function to handle switch toggle
    function handleSwitchToggle(activeSwitch, inactiveSwitch, activeName) {
        try {
            // If turning on the active switch
            if (activeSwitch.checked) {
                // Show confirmation dialog
                const confirmed = window.confirm(
                    `Enabling ${activeName} will disable the other authentication method. Continue?`
                );

                if (confirmed) {
                    // Turn off the other switch
                    inactiveSwitch.checked = false;
                    showNotification(`${activeName} enabled successfully`, 'success');
                } else {
                    // Revert the change if user cancels
                    activeSwitch.checked = false;
                    return;
                }
            }

            // Update form state
            updateFormState();
        } catch (error) {
            console.error('Error in handleSwitchToggle:', error);
            showNotification('An error occurred while updating authentication settings', 'error');
            // Revert to initial states in case of error
            activeSwitch.checked = initialStates[activeName.toLowerCase()];
            inactiveSwitch.checked = initialStates[activeName.toLowerCase() === 'tfa' ? 'mfa' : 'tfa'];
        }
    }

    // Function to update form state
    function updateFormState() {
        // Check if any changes were made
        const hasChanges = (
            tfaSwitch.checked !== initialStates.tfa ||
            mfaSwitch.checked !== initialStates.mfa
        );

        // Enable/disable save button based on changes
        saveButton.disabled = !hasChanges;

        // Update button appearance
        if (hasChanges) {
            saveButton.classList.add('btn-primary');
            saveButton.classList.remove('btn-default');
        } else {
            saveButton.classList.remove('btn-primary');
            saveButton.classList.add('btn-default');
        }
    }

    // Add event listeners to switches
    tfaSwitch.addEventListener('change', function () {
        handleSwitchToggle(tfaSwitch, mfaSwitch, 'TFA');
    });

    mfaSwitch.addEventListener('change', function () {
        handleSwitchToggle(mfaSwitch, tfaSwitch, 'MFA');
    });

    // Form submission handler
    profileForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        try {
            // Get current password value
            const currentPassword = document.getElementById('curr_password').value;
            if (!currentPassword) {
                showNotification('Please enter your current password to save changes', 'error');
                return;
            }

            // Create FormData object
            const formData = new FormData(this);

            // Show loading state
            saveButton.disabled = true;
            saveButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

            // Submit the form
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });

            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                throw new Error('Server returned non-JSON response');
            }

            if (result.success) {
                showNotification(result.message, 'success');

                // Update initial states
                initialStates.tfa = tfaSwitch.checked;
                initialStates.mfa = mfaSwitch.checked;

                // Update form state
                updateFormState();

                // Close modal if exists
                const modal = document.getElementById('profile');
                if (modal && typeof $(modal).modal === 'function') {
                    $(modal).modal('hide');
                }

                // Optional: Reload page after successful update
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(result.message || 'Failed to save settings');
            }
        } catch (error) {
            console.error('Error submitting form:', error);
            showNotification(error.message || 'Failed to save settings', 'error');
        } finally {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = '<i class="fa fa-check-square-o"></i> Save';
        }
    });


    document.head.appendChild(style);
});
</script>
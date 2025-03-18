document.addEventListener('DOMContentLoaded', function() {
    const passwordToggle = document.getElementById('password-toggle');
    const passwordInput = document.getElementById('master-password');
    const biometricButton = document.getElementById('biometric-button');
    const fingerprintButton = document.getElementById('fingerprint-button');
    const scanAnimation = document.getElementById('scan-animation');
    const scanMessage = document.getElementById('scan-message');
    const notification = document.getElementById('notification');
    const loginForm = document.getElementById('login-form');
    
    // Toggle password visibility
    passwordToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                    <path d="M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"></path>
                    <path d="m3 3 18 18"></path>
                </svg>
            `;
        } else {
            passwordInput.type = 'password';
            passwordToggle.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            `;
        }
    });

    // Check if WebAuthn is supported
    function isWebAuthnSupported() {
        return window.PublicKeyCredential !== undefined && 
               typeof window.PublicKeyCredential === 'function';
    }

    // Check if fingerprint authentication is available
    function isFingerprintAvailable() {
        if (!isWebAuthnSupported()) {
            return Promise.resolve(false);
        }
        
        return window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable()
            .then(result => {
                return result;
            })
            .catch(error => {
                console.error('Error checking fingerprint availability:', error);
                return false;
            });
    }

    // Initialize UI based on biometric availability
    isFingerprintAvailable().then(available => {
        if (!available) {
            biometricButton.disabled = true;
            biometricButton.title = "Fingerprint authentication not available on this device";
            fingerprintButton.disabled = true;
            fingerprintButton.title = "Fingerprint authentication not available on this device";
        }
    });

    // Show notification
    function showNotification(message, type) {
        notification.textContent = message;
        notification.className = 'notification ' + type;
        setTimeout(() => {
            notification.className = 'notification';
        }, 5000);
    }

    // Handle fingerprint scan
    function handleFingerprintScan() {
        // Check if fingerprint is available first
        isFingerprintAvailable().then(available => {
            if (!available) {
                showNotification("Fingerprint authentication is not available on this device.", "error");
                return;
            }

            // Show scanning animation
            scanAnimation.classList.add('active');
            scanMessage.classList.add('active');
            
            // Create credential options for WebAuthn
            const publicKeyCredentialRequestOptions = {
                challenge: new Uint8Array([1, 2, 3, 4, 5, 6, 7, 8]), // Would be a random challenge in production
                allowCredentials: [], // Allow any registered credential
                userVerification: 'required',
                timeout: 60000
            };

            // Simulate WebAuthn authentication
            // In a real implementation, you would use navigator.credentials.get()
            setTimeout(() => {
                // Simulate successful authentication
                scanAnimation.classList.remove('active');
                scanMessage.classList.remove('active');
                
                // Show success message
                showNotification("Fingerprint recognized. Logging you in...", "success");
                
                // Redirect to dashboard (simulated)
                setTimeout(() => {
                    // This would redirect to your dashboard
                    showNotification("Authentication successful!", "success");
                }, 2000);
            }, 3000);
            
            // In a real implementation, you would use something like:
            /*
            navigator.credentials.get({
                publicKey: publicKeyCredentialRequestOptions
            })
            .then(credential => {
                // Process the authentication response
                // Verify on server side
                scanAnimation.classList.remove('active');
                scanMessage.classList.remove('active');
                showNotification("Fingerprint recognized. Logging you in...", "success");
            })
            .catch(error => {
                scanAnimation.classList.remove('active');
                scanMessage.classList.remove('active');
                showNotification("Authentication failed: " + error.message, "error");
            });
            */
        });
    }

    // Event listeners for fingerprint buttons
    biometricButton.addEventListener('click', handleFingerprintScan);
    fingerprintButton.addEventListener('click', handleFingerprintScan);

    // Form submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        // Simulate login with credentials
        showNotification("Logging in with credentials...", "success");
        // In a real app, you would verify credentials here
    });
});
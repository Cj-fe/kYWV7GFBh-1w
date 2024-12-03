<div id="number-auth-modal" class="popup-modal">
    <div class="popup-modal-content">
      <span class="close-button">&times;</span>
      <h2>Number Authentication</h2>
      <p>Please enter the verification code sent to your phone. <?php echo htmlspecialchars($user['phone']); ?></p>
      <div class="input-container">
        <input type="text" id="verification-code" placeholder="Enter code" />
        <button class="btn btn-primary" id="verify-code">Verify</button>
      </div>
    </div>
  </div>
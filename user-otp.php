<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Code Verification</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    .glass-morphism {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
    }

    .gradient-bg {
      background: linear-gradient(120deg, #e0c3fc 0%, #8ec5fc 100%);
      animation: gradientBG 15s ease infinite;
      background-size: 300% 300%;
    }

    .input-glass {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(8px);
      border: 1px solid rgba(192, 192, 192, 0.5);
      transition: all 0.3s ease;
    }

    .input-glass:focus {
      background: rgba(255, 255, 255, 0.95);
      box-shadow: 0 0 0 2px rgba(138, 43, 226, 0.2);
      border-color: rgba(138, 43, 226, 0.5);
    }

    .btn-gradient {
      background: linear-gradient(45deg, #8a2be2, #0000ff);
      transition: all 0.3s ease;
    }

    .btn-gradient:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    @keyframes gradientBG {
      0% {
        background-position: 0% 50%;
      }

      50% {
        background-position: 100% 50%;
      }

      100% {
        background-position: 0% 50%;
      }
    }
  </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
  <!-- Spinner Start -->
  <div id="spinner" class="show bg-white fixed inset-0 flex items-center justify-center">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
  </div>
  <!-- Spinner End -->

  <div class="glass-morphism rounded-3xl w-full max-w-md p-8">
    <div class="flex items-center mb-8">
      <button onclick="window.history.back()" class="text-gray-600 hover:text-gray-800">
        <i class="fas fa-arrow-left"></i>
      </button>
    </div>
    <div class="text-center mb-8">
      <h2 class="text-2xl font-bold text-gray-800">Verify Your Account</h2>
      <p class="text-gray-600 mt-2">Enter the verification code sent to your email</p>
    </div>

    <form method="POST" action="user-otp-action.php" class="space-y-6 unique-form">
      <div class="flex justify-center">
        <input type="text" name="otp" maxlength="6" required class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none" placeholder="Enter 6-digit code" />
      </div>

      <button type="submit" class="w-full btn-gradient text-white py-3 px-4 rounded-xl font-medium" name="check">
        Verify OTP
      </button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Spinner logic
    window.addEventListener('load', function () {
      document.getElementById('spinner').style.display = 'none';
    });

    // Ensure only numeric input
    document.querySelector('input[name="otp"]').addEventListener('input', function (e) {
      e.target.value = e.target.value.replace(/[^0-9]/g, ''); // Allow only numbers
    });

    // SweetAlert2 logic for error messages
    document.addEventListener('DOMContentLoaded', function () {
      <?php if (isset($_GET['error'])): ?>
      let timerInterval;
      Swal.fire({
        title: "Try Again!",
        html: "<?php echo htmlspecialchars($_GET['error']); ?>",
        timer: 4000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          const timer = Swal.getPopup().querySelector("b");
          timerInterval = setInterval(() => {
            timer.textContent = `${Swal.getTimerLeft()}`;
          }, 100);
        },
        willClose: () => {
          clearInterval(timerInterval);
        }
      }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          console.log("Alert was closed by the timer");
        }
      });
      <?php endif; ?>
    });
  </script>
</body>

</html>
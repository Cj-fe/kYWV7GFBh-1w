<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .form-container {
            transition: opacity 0.5s ease-in-out;
        }

        .hidden {
            display: none;
        }

        .visible {
            display: block;
            opacity: 1;
        }

        .colored-toast.swal2-icon-error {
            background-color: rgb(254, 242, 242) !important;
            border-right: 4px solid rgb(239, 68, 68) !important;
            color: rgb(153, 27, 27) !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
        }

        .colored-toast.swal2-icon-success {
            background-color: rgb(240, 253, 244) !important;
            border-right: 4px solid rgb(34, 197, 94) !important;
            color: rgb(22, 101, 52) !important;
        }

        .colored-toast.swal2-icon-warning {
            background-color: rgb(254, 252, 232) !important;
            border-right: 4px solid rgb(234, 179, 8) !important;
            color: rgb(133, 77, 14) !important;
        }

        .colored-toast .swal2-title {
            color: inherit !important;
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
        }

        .swal2-popup.swal2-toast {
            padding: 0.75rem !important;
            width: 24rem !important;
            border-radius: 0.5rem !important;
        }

        .swal2-popup.swal2-toast .swal2-icon {
            display: none !important;
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center p-4">
    <noscript>
        <meta http-equiv="refresh" content="0;url=javascript-disabled.html">
    </noscript>
    <?php
    session_start();
    if (isset($_SESSION['error']) || isset($_SESSION['success'])) {
        $message = isset($_SESSION['error']) ? $_SESSION['error'] : $_SESSION['success'];
        $icon = isset($_SESSION['error']) ? 'error' : 'success';
        unset($_SESSION['error'], $_SESSION['success']);
        ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: '<?php echo $message; ?>',
                    icon: '<?php echo $icon; ?>',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'colored-toast',
                        title: 'swal2-title'
                    },
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            });
        </script>
        <?php
    }
    ?>

    <div id="loginForm" class="glass-morphism rounded-3xl w-full max-w-md p-8 form-container visible">
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4">
                <img src="../images/logo/fb.png" alt="Profile" class="w-100 h-20 rounded-full">
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Welcome back</h2>
            <p class="text-gray-600 mt-2">Enter your credentials to access your account</p>
        </div>

        <form method="POST" action="../login.php" class="space-y-6">
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none" placeholder="name@example.com"
                    required>
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none"
                        placeholder="Enter your password" required>
                    <button type="button" onclick="togglePassword('password', 'togglePassword')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        aria-label="Toggle password visibility">
                        <i class="fas fa-eye" id="togglePassword"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full btn-gradient text-white py-3 px-4 rounded-xl font-medium" name="login">
                Sign in
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600">
            <div class="mb-2">
                Don't have an account?
                <button onclick="toggleForms('signupForm')" class="font-semibold text-blue-600 hover:text-blue-800">Sign
                    up</button>
            </div>
            <a href="#" class="font-semibold text-blue-600 hover:text-blue-800"
                onclick="toggleForms('forgotPasswordForm')">
                Forgot your password?
            </a>
        </div>
    </div>

    <div id="signupForm" class="glass-morphism rounded-3xl w-full max-w-md p-8 form-container hidden">
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4">
                <img src="../images/logo/fb.png" alt="Profile" class="w-100 h-20 rounded-full">
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Create Account</h2>
            <p class="text-gray-600 mt-2">Enter your alumni information to register</p>
        </div>

        <form method="POST" action="../signup_action.php" class="space-y-4" autocomplete="off">
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label for="schoolId" class="block text-sm font-medium text-gray-700">Alumni ID</label>
                    <input type="text" id="schoolId" name="schoolId"
                        class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none"
                        placeholder="Alumni ID (e.g., 1234-5678)" pattern="\d{4}-\d{4}" maxlength="9" required>
                </div>

                <div class="space-y-2">
                    <label for="lastname" class="block text-sm font-medium text-gray-700">Last Name</label>
                    <input type="text" id="lastname" name="lastname"
                        class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none"
                        placeholder="Enter your last name" required>
                </div>
            </div>

            <div class="space-y-2">
                <label for="signupEmail" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="signupEmail" name="email"
                    class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none" placeholder="name@example.com"
                    required>
            </div>

            <div class="space-y-2">
                <label for="signupPassword" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="signupPassword" name="password"
                        class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none"
                        placeholder="Create your password" required>
                    <button type="button" onclick="togglePassword('signupPassword', 'toggleSignupPassword')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        aria-label="Toggle password visibility">
                        <i class="fas fa-eye" id="toggleSignupPassword"></i>
                    </button>
                </div>
                <progress id="passwordStrength" value="0" max="100" class="w-full h-2 mt-2"></progress>
                <button type="button" onclick="suggestPassword()" class="text-blue-600 hover:text-blue-800 mt-2">
                    Suggest a strong password
                </button>
            </div>

            <div class="space-y-2">
                <label for="curpassword" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="curpassword" name="curpassword"
                        class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none"
                        placeholder="Confirm your password" required>
                    <button type="button" onclick="togglePassword('curpassword', 'toggleConfirmPassword')"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                        aria-label="Toggle password visibility">
                        <i class="fas fa-eye" id="toggleConfirmPassword"></i>
                    </button>
                </div>
            </div>

            <button type="submit" name="signup"
                class="w-full btn-gradient text-white py-3 px-4 rounded-xl font-medium mt-6">
                Sign Up
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            Already have an account?
            <button onclick="toggleForms('loginForm')" class="font-semibold text-blue-600 hover:text-blue-800">Sign
                in</button>
        </div>
    </div>

    <div id="forgotPasswordForm" class="glass-morphism rounded-3xl w-full max-w-md p-8 form-container hidden">
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4">
                <img src="../images/logo/fb.png" alt="Profile" class="w-100 h-20 rounded-full">
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Reset Password</h2>
            <p class="text-gray-600 mt-2">Enter your email to reset your password</p>
        </div>

        <form class="space-y-6" method="POST" action="../controllerUserData.php">
            <div class="space-y-2">
                <label for="resetEmail" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="resetEmail" name="email"
                    class="w-full px-4 py-3 rounded-xl input-glass focus:outline-none" placeholder="Enter your email"
                    required>
            </div>

            <button type="submit" class="w-full btn-gradient text-white py-3 px-4 rounded-xl font-medium"
                name="check-email">
                Reset Password
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <button onclick="toggleForms('loginForm')" class="font-semibold text-blue-600 hover:text-blue-800">Back to
                Sign
                In</button>
        </div>
    </div>

    <script>
        function toggleForms(targetForm) {
            const forms = ['loginForm', 'signupForm', 'forgotPasswordForm'];
            forms.forEach(formId => {
                const form = document.getElementById(formId);
                if (formId === targetForm) {
                    form.classList.remove('hidden');
                    form.classList.add('visible');
                } else {
                    form.classList.remove('visible');
                    form.classList.add('hidden');
                }
            });
        }

        function togglePassword(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(toggleId);

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const loginFormEl = document.querySelector('form[action="../login.php"]');
            loginFormEl.addEventListener('submit', function (e) {
                const email = document.getElementById('email').value;
                const password = document.getElementById('password').value;

                if (!email || !password) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Please fill in all fields',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }
            });

            const signupFormEl = document.querySelector('form[action="../signup_action.php"]');
            signupFormEl.addEventListener('submit', async function (e) {
                e.preventDefault();

                const schoolId = document.getElementById('schoolId').value;
                const lastname = document.getElementById('lastname').value;
                const signupEmail = document.getElementById('signupEmail').value;
                const signupPassword = document.getElementById('signupPassword').value;
                const confirmPassword = document.getElementById('curpassword').value;

                const alumniIdPattern = /^\d{4}-\d{4}$/;
                if (!alumniIdPattern.test(schoolId)) {
                    Swal.fire({
                        title: 'Invalid Alumni ID format. Please use XXXX-XXXX format',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    return;
                }

                if (!schoolId || !lastname || !signupEmail || !signupPassword || !confirmPassword) {
                    Swal.fire({
                        title: 'Please fill in all fields',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    return;
                }

                if (signupPassword !== confirmPassword) {
                    Swal.fire({
                        title: 'Passwords do not match',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    return;
                }

                const submitButton = signupFormEl.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;

                try {
                    const formData = new FormData(signupFormEl);
                    const response = await fetch('../signup_action.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();

                    if (data.error) {
                        Swal.fire({
                            title: data.error,
                            icon: 'error',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'colored-toast',
                                title: 'swal2-title'
                            },
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                    } else if (data.success && data.redirect) {
                        Swal.fire({
                            title: 'Signup successful! Redirecting...',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'colored-toast',
                                title: 'swal2-title'
                            },
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'An error occurred. Please try again.',
                        icon: 'error',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                    console.error('Signup error:', error);
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            });

            const forgotPasswordFormEl = document.querySelector('form[action="../controllerUserData.php"]');
            forgotPasswordFormEl.addEventListener('submit', function (e) {
                const resetEmail = document.getElementById('resetEmail').value;

                if (!resetEmail) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Please enter your email',
                        icon: 'warning',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'colored-toast',
                            title: 'swal2-title'
                        },
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });
                }
            });

            const schoolIdInput = document.getElementById('schoolId');
            schoolIdInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 4) {
                    value = value.slice(0, 4) + '-' + value.slice(4, 8);
                }
                e.target.value = value;
            });

            const signupPassword = document.getElementById('signupPassword');
            const passwordStrength = document.getElementById('passwordStrength');

            signupPassword.addEventListener('input', function () {
                const strength = calculatePasswordStrength(signupPassword.value);
                passwordStrength.value = strength;
                updateProgressBarColor(passwordStrength, strength);
            });

            function calculatePasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength += 20;
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[a-z]/.test(password)) strength += 20;
                if (/\d/.test(password)) strength += 20;
                if (/[\W_]/.test(password)) strength += 20;
                return strength;
            }

            function updateProgressBarColor(progressBar, strength) {
                if (strength < 40) {
                    progressBar.classList.add('bg-red-500');
                    progressBar.classList.remove('bg-yellow-500', 'bg-green-500');
                } else if (strength < 80) {
                    progressBar.classList.add('bg-yellow-500');
                    progressBar.classList.remove('bg-red-500', 'bg-green-500');
                } else {
                    progressBar.classList.add('bg-green-500');
                    progressBar.classList.remove('bg-red-500', 'bg-yellow-500');
                }
            }

            window.suggestPassword = function () {
                const suggestedPassword = generateStrongPassword();
                signupPassword.value = suggestedPassword;
                const strength = calculatePasswordStrength(suggestedPassword);
                passwordStrength.value = strength;
                updateProgressBarColor(passwordStrength, strength);
            };

            function generateStrongPassword() {
                const chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
                let password = "";
                for (let i = 0; i < 12; i++) {
                    password += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                return password;
            }

            const confirmPassword = document.getElementById('curpassword');

            function validatePasswordMatch() {
                if (confirmPassword.value && signupPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('border-red-500');
                    confirmPassword.classList.remove('border-green-500');
                } else if (confirmPassword.value) {
                    confirmPassword.classList.remove('border-red-500');
                    confirmPassword.classList.add('border-green-500');
                } else {
                    confirmPassword.classList.remove('border-red-500', 'border-green-500');
                }
            }

            signupPassword.addEventListener('input', validatePasswordMatch);
            confirmPassword.addEventListener('input', validatePasswordMatch);

            const emailInput = document.getElementById('signupEmail');
            emailInput.addEventListener('input', function () {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.classList.add('border-red-500');
                    this.classList.remove('border-green-500');
                } else if (this.value) {
                    this.classList.remove('border-red-500');
                    this.classList.add('border-green-500');
                } else {
                    this.classList.remove('border-red-500', 'border-green-500');
                }
            });
        });
    </script>
</body>

</html>
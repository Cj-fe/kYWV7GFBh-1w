<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Documentation</title>
    <style>
        :root {
            --primary-color: #0070f3;
            --text-color: #000;
            --text-secondary: #666;
            --bg-color: #fff;
            --sidebar-bg: #fafafa;
            --border-color: #eaeaea;
            --code-bg: #f6f6f6;
            --header-height: 64px;
            --sidebar-width: 300px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: var(--text-color);
            background-color: var(--bg-color);
            overflow-x: hidden;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* Header Styles */
        .header {
            height: var(--header-height);
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--bg-color);
            z-index: 100;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            color: var(--text-color);
        }

        .logo {
            font-weight: 600;
            font-size: 1.25rem;
            color: var(--text-color);
            text-decoration: none;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            padding-top: var(--header-height);
            transition: transform 0.3s ease;
        }

        .sidebar-nav {
            padding: 1.5rem;
        }

        .nav-group {
            margin-bottom: 2rem;
        }

        ol,
        .indented-list {
            margin-left: 20px;
            /* Adjust the value as needed */
            padding-left: 20px;
            /* Adjust the value as needed */
        }

        .indented-list ul {
            margin-left: 20px;
            /* Indent nested lists further */
            padding-left: 20px;
            /* Indent nested lists further */
        }

        .nav-group-title {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
        }

        .nav-items {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: block;
            padding: 0.375rem 0.75rem;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.9375rem;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--primary-color);
            background: rgba(0, 112, 243, 0.1);
        }

        .nav-link.active {
            color: var(--primary-color);
            background: rgba(0, 112, 243, 0.1);
            font-weight: 500;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding-top: var(--header-height);
            transition: margin-left 0.3s ease;
        }

        /* Article Styles */
        .article {
            max-width: 768px;
            margin: 0 auto;
            padding: 2rem;
        }

        .article h1 {
            font-size: 2.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .article h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 2.5rem 0 1rem;
            padding-top: 2.5rem;
            border-top: 1px solid var(--border-color);
        }

        .article h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
        }

        .article p {
            margin-bottom: 1.5rem;
            color: var(--text-secondary);
        }

        .screenshot-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin: 2rem 0;
            width: 100%;
        }

        .screenshot {
            width: 100%;
            height: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin: 0;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .screenshot:hover {
            transform: scale(1.02);
        }

        .bullet {
            padding: 1rem;
            border-radius: 8px;
            line-height: 2.0;
            overflow-x: auto;
        }

        /* Image Viewer Styles */
        .image-viewer-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .viewer-image {
            max-width: 90%;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .close-viewer {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 30px;
            color: white;
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            z-index: 2001;
            transition: transform 0.2s ease;
        }

        .close-viewer:hover {
            transform: scale(1.1);
        }

        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: none;
            padding: 20px 15px;
            cursor: pointer;
            font-size: 20px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .prev-btn {
            left: 30px;
        }

        .next-btn {
            right: 30px;
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
        }

        /* Mobile Styles */
        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: block;
                pointer-events: none;
            }

            .sidebar-overlay.active {
                pointer-events: auto;
            }

            .screenshot-container {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }

    /* Center images when there are only two images */
    .screenshot-container:has(.screenshot:nth-child(2)):not(:has(.screenshot:nth-child(3))) {
        grid-template-columns: repeat(2, 1fr);
        justify-content: center;
    }

            .article {
                padding: 1.5rem;
            }

            .article h1 {
                font-size: 2rem;
            }

            .article h2 {
                font-size: 1.25rem;
            }

            .article h3 {
                font-size: 1.125rem;
            }

            .nav-btn {
                padding: 15px 10px;
                font-size: 16px;
            }

            .prev-btn {
                left: 10px;
            }

            .next-btn {
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" aria-label="Toggle menu">☰</button>
            <a href="#" class="logo">MCC ALUMNI DOCS</a>
        </div>
    </header>

    <div class="sidebar-overlay"></div>

    <div class="layout">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <div class="nav-group">
                    <div class="nav-group-title">Getting Started</div>
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="#introduction" class="nav-link active" data-content="introduction">Introduction</a>
                        </li>
                        <li class="nav-item">
                            <a href="#quickstart" class="nav-link" data-content="quickstart">Quick Start</a>
                        </li>
                        <li class="nav-item">
                            <a href="#installation" class="nav-link" data-content="installation">Installation</a>
                        </li>
                    </ul>
                </div>

                <div class="nav-group">
                    <div class="nav-group-title">Core Concepts</div>
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="#dashboard" class="nav-link" data-content="dashboard">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a href="#projects" class="nav-link" data-content="projects">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a href="#settings" class="nav-link" data-content="settings">Settings</a>
                        </li>
                    </ul>
                </div>

                <div class="nav-group">
                    <div class="nav-group-title">Advanced Features</div>
                    <ul class="nav-items">
                        <li class="nav-item">
                            <a href="#api" class="nav-link" data-content="api">API Reference</a>
                        </li>
                        <li class="nav-item">
                            <a href="#integrations" class="nav-link" data-content="integrations">Integrations</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <main class="main-content">
            <article class="article" id="content">
                <!-- Default content (Introduction) -->
                <h1>Introduction</h1>
                <p>This guide is designed to help you seamlessly navigate and utilize all the features and
                    functionalities of the MCC Alumni Application, a platform exclusively developed for the vibrant and
                    accomplished alumni of Madridejos Community College. Whether you're reconnecting with former
                    classmates, networking with fellow alumni, or staying updated on campus events, this app provides an
                    all-in-one solution to foster a thriving alumni community.</p>

                <div class="screenshot-container">
                    <img src="../images/462587315_568750512637376_3657400423828773038_n.jpg" alt="Dashboard Overview"
                        class="screenshot">
                    <img src="../images/462562498_580301538110408_8486396023419738187_n (1).jpg"
                        alt="Dashboard Overview" class="screenshot">
                    <img src="../images/462586812_950376430310923_8737803355523657319_n.jpg" alt="Dashboard Overview"
                        class="screenshot">
                </div>

                <h2>Who Should Use This Manual?</h2>
                <p>This user manual is for all MCC alumni who have downloaded the application and wish to make the most
                    of its offerings. Whether you're a first-time user or looking for advanced tips, this guide will
                    ensure you have a smooth and enriching experience.</p>

                <h3>Features Covered in This Manual</h3>
                <p>This document includes:</p>
                <div class="bullet">
                    <ul>
                        <li><b>Getting Started: </b>Installation and account setup.</li>
                        <li><b>Navigation Basics:</b> Understanding the app's layout and key functions.</li>
                        <li><b>Troubleshooting and Support:</b> Resolving common issues and reaching out for help.</li>
                    </ul>
                </div>

                <h3>Let's Get Started</h3>
                <p>Dive into the sections ahead to begin your journey with the MCC Alumni Application. Rediscover your
                    connections, explore opportunities, and continue contributing to the legacy of MCC.</p>
            </article>
        </main>
    </div>

    <!-- Image Viewer Overlay -->
    <div class="image-viewer-overlay">
        <button class="close-viewer">&times;</button>
        <button class="nav-btn prev-btn">&lt;</button>
        <button class="nav-btn next-btn">&gt;</button>
        <img src="" alt="Full size image" class="viewer-image">
    </div>

    <script>
        // Mobile menu toggle functionality
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }

        menuToggle.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);

        // Close sidebar when clicking a link (mobile)
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                loadContent(link.dataset.content);
            });
        });

        // Handle resize events
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                body.style.overflow = '';
            }
        });

        // Image Viewer functionality
        const imageViewer = {
            overlay: document.querySelector('.image-viewer-overlay'),
            image: document.querySelector('.viewer-image'),
            closeBtn: document.querySelector('.close-viewer'),
            prevBtn: document.querySelector('.prev-btn'),
            nextBtn: document.querySelector('.next-btn'),
            images: [],
            currentIndex: 0,
            touchStartX: 0,
            touchEndX: 0,

            init() {
                // Get all screenshots
                this.images = Array.from(document.querySelectorAll('.screenshot'));

                // Add click handlers to all screenshots
                this.images.forEach((img, index) => {
                    img.addEventListener('click', () => this.open(index));
                });

                // Add event listeners for controls
                this.closeBtn.addEventListener('click', () => this.close());
                this.prevBtn.addEventListener('click', () => this.navigate(-1));
                this.nextBtn.addEventListener('click', () => this.navigate(1));

                // Add keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (!this.overlay.style.display || this.overlay.style.display === 'none') return;

                    switch (e.key) {
                        case 'Escape':
                            this.close();
                            break;
                        case 'ArrowLeft':
                            this.navigate(-1);
                            break;
                        case 'ArrowRight':
                            this.navigate(1);
                            break;
                    }
                });

                // Add touch events for mobile swipe
                this.overlay.addEventListener('touchstart', (e) => {
                    this.touchStartX = e.touches[0].clientX;
                });

                this.overlay.addEventListener('touchend', (e) => {
                    this.touchEndX = e.changedTouches[0].clientX;
                    this.handleSwipe();
                });

                // Close on overlay click (but not on image click)
                this.overlay.addEventListener('click', (e) => {
                    if (e.target === this.overlay) {
                        this.close();
                    }
                });
            },

            open(index) {
                this.currentIndex = index;
                this.updateImage();
                this.overlay.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            },

            close() {
                this.overlay.style.display = 'none';
                document.body.style.overflow = '';
            },

            navigate(direction) {
                this.currentIndex = (this.currentIndex + direction + this.images.length) % this.images.length;
                this.updateImage();
            },

            updateImage() {
                const currentImg = this.images[this.currentIndex];
                this.image.src = currentImg.src;
                this.image.alt = currentImg.alt;

                // Update navigation button visibility
                this.prevBtn.style.display = this.images.length > 1 ? 'block' : 'none';
                this.nextBtn.style.display = this.images.length > 1 ? 'block' : 'none';
            },

            handleSwipe() {
                const swipeThreshold = 50; // minimum distance for swipe
                const swipeDistance = this.touchEndX - this.touchStartX;

                if (Math.abs(swipeDistance) > swipeThreshold) {
                    if (swipeDistance > 0) {
                        // Swipe right - show previous image
                        this.navigate(-1);
                    } else {
                        // Swipe left - show next image
                        this.navigate(1);
                    }
                }
            }
        };

        // Initialize the image viewer when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            imageViewer.init();
        });

        // Function to load content dynamically
        function loadContent(contentId) {
            const content = document.getElementById('content');
            switch (contentId) {
                case 'introduction':
                    content.innerHTML = `
                        <h1>Introduction</h1>
                        <p>This guide is designed to help you seamlessly navigate and utilize all the features and
                            functionalities of the MCC Alumni Application, a platform exclusively developed for the vibrant and
                            accomplished alumni of Madridejos Community College. Whether you're reconnecting with former
                            classmates, networking with fellow alumni, or staying updated on campus events, this app provides an
                            all-in-one solution to foster a thriving alumni community.</p>
                        <div class="screenshot-container">
                            <img src="../images/462587315_568750512637376_3657400423828773038_n.jpg" alt="Dashboard Overview" class="screenshot">
                            <img src="../images/462562498_580301538110408_8486396023419738187_n (1).jpg" alt="Dashboard Overview" class="screenshot">
                            <img src="../images/462586812_950376430310923_8737803355523657319_n.jpg" alt="Dashboard Overview" class="screenshot">
                        </div>
                        <h2>Who Should Use This Manual?</h2>
                        <p>This user manual is for all MCC alumni who have downloaded the application and wish to make the most
                            of its offerings. Whether you're a first-time user or looking for advanced tips, this guide will
                            ensure you have a smooth and enriching experience.</p>
                        <h3>Features Covered in This Manual</h3>
                        <p>This document includes:</p>
                        <div class="bullet">
                            <ul>
                                <li><b>Getting Started: </b>Installation and account setup.</li>
                                <li><b>Navigation Basics:</b> Understanding the app's layout and key functions.</li>
                                <li><b>Troubleshooting and Support:</b> Resolving common issues and reaching out for help.</li>
                            </ul>
                        </div>
                        <h3>Let's Get Started</h3>
                        <p>Dive into the sections ahead to begin your journey with the MCC Alumni Application. Rediscover your
                            connections, explore opportunities, and continue contributing to the legacy of MCC.</p>
                    `;
                    break;
                case 'quickstart':
                    content.innerHTML = `
                        <h1>Quick Start</h1>
                        <p>Welcome to the Quick Start guide for the MCC Alumni Application. This section will help you get up and running in no time.</p>
                        <h2>Step 1: Download the App</h2>
                        <p>Visit the mccalumnitracker.com to download the MCC Alumni App.</p>
                        <img src="../images/image.png"  class="screenshot">
                        <h2>Step 2: Create an Account</h2>
                        <p>Open the app and follow the on-screen instructions to create your account using your alumni credentials.</p>
                         <div class="screenshot-container">
                         <img src="../images/462585845_616930530821165_1179656745664993019_n.jpg"  class="screenshot">
                         <img src="../images/462564247_973168487991836_3695341457472946874_n.jpg"  class="screenshot">
                          </div>
                        <h2>Step 3: Sign-Up Process</h2>
                        <p> Fill in the Sign-Up Form </p>
                        <p>Enter the following details:</p>
                        <ol class="indented-list">
                            <li><b>Student ID:</b> Your School ID, e.g 2021-1485</li>
                            <li><b>Last Name:</b> Must match the last name associated with the provided Student ID in the MCC database.</li>
                            <li><b>Email Address:</b>  A valid, unused email address for your account.</li>
                            <li><b>Password:</b> Create a secure password (8 characters minimum).</li>
                            <li><b>Confirm Password:</b> Retype the password to confirm accuracy.</li>
                        </ol>
                        <h3>Validation Rules and Error Messages</h3>
                        <ol class="indented-list">
                            <li>
                                <strong>Student ID and Last Name Mismatch:</strong>
                                <ul>
                                <li>If the entered <strong>Student ID</strong> and <strong>Last Name</strong> do not match the records in the MCC database:</li>
                                <li>Error Message: <em>"Student ID and Last Name do not match our records. Please verify your information or contact support."</em></li>
                                </ul>
                            </li>
                            <li>
                                <strong>Email Already Registered:</strong>
                                <ul>
                                <li>If the provided <strong>Email Address</strong> is already in use:</li>
                                <li>Error Message: <em>"This email is already registered. Please use a different email or log in with your existing account."</em></li>
                                </ul>
                            </li>
                            <li>
                                <strong>Password and Confirm Password Mismatch:</strong>
                                <ul>
                                <li>If the <strong>Password</strong> and <strong>Confirm Password</strong> fields do not match:</li>
                                <li>Error Message: <em>"Passwords do not match. Please re-enter your password."</em></li>
                                </ul>
                            </li>
                            <li>
                                <strong>General Field Validation:</strong>
                                <ul>
                                <li>If any required field is left blank:</li>
                                <li>Error Message: <em>"All fields are required. Please complete the form."</em></li>
                                </ul>
                            </li>
                            </ol>
                        <h3>Successful Sign-Up</h3>
                        <p>If all validations are passed:</p>
                        <ul class="indented-list">
                        <li>You will receive a verification code. Copy this code and enter it in the OTP Process</li>
                        <img src="../images/Screenshot 2025-01-07 2345710.png"  class="screenshot">
                        <li>After activation, you can log in and begin exploring the MCC Alumni Application.</li></ul>
               
                        <h2>Step 3: Troubleshooting Sign-Up Issues</h2>
                        <p>If you encounter repeated errors:</p>
                        <ol class="indented-list">
                        <li>Double-check your <strong>Student ID</strong> and <strong>Last Name</strong> for accuracy.</li>
                        <li>Ensure the email you are using is not already registered.</li>
                        <li>
                            If issues persist, contact <strong>Support</strong> via the <strong>Help & Support</strong> section of the app.
                        </li>
                        </ol>

                        <h2>First-Time Login - Additional Information Form</h2>
                        <p>Redirect to Profile Completion:</p>
                        <ol class="indented-list">
                            <li>
                                <strong>Redirect to Profile Completion:</strong>
                                <ul>
                                    <li>Upon logging in for the first time, you will be redirected to an <strong>Additional Information Form</strong>. This step is necessary to complete your profile.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Fill in Required Information:</strong>
                                <ul>
                                    <li>On the form, fill in the remaining details that are required to complete your profile:</li>
                                    <ul>
                                        <li><strong>Date of Birth</strong></li>
                                        <li><strong>Gender</strong></li>
                                        <li><strong>Contact Number</strong></li>
                                        <li><strong>Address</strong></li>
                                        <li><strong>Etc.</strong></li>

                                    </ul>
                                    <li>Take your time to ensure that all the details are accurate.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Verify Your Information:</strong>
                                <ul>
                                    <li>Carefully review the information you’ve entered to ensure everything is correct.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Submit Your Profile Information:</strong>
                                <ul>
                                    <li>Once you are satisfied with the details, click the <strong>Submit</strong> button to save your information.</li>
                                </ul>
                            </li>
                        </ol>

                        <div class="screenshot-container">
                         <img src="../images/472376013_618775607219387_536450725686865327_n.jpg"  class="screenshot">
                         <img src="../images/462587512_581941218035079_7650938391830401527_n.jpg"  class="screenshot">
                         <img src="../images/471928348_486391714089187_6356988375940182779_n.jpg"  class="screenshot">
                         </div>
                        <h2>Redirect to the Homepage</h2>
                         <ol class="indented-list">
                            <li>
                                <strong>Confirmation:</strong>
                                <ul>
                                    <li>After submitting your profile information, the system will process it and log you into the application.</li>
                                </ul>
                            </li>
                            <li>
                                <strong>Access the Homepage:</strong>
                                <ul>
                                    <li>You will be redirected to the homepage, where you can start exploring and using the features of the application.</li>
                                </ul>
                            </li>
                            
                        </ol>
                        <div class="screenshot-container">
                         <img src="../images/image_app_one.jpg"  class="screenshot">
                         <img src="../images/462581209_480895911697563_8410250235435675641_n.jpg"  class="screenshot">
                         <img src="../images/462574929_1160860839050358_3134453183808623564_n (1).jpg"  class="screenshot">

                         </div>
                        <h3>Need Help?</h3>
                        <p>If you encounter any issues, refer to the Troubleshooting section or contact support for assistance.</p>
                    `;
                    break;
                // Add more cases for other sections if needed
                default:
                    content.innerHTML = `<h1>Content Not Found</h1><p>The requested content could not be found.</p>`;
            }
        }
    </script>
</body>

</html>
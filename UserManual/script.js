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
                       <img src="../images/462587315_568750512637376_3657400423828773038_n.jpg"  class="screenshot">
                       <img src="../images/462562498_580301538110408_8486396023419738187_n (1).jpg"  class="screenshot">
                       <img src="../images/462586812_950376430310923_8737803355523657319_n.jpg"  class="screenshot">
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
            case 'home':
                content.innerHTML = `
                    <h1>Home Page</h1>
                    <div class="screenshot-container">
                    <img src="../images/image_app_one.jpg"  class="screenshot">
                    </div>
                `; 
            break;

        // Add more cases for other sections if needed
        default:
            content.innerHTML = `<h1>Content Not Found</h1><p>The requested content could not be found.</p>`;
    }
}
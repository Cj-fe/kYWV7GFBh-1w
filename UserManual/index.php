<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Documentation</title>
    <link rel="stylesheet" href="style.css">
   
</head>

<body>
    <header class="header">
        <div class="header-left">
            <button class="menu-toggle" aria-label="Toggle menu">☰</button>
            <a href="#" class="logo">MCC ALUNI DOCS</a>
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

<script src="script.js"></script>
</body>

</html>
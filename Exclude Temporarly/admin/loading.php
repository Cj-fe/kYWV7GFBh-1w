<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Loading...</title>
    <style>
        /* Style for loading screen */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0s ease-in;
        }
        #loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }
    </style>
</head>
<body>
    <div id="loading-screen">
        <div>Loading...</div>
    </div>

    <script>
        // Hide the loading screen after the page has fully loaded
        window.onload = function() {
            setTimeout(function() {
                document.getElementById('loading-screen').classList.add('hidden');
            }, 10000); // 10 seconds
        };
    </script>
</body>
</html>

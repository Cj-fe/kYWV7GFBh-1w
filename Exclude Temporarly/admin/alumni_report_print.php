<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A4 Document Centered</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.3.1/jspdf.umd.min.js"></script>
    <style>
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            background-color: #f0f0f0;
        }

        .a4 {
            width: 210mm;
            min-height: 297mm;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20mm;
            box-sizing: border-box;
            margin-top: 20px;
            position: relative;
            margin-bottom: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            position: relative;
            margin-bottom: 20px;
        }

        .header img {
            width: 185px;
            height: auto;
        }

        .header .left {
            margin-right: auto;
        }

        .header .right {
            width: 95px;
            margin-left: auto;
        }

        .header center {
            position: absolute;
            left: 50%;
            transform: translateX(-39%);
            text-align: center;
        }

        .header p {
            margin: 5px 0;
            font-size: 14px;
        }

        .content {
            overflow-x: auto;
        }

        .content center h2 {
            margin-top: 50px;
            margin-bottom: 20px;
        }



        .print-button {
            position: absolute;
            bottom: 10mm;
            right: 10mm;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .download-pdf-button {
            position: absolute;
            bottom: 10mm;
            right: 80mm;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease;
        }

        .download-pdf-button:hover {
            transform: scale(1.1);
        }

        .print-button:hover {
            transform: scale(1.1);
        }

        .view-report-details {
            margin-right: 10px;
            /* Adjust as needed for spacing */
        }


        .information-section {
            margin-bottom: 20px;
            border: 3px solid silver;
            padding: 45px;
            border-radius: 8px;
            /* Rounded corners for a card-like appearance */

        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #f85252;


        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;

            border-bottom: 2px solid silver;
            /* Horizontal line below each label */
            padding-bottom: 8px;
            padding-top: 8px;
            padding-right: 10px;
        }

        .info-label {
            flex: 1;
           
            padding-right: 20px;
            font-size: 12px;
            text-transform: uppercase;
            /* Ensure at least 20px space between label and content */
        }

        .info-content {
            flex: 2;
            padding-left: 20px;
            /* Ensure at least 20px space between label and content */
        }

        /* Add padding and other styles as needed */
        .view-report-details {
            font-size: 20px;
        }

        @media print {
            .print-button {
                display: none;
            }

            body {
                background-color: transparent;
            }

            .a4 {
                box-shadow: none;
                background-color: transparent;
            }
        }
    </style>
</head>

<body>
    <div class="a4" id="a4-container">
        <div class="header">
            <img src="../images/school_logo.png" class="left" alt="">
            <center>
                <p>Republic of the Philippines</p>
                <p>Region VII, Central Visayas</p>
                <p>Commission on Higher Education</p>
                <p><b>MADRIDEJOS COMMUNITY COLLEGE</b></p>
                <p>Crossing Bunakan, Madridejos, Cebu</p>
            </center>
            <img src="../images/madridejos.png" class="right" alt="">
        </div>
        <div class="content" style="overflow-x:auto;">
            <div class="information-section">
                <div class="section-title">Personal Information</div>
                <div class="info-row">
                    <div class="info-label">Student ID</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Alumni Name</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Course</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Year Graduated</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Employment Status</div>
                    <div class="info-content"></div>
                </div>
                <!-- Add more personal information rows as needed -->
            </div>
            <div class="information-section">
                <div class="section-title">Employment Information</div>
                <div class="info-row">
                    <div class="info-label">First Employment</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Current Employment</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Type Of Work</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Work Position</div>
                    <div class="info-content"></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Monthly Income</div>
                    <div class="info-content"></div>
                </div>
                <!-- Add more employment information rows as needed -->
            </div>
        </div>
        <button class="print-button" id="printButton">Print</button>
    </div>

    <script>
        window.addEventListener('scroll', function () {
            var a4Container = document.getElementById('a4-container');
            var printButton = document.getElementById('printButton');

            if (window.pageYOffset > a4Container.offsetTop) {
                printButton.style.position = 'fixed';
                printButton.style.bottom = '10mm';
                printButton.style.right = '10mm';
            } else {
                printButton.style.position = 'absolute';
                printButton.style.bottom = '10mm';
                printButton.style.right = '10mm';
            }
        });

        // JavaScript for printing the A4 document content
        var printButton = document.getElementById('printButton');
        printButton.addEventListener('click', function () {
            window.print();
        });
    </script>
</body>

</html>
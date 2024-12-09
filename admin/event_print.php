<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>A4 Document</title>
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

    table {
      width: 100%;
      border-collapse: collapse;
      margin: 0 auto;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 8px;
      font-size: 13px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
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
      <center>
        <h2>
          <?php
          if (isset($_GET['data'])) {
            $encodedData = $_GET['data'];
            $decodedData = json_decode(urldecode($encodedData), true);
            $dataToPrint = $decodedData['dataToPrint'];
            $isMixedBatch = $decodedData['isMixedBatch'];

            if ($isMixedBatch) {
              echo "LIST OF ALUMNI";
            } else {
              if (is_array($dataToPrint) && count($dataToPrint) > 0) {
                echo "LIST OF BATCH " . htmlspecialchars($dataToPrint[0]['batchYear']);
              } else {
                echo "No data to display.";
              }
            }
          } else {
            echo "No data provided.";
          }
          ?>
        </h2>
      </center>

      <table id="printTable" class="printable-table">
        <thead>
          <tr>
            <th>Alumni ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Batch</th>
            <th>Status</th>
            <th>Date Responded</th>
            <th>Event Participated</th>
          </tr>

        </thead>
        <tbody id="printTableBody">
          <?php
          if (isset($dataToPrint)) {
            foreach ($dataToPrint as $row) {
              echo "<tr>" . $row['content'] . "</tr>";
            }
          }
          ?>
        </tbody>
      </table>
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
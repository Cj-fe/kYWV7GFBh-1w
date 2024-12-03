<?php
//fetch_deleteSurvey.php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

$data = $firebase->retrieve("deleted_survey_set");
$data = json_decode($data, true);

if (is_array($data)) {
    foreach ($data as $id => $survey) {
        // Convert timestamps to readable format
        $start_date = new DateTime($survey['survey_start']);
        $formatted_start = $start_date->format('F j, Y H:i:s');

        $end_date = new DateTime($survey['survey_end']);
        $formatted_end = $end_date->format('F j, Y H:i:s');

        $deleted_date = new DateTime($survey['deleted_at']);
        $formatted_deleted = $deleted_date->format('F j, Y H:i:s');

        echo "<tr>
                <td>{$survey['survey_title']}</td>
                <td><i>{$survey['survey_desc']}</i></td>
                <td>{$formatted_start}</td>
                <td>{$formatted_end}</td>
                <td>{$formatted_deleted}</td>
                <td>
                <a class='btn btn-info btn-sm btn-flat open-retrieve' data-id='$id'>RESTORE</a>
                <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }
}
?>
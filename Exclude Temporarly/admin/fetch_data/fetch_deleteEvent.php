<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

$data = $firebase->retrieve("deleted_event");
$data = json_decode($data, true);

if (is_array($data)) {
  
    foreach ($data as $id => $event) {
        // Strip HTML tags from event_description
        $event_description_plain = strip_tags($event['event_description']);

        // Prepare image HTML if image_url is available
        $image_html = '';
        if (isset($event['image_url']) && !empty($event['image_url'])) {
            $image_html = "<img src='{$event['image_url']}' alt='event Image' width='65px' height=65px'>";
        }

        // Convert the timestamp to readable format
        $date = new DateTime($event['event_created']);
        $formatted_date = $date->format('F j, Y H:i:s');

        echo "<tr>
                <td>{$image_html}</td>
                <td>{$event['event_title']}</td>
                <td>{$event['event_author']}</td>
                <td class='description-cell'>{$event_description_plain}</td>
                <td>{$formatted_date}</td>
                <td>
                <a class='btn btn-info btn-sm btn-flat open-retrieve' data-id='$id'>RESTORE</a>
                <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }
}
?>
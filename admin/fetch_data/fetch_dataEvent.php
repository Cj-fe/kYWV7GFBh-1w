<?php
require_once 'includes/firebaseRDB.php';
require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

$data = $firebase->retrieve("event");
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

        echo "<tr>
                <td>{$image_html}</td>
                <td>{$event['event_title']}</td>
                <td>{$event['event_author']}</td>
                <td class='description-cell'>{$event_description_plain}</td>
                <td>{$event['event_created']}</td>
                <td>
                    <a class='btn btn-success btn-sm btn-flat open-modal' data-id='$id'>EDIT</a>
                    <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }

}
?>

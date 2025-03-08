<?php
require_once 'includes/firebaseRDB.php';

require_once 'includes/config.php'; // Include your config file

$firebase = new firebaseRDB($databaseURL);

$data = $firebase->retrieve("news");
$data = json_decode($data, true);

if (is_array($data)) {
  
    foreach ($data as $id => $news) {
        // Strip HTML tags from news_description
        $news_description_plain = strip_tags($news['news_description']);

        // Prepare image HTML if image_url is available
        $image_html = '';
        if (isset($news['image_url']) && !empty($news['image_url'])) {
            $image_html = "<img src='{$news['image_url']}' alt='News Image' width='65px' height=65px'>";
        }

        echo "<tr>
                <td>{$image_html}</td>
                <td>{$news['news_title']}</td>
                <td>{$news['news_author']}</td>
                <td class='description-cell'>{$news_description_plain}</td>
                <td>{$news['news_created']}</td>
                <td>
                    <a class='btn btn-success btn-sm btn-flat open-modal' data-id='$id'>EDIT</a>
                    <a class='btn btn-danger btn-sm btn-flat open-delete' data-id='$id'>DELETE</a>
                </td>
              </tr>";
    }

}
?>

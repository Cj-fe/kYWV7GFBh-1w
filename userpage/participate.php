<?php
    // Include Firebase database handling class
    require_once '../includes/firebaseRDB.php';

    // Initialize Firebase URL
    require_once '../includes/config.php';
    $firebase = new firebaseRDB($databaseURL);
    
    if (isset($_POST['event_id']) && isset($_POST['alumni_id'])) {
        $event_id = $_POST['event_id'];
        $alumni_id = $_POST['alumni_id'];

        // Check if the alumni has already participated
        $participationData = $firebase->retrieve("event_participation");
        $participationData = json_decode($participationData, true);
        $alreadyParticipated = false;

        if (is_array($participationData)) {
            foreach ($participationData as $participation) {
                if ($participation['event_id'] === $event_id && $participation['alumni_id'] === $alumni_id) {
                    $alreadyParticipated = true;
                    break;
                }
            }
        }

        if ($alreadyParticipated) {
            echo "You have already participated in this event.";
        } else {
            // Create the data to be inserted
            $participationData = [
                'event_id' => $event_id,
                'alumni_id' => $alumni_id,
                'participation' => "Participated"
            ];

            // Insert the data into the event_participation rule
            $insert = $firebase->insert("event_participation", $participationData);

            if ($insert) {
                echo "Participation successful!";
            } else {
                echo "Error in participation.";
            }
        }
    } else {
        echo "Invalid request.";
    }
?>

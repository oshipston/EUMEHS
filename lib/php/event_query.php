<?php
    include "../../../config/config.php";
    include "construct_eventfeed_fncs.php";
    // Pull and initialise Past or Present var from event_query js call
    $PorP = $_GET['PorP'];
    
    // Initialise mySQL DB connection
    $con = mysqli_connect($servername, $username, $password, $dbname);
    // Handle connection error
    if (!$con) {
        console_log("Connection Unsuccessful..");
        die('Could not connect: ' . mysqli_error($con));
    } else {
        console_log("Connection Made");
        $con->set_charset("utf8");
            // Select upcoming events
        $upcoming_sql = "SELECT * FROM events WHERE START_DATETIME > CURDATE() ORDER BY START_DATETIME ASC";
            // Select events from the past
        $past_sql = "SELECT * FROM events WHERE START_DATETIME <= CURDATE() ORDER BY START_DATETIME DESC";
        // Query mySQL database for upcoming and past results seperately
        $upcoming_result = mysqli_query($con,$upcoming_sql);
        $past_result = mysqli_query($con,$past_sql);
        // Per case return html feedboxes
        echo '<div class="container-fluid" style="width:80%;">';
        switch ($PorP){
            case "All":
                echo '<h1>All Events</h1>';
                echo '<h2>Upcoming</h2>';
                construct_event_feed($upcoming_result,1);
                echo '<h2>Past</h2>';
                construct_event_feed($past_result,0);
                break;
            case "Upcoming":
                echo '<h1>Upcoming Events</h1>';
                echo '<h2>Upcoming</h2>';
                construct_event_feed($upcoming_result,1);
                break;
            case "Past":
                echo '<h1>Past Events</h1>';
                echo '<h2>Past</h2>';
                construct_event_feed($past_result,0);
                break;
        }
        echo '</div>';
        // CLose mySQL connection
        mysqli_close($con);
    }
?>

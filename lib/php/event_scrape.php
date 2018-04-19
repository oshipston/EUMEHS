<?php
    require 'vendor/autoload.php';
    include "../../../config/config.php";

    // Create mySQL connection
    $con = new mysqli($servername, $username, $password, $dbname);
    mysqli_set_charset($con,"utf8");

    // Check connection to mySql server
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
        echo '<p>Connection failed: ' . $con->connect_error.'</p>';
    }

    // Construct mySQL search strings for current event list
    $pageSearch = "SELECT * FROM pages";
    $eventSearch = "SELECT * FROM events";

    // Query mySQL tables using current SQL connection
    $pageIds = mysqli_query($con,$pageSearch);
    $currentEventIds = mysqli_query($con,$eventSearch);

    //Assign current event table query into an array of current event IDs
    $i = 0;
    while($row = $currentEventIds->fetch_assoc()) {
        $ID_array[$i] = $row["FB_EVENT_ID"];
        //echo $ID_array[$i] . '<br>'; // Debugging
        $i = $i+1;
    }
    //If nothing is returned...
    if (is_null($ID_array)) {
        $ID_array = []; // Do nothing
    }

    //Use page IDs to request new event lists from FB API
    //If any page IDs were returned..
    if ($pageIds->num_rows > 0) {
        // For EACH ORG in organisation table...
        while($row = $pageIds->fetch_assoc()) {
            $FB_ORG_ID = $row["FB_PAGE_ID"]; // Assign FB ID from table query
            if ($FB_ORG_ID == "/medicalethicsandhumanities/") {
                $FEATURED = 1; // If from EUMEHS page set as featured
            } else {
                $FEATURED = 0; // Else set as normal
            }
            $ORG_LOGO = $row["ORG_LOGO"]; // Assign organisation logo to insert into events table
            $ORG_URL = $row["ORG_URL"]; // Assign organisation URL to insert into events table
            $FB_org_name = $row["NAME"]; // Assign organisation name to insert into events table

            // Construct FB API request to get the organisations new events
            $eventsRequest = $fb->request('GET', $FB_ORG_ID.'events');
            try {
                // Requests client service and sends asks for events frot the particular org
                $response = $fb->getClient()->sendRequest($eventsRequest); //
                //echo 'Graph returned successfully ' . $response->getBody() . '<br>'; // Debugging
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
            }
            //Handle Graph response
            $graphEdge = $response->getGraphEdge();
            //$object = $response->getGraphObject();

            // For every event scrape details...
            echo '<p> Dump </p><p> ' . get_class($graphEdge); //Debugging Line
            echo '<p>' . sizeof($graphEdge) . '</p>'; //Debugging Line
            echo '<p>' . var_dump($response->getHeaders()) . '</p>'; //Debugging Line
            echo '<p>' . var_dump($response->getAccessToken()) . '</p>'; //Debugging Line

            //echo '<p> Dump </p><p> ' . var_dump($response->getGraphEdge()) . ' </p>'; // Debugging line
            foreach ($graphEdge as $graphNode) {
                echo '<p> Graph Node </p>'; // Debugging line
                $event_ID = $graphNode->getField('id');
                // If event_ID is not in current array...
                if (!in_array($event_ID, $ID_array)) {
                    $event_name = $graphNode->getField('name');
                    $event_name = mysqli_real_escape_string($con,$event_name);
                    $event_descr = $graphNode->getField('description');
                    $event_place = $graphNode->getField('place');
                    $event_place_string = $event_place['name'].', '.$event_place['location']['street'].', '.$event_place['location']['city'].', '.$event_place['location']['zip'];
                    $event_descr = mysqli_real_escape_string($con,$event_descr);
                    //echo '<p>'.$event_name.'</p>';
                    //$event_descr = addslashes($event_descr);
                    $event_start = $graphNode->getField('start_time');
                    if (is_null($event_start)){
                        $event_start_date = "Unknown Start";
                        $event_start_time = "UkwSt";
                        $event_start_datetime = "NULL";
                    } else {
                        $event_start_date = $event_start->format('l jS F Y');
                        $event_start_time = $event_start->format('H:i');
                        $event_start_datetime = $event_start->format('Y-m-d H:i:s');
                    }
                    $event_end = $graphNode->getField('end_time');
                    if (is_null($event_end)){
                        $event_start_date = "Unknown End";
                        $event_start_time = "UkwEn";
                        $event_start_datetime = "NULL";
                    } else {
                        $event_end_date = $event_end->format('l jS F Y');
                        $event_end_time = $event_end->format('H:i');
                        $event_end_datetime = $event_end->format('Y-m-d H:i:s');
                    }
                    //echo $event_start_datetime.'<br>';
                    $event_upload_datetime = date('Y-m-d H:i:s');
                    // For every event scrape images
                    /*$eventImgRequest = $fb->request('GET','/1882259251987412/picture?fields=url');
                    try {
                        $imgResponse = $fb->getClient()->sendRequest($eventImgRequest);
                        //trigger_error("Image Response received", E_USER_NOTICE);
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        // When Graph returns an error
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        // When validation fails or other local issues
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }*/
                    $sql_event_insert = "INSERT INTO events (FB_EVENT_ID, TITLE, DESCRIPTION, LOCATION, START_DATE, START_TIME, START_DATETIME, END_DATE, END_TIME, END_DATETIME, HOST_ORG, ORG_LOGO, ORG_URL, FB_ORG_ID, IMAGE, UPLOADED_DATETIME, FEATURED) VALUES ('$event_ID','$event_name','$event_descr','$event_place_string','$event_start_date','$event_start_time','$event_start_datetime','$event_end_date','$event_end_time','$event_end_datetime','$FB_org_name','$ORG_LOGO','$ORG_URL','$FB_ORG_ID','img','$event_upload_datetime','$FEATURED')";
                    if ($con->query($sql_event_insert) === TRUE) {
                        echo '<p>New records created successfully. Last inserted event is: "'.$event_name.'"<p>';
                    } else {
                        console_log("Error: <br>" . $con->error);
                        echo '<p>Error in creating event: "'.$event_name.'"</p>';
                        echo '<p>Host Organisation: "'.$FB_org_name.'"</p>';
                        echo '<p>Event Description: "'.$event_descr.'"</p>';
                        echo '<p>Encoding: '.mb_detect_encoding($event_descr, "auto").'</p>';
                        echo '<p>'.$con->error.'</p>';
                    }
                }
            }
        }
    }
    $con->close();

?>

<?php
function construct_event_feed($result,$porp){
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if ($row["FEATURED"] == 1)
            {
                $feat = "featured";
                $feat_text = "featured_event_text";
                
            } else {
                $feat = "";
                $feat_text = "event_text";
            }
            if ($porp == 1)
            {
                $porp_class= "upcoming";
                
            } else {
                 $porp_class= "past";
            }
            echo '<div class="container-fluid event_feedbox">'; //feedbox start
            echo '<div class="event_feedbox_header '.$feat.' '.$porp_class.'" id="event_headerbox" data-toggle="collapse" data-target="#collapse'.$row["FB_EVENT_ID"].'" onclick=switch_chevron('.$row["FB_EVENT_ID"].')>'; //feedbox_header start
            echo '<a id="title">'.$row["TITLE"].'</a><span id="collapse_chevron_'.$row["FB_EVENT_ID"].'" class="glyphicon glyphicon-chevron-right"></span><br><a id="time_details">'.$row["START_TIME"]." - ".$row["END_TIME"]." ".$row["START_DATE"].'</a>';
            echo '</div>'; //feedbox_header end
            echo '<div class="collapse" id="collapse'.$row["FB_EVENT_ID"].'">'; // collapse start
            echo '<div class="'.$feat_text.'" id="event_text">'; //event_text start
            echo '<p><a id="description">'.$row["DESCRIPTION"].'</a></p>';
            echo '<div class="container-fluid" id="detailsLogoContainer">'; //detailsLogoContainer start
            echo '<div class="row">'; //row start
            echo '<div class="col-sm-8" id="detailsContainer">'; //detailsContainer start
            echo '<p><a class="event_details" href="https://www.google.co.uk/maps/search/'. str_replace(" ", "+", $row["LOCATION"]).'/" target="_blank" >'.'<strong>Location: </strong>'.$row["LOCATION"].'</a><br>';
            echo '<a class="event_details" href="https://www.facebook.com'.$row["FB_ORG_ID"].'" target="_blank">'.'<strong>Host Page: </strong>'.$row["HOST_ORG"].'</a><br>';
            echo '<a class="event_details" href="https://www.facebook.com/events/'.$row["FB_EVENT_ID"].'/" target="_blank" >'.'<strong>Facebook Event: </strong>'.$row["TITLE"].'</a></p>';
            echo '</div>'; //detailsContainer end
            echo '<div class="col-sm-4" id="logoContainer">'; //logoContainer start
            echo '<a href="'.$row["ORG_URL"].'" target="_blank">';
            echo '<img class="img-responsive" src="'.$row["ORG_LOGO"].'" height=100px/>';
            echo '</a>';
            echo '</div>'; //logosContainer end
            echo '</div>'; //row end
            echo '</div>'; //detailsLogosContainer end
            echo '</div>'; //event_text end
            echo '</div>'; //collapse end
            echo '</div>'; //feedbox end
        }
    } else {
        echo "No results from your search";
    }
}
?>

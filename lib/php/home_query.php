<?php
    include "../../../config/config.php";
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
        $home_sql = "SELECT SQL_NO_CACHE * FROM home_feed ORDER BY PRIORITY";
        $home_result = mysqli_query($con,$home_sql);
        $i=0;
        $num_cols = 3;
        $num_items = mysqli_num_rows($home_result);
        $remainder_items = $num_items % $num_cols;
        $num_items_per_col = ($num_items-($num_items % $num_cols))/$num_cols;
        echo '<div class="container-fluid">';
        while($row = $home_result->fetch_assoc()) {
            if ($i==0) {
                 echo '<div class="col-sm-4 home_col">';
                 $col_count = 1;
            } else if ($i % $num_items_per_col == 0 && $col_count != $num_cols) {
                 echo '</div>';
                 echo '<div class="col-sm-4 home_col">';
                 $col_count++;
            }
            if ($row["STABLE"]){
                echo '<div class="container-fluid home_feedbox">';
                echo '<div class="feed_border">';
                echo '<div class="stable_home_header feed_link" id="'.$row["SEE_MORE_URL"].'">';
                 echo '<h2>'.$row["TITLE"].'</h2>';
                 echo '</div>';
                 echo '<div class="home_feed_text">';
                 echo '<p>'.$row["BLURB"].'</p>';
                 echo '</div>';
                 echo '</div>';
                 echo '</div>';
            } else {
                echo '<div class="container-fluid home_feedbox">';
                echo '<div class="feed_border">';
                echo '<img class="img-responsive home_feed_img feed_link" id="'.$row["SEE_MORE_URL"].'" src="'.$row["IMAGE_URL"].'" width="100%"/>';
                echo '<div class="unstable_home_header feed_link" id="'.$row["SEE_MORE_URL"].'">';
                echo '<h2 class="feed_link" id="'.$row["SEE_MORE_URL"].'">'.$row["TITLE"].'</h2>';
                echo '</div>';
                echo '<div class="home_feed_text">';
                echo '<p>'.$row["BLURB"].'</p>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            $i++;
            // if ($i == $num_items-1 && $col_count == $num_cols) {
              //  echo '</div>';
                //}
        }
        echo '</div>';
        // CLose mySQL connection
        mysqli_close($con);
    }
    ?>

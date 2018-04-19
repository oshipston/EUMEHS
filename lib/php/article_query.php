<?php
    include "../../../config/config.php";
    $type = $_GET['type'];
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
        switch ($type){
            case "all":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles ORDER BY PUBLISH_DATETIME DESC";
                break;
            case "recent":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles WHERE TYPE = recent ORDER BY PUBLISH_DATETIME DESC";
                break;
            case "featured":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles WHERE TYPE = featured ORDER BY PUBLISH_DATETIME DESC";
                break;
            case "reviews":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles WHERE TYPE = review ORDER BY PUBLISH_DATETIME DESC";
                break;
            case "opinions":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles WHERE TYPE = opinion ORDER BY PUBLISH_DATETIME DESC";
                break;
            case "news":
                $article_sql = "SELECT SQL_NO_CACHE * FROM articles WHERE TYPE = news ORDER BY PUBLISH_DATETIME DESC";
                break;
        }
        try {
            $article_result = mysqli_query($con,$article_sql);
            if ($article_result == 0){
                throw new Exception('No events found of that type.');
            }
            while($row = $article_result->fetch_assoc()) {
                echo '<div class="container-fluid col-sm-12 article_feedbox">';
                echo '<div class="article_feed_border">';
                echo '<div class="article_header feed_link" id="Article'.$row["ARTICLE_ID"].'">';
                echo '<h2>'.$row["TITLE"].'</h2>';
                echo '<p style="margin:0;"> By: '.$row["AUTHOR"].'</p>';
                echo '</div>';
                echo '<div class="article_feed_text">';
                echo '<p>'.$row["EXCERPT"].'</p>';
                echo '</div>';
                echo '</div>'; // end article feedborder
                echo '</div>'; // end article feedbox
            }
        } catch (Exception $e) {
            echo '<h2 style="padding-left:30px;">No articles found of that type.</h2>';
        }
        // CLose mySQL connection
        mysqli_close($con);
    }
    ?>

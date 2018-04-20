<?php
    require 'vendor/autoload.php';
    require 'simple_html_dom.php';
    include "../../../config/config.php";


    // Console logging function
    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }

    // Create mySQL connection
    $con = new mysqli($servername, $username, $password, $dbname);
    mysqli_set_charset($con,"utf8");

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
        echo '<p>Connection failed: ' . $con->connect_error.'</p>';
    }

    // Download BMJ Blog list by each page
    $page=1;
    while ($page < 3) {
      echo 'Page = '.$page.'<br>'; //Debugging Line
        $html = file_get_html('http://blogs.bmj.com/medical-humanities/category/book-reviews/page/'.$page.'/');
        $blogPosts = $html->find('article.post'); //Pull HTML array of all posts
        // For each post..
        foreach ($blogPosts as $post){
            $postHeader = $post->find('header.entry-header')[0]; //Pull header of post
            $postTitle = $postHeader->find('.entry-title')[0]->getAttribute('plaintext'); //Discern whether a book review...
            // If post is a book review...
            if (strpos($postTitle,'Book Review:') !== false) {
                echo $postHeader.'<br>'; //Debugging Line
                $postDate = $postHeader->find('.posted-on')[0]->getAttribute('plaintext');
                $postAuthor = $postHeader->find('.author')[0]->getAttribute('plaintext');
                $postAuthorURL = $postHeader->find('span.author a')[0]->getAttribute('href');
                $postBlurb = $post->find('.entry-content p')[0]->getAttribute('plaintext');
                $postURL = $post->find('.entry-content .shareaholic-canvas')[0]->getAttribute('data-link');
                echo $postURL;
            }
      }
      $page++;
    }
    $con->close();
?>

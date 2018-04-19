<?php
    require 'vendor/autoload.php';
    require 'simple_html_dom.php';
    include "../../../config/config.php";
    
    // Create mySQL connection
    $con = new mysqli($servername, $username, $password, $dbname);
    mysqli_set_charset($con,"utf8");

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
        echo '<p>Connection failed: ' . $con->connect_error.'</p>';
    }
    
    // Download BMJ Blog list by each page
    $i=0;
    while ($i < 12) {
        $html = file_get_html('http://blogs.bmj.com/medical-humanities/category/book-reviews/page/'.$i.'/');
        $blogHeaders = $html->find('header.entry-header');
        foreach ($blogHeaders as $postHeader){
            $blogTitle = $postHeader->find('.entry-title')[0]->getAttribute('plaintext');
            echo $blogTitle.'<br>';
            if (strpos($blogTitle,'Book Review:') !== false) {
                $blogDate = $postHeader->find('.posted-on')[0]->getAttribute('plaintext');
                $blogAuthor = $postHeader->find('.author')[0]->getAttribute('plaintext');
                $blogAuthorURL = $postHeader->find('span.author a')[0]->getAttribute('href');
                echo $blogAuthor.'<br>';
                echo $blogAuthorURL.'<br>';
                echo $blogDate.'<br>';
            }
        }
        echo $i.'<br>';
        $i++;
    }
   
    $con->close();
?>

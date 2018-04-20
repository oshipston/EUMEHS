<?php
    require 'vendor/autoload.php';
    require 'simple_html_dom.php';
    include "../../../config/config.php";

    // Price Search function
    function bookPrice($plainText){
        $prIdx = strpos($plainText,'£'); // Find 1st £ which is normally the price
        // If any £'s were found try for price...
        if ($prIdx !== false) {
          $cut = substr($plainText,$prIdx,strlen($plainText)); // Take str segment following £
          $i=0;
          // For each char check if alphabetical (normally ed of price)...
          foreach (str_split($cut) as $a) {
            // If alphabetical character...
            if (ctype_alpha($a) == true){
              $prEndIdx = $i; // Note index of char...
              break;
            }
            $i++;
          }
          $priceStr = substr($cut,0,($i-2)); //Cut segment at alpha Idx...
        } else {
          $priceStr = ''; // If no £ return a null price.
        }
        return $priceStr;
    }

    // Function to scrape book cover URLs from main article...
    function coverURL($postURL){
      $html = file_get_html($postURL);
      $coverImg = $html->find('.entry-content img')[0];
      if (is_null($coverImg)){
        $coverURL = '';
      } else {
        $coverURL = $coverImg->getAttribute('src');
      }
      return $coverURL;
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
    $pg=1;
    while ($pg < 2) {
      echo 'Page = '.$pg.'<br>'; //Debugging Line
      $html = file_get_html('http://blogs.bmj.com/medical-humanities/category/book-reviews/page/'.$pg.'/');
      // For each post..
      if (is_null($html)) {
        echo 'No html on page';
        break;
      } else {
        foreach ($html->find('article.post') as $post){
          //Pull post information..
          $postDat['Header'] = $post->find('header.entry-header')[0]; //Pull header of post
          $postDat['Title'] = $postDat['Header']->find('.entry-title')[0]->getAttribute('plaintext'); //Discern whether a book review...
          $postDat['Date'] = $postDat['Header']->find('.posted-on')[0]->getAttribute('plaintext');
          $postDat['Author'] = $postDat['Header']->find('.author')[0]->getAttribute('plaintext');
          $postDat['AuthorURL'] = $postDat['Header']->find('span.author a')[0]->getAttribute('href');
          $blurbStr = $post->find('.entry-content p')[0]->getAttribute('plaintext'); // Pull blurb text
          $postDat['Blurb'] = substr($blurbStr,0,(strlen($blurbStr)-10)); // Cut end off blurb text
          $postDat['URL'] = $post->find('.entry-content .shareaholic-canvas')[0]->getAttribute('data-link');
          $postDat['BookPrice'] = bookPrice($postDat['Blurb']); //Try and find book price in blurb..
          $postDat['CoverURL'] = coverURL($postDat['URL']); //Try and cover source in post HTML...
          // Pull book title..
          // 'Book review: ' is a common prefix so check for it...
          if (strpos($postDat['Title'],'Book Review:') !== false) {
            // If present slice string...
            $postDat['BookTitle'] = substr($postDat['Title'],strlen('Book Review:'),(strlen($postDat['Title'])-strlen('Book Review:')));
          } else {
            // If not ASSUME book title is the same as the article...
            $postDat['BookTitle'] = $postDat['Title'];
          }
          echo $postDat['BookTitle'].'<br>';
        }
        $pg++;
      }
    }
    $con->close();
?>

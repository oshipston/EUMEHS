<?php
    include "../../config/config.php";
    function console_log( $data ){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
    // Create connection
    $con = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    
    $org_query_array = array("INSERT INTO pages (NAME, FB_PAGE_ID) VALUES ('Edinburgh University Medical Ethics and Humanities Society','/medicalethicsandhumanities/')","INSERT INTO pages (NAME, FB_PAGE_ID) VALUES ('Glasgow University Medical Ethics Society','/GUMedicalEthics/')","INSERT INTO pages (NAME, FB_PAGE_ID) VALUES ('University of Glasgow End of Life Studies','/EndofLifeStudy/')");
    
    foreach($org_query_array as $q){
        if ($con->query($q) === TRUE) {
            echo "New records created successfully. Last inserted ID is: ".$q;
        } else {
            echo "Error: " . $q . "<br>" . $con->error;
        }
    }
    $con->close();
?>

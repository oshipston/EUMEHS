<?php
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];
    
    $EmailTo = "eumehs@gmail.com";
    $Subject = "New Message Received";
    
    // prepare email body text
    $Body = "";
    $Body .= "Name: ";
    $Body .= $name;
    $Body .= "\n";
    
    $Body .= "Email: ";
    $Body .= $email;
    $Body .= "\n";
    
    $Body .= "Message: ";
    $Body .= $message;
    $Body .= "\n";
    
    // send email
    $success = mail($EmailTo, $Subject, $Body, "From:".$email);
    trigger_error($Body, E_USER_NOTICE);
    // redirect to success page
    if ($success){
        echo "success";
    }else{
        echo "invalid";
    }
    
?>
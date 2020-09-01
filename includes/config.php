<?php
    //start connection to sql database
    ob_start();
    //start session
    session_start();
    $timezone = date_default_timezone_set("America/Detroit");

    $con = mysqli_connect("localhost", "root", "", "slotify");

    if(mysqli_connect_errno()) {
        echo "Failed to connect: ", mysqli_connect_errno();
    }
?>
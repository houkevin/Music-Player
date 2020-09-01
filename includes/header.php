<?php
// Artist.php has to come before album
// becuase we are calling the Artist object
include("includes/config.php");
include("includes/classes/User.php");
include("includes/classes/Artist.php");
include("includes/classes/Album.php");
include("includes/classes/Song.php");
include("includes/classes/Playlist.php");
//session_destroy();

//redirect to login page if user isn't logged in
if(isset($_SESSION['userLoggedIn'])) {
    $userLoggedIn = new User($con, $_SESSION['userLoggedIn']);
    $username = $userLoggedIn->getUsername();
    // give access to userLoggedIn to JS
    echo "<script>userLoggedIn = '$username';</script>";
}
else {
    header("Location: register.php");
}

?>


<html>
<head>
    <title>Welcome to Slotify!</title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
</head>
<body>
    <div class="mainContainer">
        <div id="topContainer">

            <?php include("includes/navBarContainer.php"); ?>

            <div id="mainViewContainer">

                <div id="mainContent">
<?php
// check to see if page access by URL or ajax


// every ajax req contains this
// if came from ajax don't load header and footer twice
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    include("includes/config.php");
    include("includes/classes/User.php");
    include("includes/classes/Artist.php");
    include("includes/classes/Album.php");
    include("includes/classes/Song.php");
    include("includes/classes/Playlist.php");
    // let pages access userLoggedIn object
    if(isset($_GET['userLoggedIn'])) {
        $userLoggedIn = new User($con, $_GET['userLoggedIn']);
    }
    else {
        echo "username varaible not passed into page";
        exit();
    }
}
else {
    // didn't come from ajax, load header and footer
    include("includes/header.php");
    include("includes/footer.php");

    // call script.js openPage function to load main content
    $url = $_SERVER['REQUEST_URI'];
    echo "<script> openPage('$url')</script>";
    // prevent from double loading
    exit();
}

?>
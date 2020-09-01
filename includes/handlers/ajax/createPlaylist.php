<?php
include("../../config.php");

// make sure what is in the post matches ajax
if(isset($_POST['name']) && isset($_POST['username'])) {

    $name = $_POST['name'];
    $username = $_POST['username'];
    $date = date("Y-m-d");

    $query = mysqli_query($con, "INSERT INTO playlists VALUES('', '$name', '$username', '$date')");

}
else {
    echo "Name or username parameters not passed into file";
}

?>
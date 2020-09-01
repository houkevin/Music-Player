<?php
// anything echoed here is passed to the ajax function
include("../../config.php");

if(isset($_POST['albumId'])) {
    $albumId = $_POST['albumId'];

    $query = mysqli_query($con, "SELECT * FROM albums WHERE id='$albumId'");

    $resultArray = mysqli_fetch_array($query);

    echo json_encode($resultArray);
}
?>
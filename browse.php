<?php 
include("includes/includedFiles.php");
?>

<h1 class="pageHeadingBig">You Might Also Like</h1>

<div class="gridViewContainer">

    <!-- fetches row from album database -->
    <?php
        $albumQuery = mysqli_query($con, "SELECT * FROM albums ORDER BY RAND() LIMIT 10");
        # . appends two strings in php, <br> puts new line
        while($row = mysqli_fetch_array($albumQuery)) {
            # appending multiple strings together so can use the variables
            echo "<div class='gridviewItem'>
                    <span role='link' tabindex='0' onclick='openPage(\"album.php?id=" . $row['id'] . "\")' >
                        <img src='" . $row['artworkPath'] . "'>

                        <div class='gridViewInfo'>"

                            . $row['title'] .

                        "</div>
                    </span>    
                </div>";

        } 
    ?>

</div>
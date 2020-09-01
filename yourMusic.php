<?php
include("includes/includedFiles.php");
?>

<div class="playlistsContainer">
    <div class="gridViewContainer">

        <h2>PLAYLISTS</h2>
        <div class="buttonItems">
            <button class="button green" onclick="createPlaylist()">NEW PLAYLIST</button>
        </div>

        <?php
            $username = $userLoggedIn->getUsername();

            $playlistsQuery = mysqli_query($con, "SELECT * FROM playlists WHERE  owner='$username'");
        
            if(mysqli_num_rows($playlistsQuery) == 0) {
                echo "<span class='noResults'>You don't have any playlists yet.</span>";
            }
            # . appends two strings in php, <br> puts new line
            while($row = mysqli_fetch_array($playlistsQuery)) {

                $playlist = new Playlist($con, $row);
                # appending multiple strings together so can use the variables
                echo "<div class='gridviewItem' role='link' tabindex='0' onclick='openPage(\"playlist.php?id=" . $playlist->getId() . "\")'>

                        <div class='playlistImage'>
                            <img src='assets/images/icons/playlist.png'>
                        </div>
                        <div class='gridViewInfo'>"

                        . $playlist->getName() .

                        "</div>  
                    </div>";

            } 
        ?>
    </div>

</div>
<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
    array_push($resultArray, $row['id']);
} 

// using JS to play the song, so need to convert PHP array to JS array

// json can be understood by all languages, so convert to json
$jsonArray = json_encode($resultArray);
?>

<script>

// only executed when everything is ready
$(document).ready(function() {
    // need php still because it is still a PHP object
    var newPlaylist = <?php echo $jsonArray; ?>;
    audioElement = new Audio();
    // set play to false b/c dont want to play song right away when on page.
    setTrack(newPlaylist[0], newPlaylist, false);
    updateVolumeProgressBar(audioElement.audio);

    // the whole bottom container
    // on any of the following events
    // prevent the event's default action
    // in this case prevents the highlighting while dragging
    $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function(e) {
        e.preventDefault();
    });

    $(".playbackBar .progressBar").mousedown(function() {
        mouseDown = true;
    });

    $(".playbackBar .progressBar").mousemove(function(e) {
        if(mouseDown) {
            // Set time of song, depending on position of mouse
            timeFromOffset(e, this);
        }
    });

    $(".playbackBar .progressBar").mouseup(function(e) {
        timeFromOffset(e, this);
    });


    $(".volumeBar .progressBar").mousedown(function() {
        mouseDown = true;
    });

    $(".volumeBar .progressBar").mousemove(function(e) {
        if(mouseDown) {
            // this is .volumeBar .progressBar
            var percentage = e.offsetX / $(this).width();
            if(percentage >= 0 && percentage <= 1) {
                audioElement.audio.volume = percentage;
            }
        }
    });

    $(".volumeBar .progressBar").mouseup(function(e) {
        var percentage = e.offsetX / $(this).width();
        if(percentage >= 0 && percentage <= 1) {
                audioElement.audio.volume = percentage;
        }
    });

    // do it on document so mouseDown will be false no matter where you let go
    // inside of document rather than just on progress bar.
    $(document).mouseup(function() {
        mouseDown = false;
    });
});

function timeFromOffset(mouse, progressBar) {
    // width repesents the width of the element, in this case the bar
    var percentage = mouse.offsetX / $(progressBar).width() * 100;
    var seconds = audioElement.audio.duration * (percentage / 100);
    audioElement.setTime(seconds);
}

function prevSong() {
    // song time set back to 0 after getting past a certain time
    // also set to 0 if they are at the first song of the list
    if(audioElement.audio.currentTime >= 3 || currentIndex == 0) {
        audioElement.setTime(0);
    }
    else {
        currentIndex = currentIndex - 1;
        setTrack(currentPlaylist[currentIndex], currentPlaylist, true);
    }

}


function nextSong() {

    if(repeat) {
        audioElement.setTime(0);
        playSong();
        return;
    }

    if(currentIndex == currentPlaylist.length - 1) {
        currentIndex = 0;
    }
    else {
        currentIndex = currentIndex + 1;
    }

    var trackToPlay = shuffle ? shufflePlaylist[currentIndex] : currentPlaylist[currentIndex];
    setTrack(trackToPlay, currentPlaylist, true);
}

function setRepeat() {
    repeat = !repeat;
    // change the images to active or inactive if selected
    var imageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src", "assets/images/icons/" + imageName);
}

function setMute() {
    audioElement.audio.muted = !audioElement.audio.muted;
    var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src", "assets/images/icons/" + imageName);
}

function setShuffle() {
    shuffle = !shuffle;
    var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src", "assets/images/icons/" + imageName);

    // set the currentIndex so it won't repeat playing the same song in a row
    if(shuffle) {
        shuffleArray(shufflePlaylist);
        currentIndex = shufflePlaylist.indexOf(audioElement.currentlyPlaying.id);
    }
    else {
        currentIndex = currentPlaylist.indexOf(audioElement.currentlyPlaying.id);
    }

}

function shuffleArray(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
    return a;
}

function setTrack(trackId, newPlaylist, play) {

    // newPlaylist repesents the new playlist if you click a different
    // playlist from the one you are currently on
    if(newPlaylist != currentPlaylist) {
        currentPlaylist = newPlaylist;
        shufflePlaylist = currentPlaylist.slice();
        shuffleArray(shufflePlaylist);
    }

    if(shuffle) {
        currentIndex = shufflePlaylist.indexOf(trackId);
    }
    else {
        // set currentIndex everytime a song is selected
        currentIndex = currentPlaylist.indexOf(trackId);
    }
    pauseSong();

    // 1st parameter is page you want to make ajax call to
    // 2nd parameter values you want to pass into ajax call
    // 3rd parameter is what to do with the data
    // don't need anything other than the 1st param
    $.post("includes/handlers/ajax/getSongJson.php", { songId: trackId}, function(data) {
    

        // need to change to JSON so JS can read it
        var track = JSON.parse(data);

        $(".trackName span").text(track.title);

        $.post("includes/handlers/ajax/getArtistJson.php", { artistId: track.artist}, function(data) {
            var artist = JSON.parse(data);
            $(".trackInfo .artistName span").text(artist.name);
            // loads onclick option when song loaded
            $(".trackInfo .artistName span").attr("onclick", "openPage('artist.php?id=" + artist.id + "')");
            
        });

        $.post("includes/handlers/ajax/getAlbumJson.php", { albumId: track.album}, function(data) {
            var album = JSON.parse(data);
            $(".content .albumLink img").attr("src", album.artworkPath);
            $(".content .albumLink img").attr("onclick", "openPage('album.php?id=" + album.id + "')");
            $(".trackInfo .trackname span").attr("onclick", "openPage('album.php?id=" + album.id + "')");
        });
        audioElement.setTrack(track);

        if(play) {
            playSong();
        }
    });

}

// creating these function so we can call these functions from the html when clicking
// the play/pause button
function playSong() {

    if(audioElement.audio.currentTime == 0) {
        $.post("includes/handlers/ajax/updatePlays.php", { songId: audioElement.currentlyPlaying.id});
    }
    else {
        console.log("DONT UPDATE TIME");
    }
    // when click play, hide play button show pause button
    $(".controlButton.play").hide();
    $(".controlButton.pause").show();
    audioElement.play();
}

function pauseSong() {
    $(".controlButton.play").show();
    $(".controlButton.pause").hide();
    audioElement.pause();
}
</script>

<!-- The black now playing bar at the bottom of page -->
<div id="nowPlayingBarContainer">
    <div id="nowPlayingBar">
        <div id="nowPlayingLeft">
            <div class="content">
                <span class="albumLink">
                    <img role="link" tabindex="0" src="" alt="" class="albumArtwork">
                </span>
                <div class="trackInfo">
                    <span class="trackName">
                        <span role="link" tabindex="0"></span>
                    </span>

                    <span class="artistName">
                        <span role="link" tabindex="0"></span>
                    </span>
                </div>
            </div>
        </div>

        <div id="nowPlayingCenter">
            <!-- content primary class playerControls secondary class -->
            <div class="content playerControls">
                <div class="buttons">

                    <button class="controlButton shuffle" title="Shuffle button" onclick="setShuffle()">
                        <img src="assets/images/icons/shuffle.png" alt="Shuffle">
                    </button>

                    <button class="controlButton previous" title="Previous button" onclick="prevSong()">
                        <img src="assets/images/icons/previous.png" alt="Previous">
                    </button>

                    <button class="controlButton play" title="Play button" onClick="playSong()">
                        <img src="assets/images/icons/play.png" alt="Play">
                    </button>

                    <button class="controlButton pause" title="Pause button" style="display: none;" onClick="pauseSong()">
                        <img src="assets/images/icons/pause.png" alt="Pause">
                    </button>

                    <button class="controlButton next" title="Next button" onclick="nextSong()">
                        <img src="assets/images/icons/next.png" alt="Next">
                    </button>

                    <button class="controlButton repeat" title="Repeat button" onclick="setRepeat()">
                        <img src="assets/images/icons/repeat.png" alt="Repeat">
                    </button>

                </div>
                <div class="playbackBar">

                    <span class="progressTime current">0.00</span>

                    <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                    </div>

                    <span class="progressTime remaining">0.00</span>

                </div>
            </div>
        </div>
        <div id="nowPlayingRight">
            <div class="volumeBar">
                <button class="controlButton volume" title="Volume button" onclick="setMute()">
                    <img src="assets/images/icons/volume.png" alt="Volume">
                </button>
                <div class="progressBar">
                        <div class="progressBarBg">
                            <div class="progress"></div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
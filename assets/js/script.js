var currentPlaylist = [];
var shufflePlaylist = [];
// for whatever page we are on
var tempPlaylist = [];
var audioElement;
var mouseDown = false;
var currentIndex = 0;
var repeat = false;
var shuffle = false;
var userLoggedIn;
var timer;
$(window).scroll(function() {
    hideOptionsMenu();
})

$(document).click(function(click) {
    var target = $(click.target);
    // if what you clicked on doesn't have the class "item"
    // and or class optionsButton
    if(!target.hasClass("item") && !target.hasClass("optionsButton")) {
        hideOptionsMenu();
    }
})

// select items with the class playlist
// when this is changed, do the following
$(document).on("change", "select.playlist", function() {
    var select = $(this);
    var playlistId = select.val();
    // prev is immediate ancestor in html
    var songId = select.prev(".songId").val();

    $.post("includes/handlers/ajax/addToPlaylist.php", { playlistId: playlistId, songId: songId})
    .done(function(error) {

        if(error != "") {
            alert(error);
            return;
        }
        hideOptionsMenu();
        // make option go back to select default
        select.val("");
    });
})

function updateEmail(emailClass) {
    var emailvalue = $("." + emailClass).val();

    $.post("includes/handlers/ajax/updateEmail.php", { email: emailvalue, username: userLoggedIn})
    .done(function(response) {
        // nextAll will return all of the sibling
        $("." + emailClass).nextAll(".message").text(response);
    });
}

function updatePassword(oldPasswordClass, newPasswordClass1, newPasswordClass2) {
    var oldPassword = $("." + oldPasswordClass).val();
    var newPassword1 = $("." + newPasswordClass1).val();
    var newPassword2 = $("." + newPasswordClass2).val();

    $.post("includes/handlers/ajax/updatePassword.php", 
    { oldPassword: oldPassword, newPassword1: newPassword1, newPassword2: newPassword2, username: userLoggedIn})
    .done(function(response) {
        // nextAll will return all of the sibling
        $("." + oldPasswordClass).nextAll(".message").text(response);
    });
}
function logout() {
    $.post("includes/handlers/ajax/logout.php", function() {
        location.reload();
    });
}

function removeFromPlaylist(button, playlistId) {

    var songId = $(button).prevAll(".songId").val();
    $.post("includes/handlers/ajax/removeFromPlaylist.php", { playlistId: playlistId, songId: songId})
    .done(function (error) {
            
        if(error != "") {
            alert(error);
            return;
        }
        
        // do something when ajax returns
        openPage("playlist.php?id=" + playlistId);
    });
}
function playFirstSong() {
    setTrack(tempPlaylist[0], tempPlaylist, true);
}

function showOptionsMenu(button) {
    // prevAll is multiple ancestors
    // get hidden field songId
    var songId = $(button).prevAll(".songId").val();
    var menu = $(".optionsMenu");
    var menuWidth = menu.width();

    // take menu item and finds songId
    menu.find(".songId").val(songId);

    // scrollTop is distance from top of window to top of the document
    var scrollTop = $(window).scrollTop();
    // distance from top of the document
    var elementOffset = $(button).offset().top;

    var top = elementOffset - scrollTop;

    var left = $(button).position().left;

    menu.css({ "top": top + "px", "left": left - menuWidth + "px", "display": "inline"});
}

function hideOptionsMenu () {
    var menu = $(".optionsMenu");
    // if not displaying none, set to display none
    if(menu.css("display") != "none") {
        menu.css("display", "none");
    }
}
function deletePlaylist(playlistId) {
    var prompt = confirm("Are you sure you want to delete this playlist?");
    if(prompt) {
        $.post("includes/handlers/ajax/deletePlaylist.php", { playlistId: playlistId}).done(function (error) {
            
            if(error != "") {
                alert(error);
                return;
            }
            
            // do something when ajax returns
            openPage("yourMusic.php");
        });
    }
}
function createPlaylist() {
    var popup = prompt("Please enter the name of your playlist");

    if(popup != null) {

        // .done will have function execute when ajax call is done
        $.post("includes/handlers/ajax/createPlaylist.php", { name: popup, username: userLoggedIn}).done(function (error) {
            
            if(error != "") {
                alert(error);
                return;
            }
            
            // do something when ajax returns
            openPage("yourMusic.php");
        });
    }
}
function openPage(url) {

    // if go to new page and timer still going,
    // clear the timer for search function
    if(timer != null) {
        clearTimeout(timer);
    }

    // if url doesn't have ?, put one in there
    if(url.indexOf("?") == -1) {
        url = url + "?";
    }
    // encodes uri with their url equivalent
    // will convert any characters that don't match
    var encodedUrl = encodeURI(url + "&userLoggedIn=" + userLoggedIn);
    $("#mainContent").load(encodedUrl);
    // when change page, automatically scroll to top
    $("body").scrollTop(0);
    // puts url into history 
    history.pushState(null, null, url);
}

function formatTime(seconds) {
    var time = Math.round(seconds);
    var minutes = Math.floor(time / 60);
    var seconds = time - minutes * 60;
    var extraZero;

    var extraZero = (seconds < 10) ? "0" : "";
    return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

    var progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}


function updateVolumeProgressBar(audio) {
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

// creates class Audio
function Audio() {
    // a variable for the class
    this.currentlyPlaying;
    this.audio = document.createElement('audio');

    // once song has ended, go to the next song
    this.audio.addEventListener("ended", function() {
        nextSong();
    });

    this.audio.addEventListener("canplay", function() {
        // 'this' refers to the object that the event was called on
        var duration = formatTime(this.duration);
        $(".progressTime.remaining").text(duration);
    });

    this.audio.addEventListener("timeupdate", function () {
        // check if a duration exists first
        if(this.duration) {
            updateTimeProgressBar(this);
        }
    });

    this.audio.addEventListener("volumechange", function() {
        updateVolumeProgressBar(this);
    });

    this.setTrack = function(track) {
        this.currentlyPlaying = track;
        this.audio.src = track.path;
    }

    // so can directly call .play instead of needing to to audio.play
    this.play = function() {
        this.audio.play();
    }

    this.pause = function() {
        this.audio.pause();
    }

    this.setTime = function(seconds) {
        this.audio.currentTime = seconds;
    }
}
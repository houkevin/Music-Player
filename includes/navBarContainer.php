<div id="navBarContainer">
    <nav class="navBar">

        <!-- role tells them that it is a link, and tab will go through the links -->
        <span role="link" tabindex="0" onclick="openPage('index.php')" class="logo">
            <img src="assets/images/icons/logo.png" alt="">
        </span>
        <!-- Search bar -->
        <div class="group">

            <div class="navItem">
                <span role="link" tabindex="0" onclick="openPage('search.php')" class="navItemLink">Search
                    <img src="assets/images/icons/search.png" class="icon" alt="">
                </span>
            </div>

        </div>

        <div class="group">

            <div class="navItem">
                <span role="link" tabindex="0" onclick="openPage('browse.php')" class="navItemLink">Browse</span>
            </div>

            <div class="navItem">
                <span role="link" tabindex="0" onclick="openPage('yourMusic.php')" class="navItemLink">Your Music</span>
            </div>

            <div class="navItem">
                <span role="link" tabindex="0" onclick="openPage('settings.php')" class="navItemLink"><?php echo $userLoggedIn->getFirstAndLastName(); ?></span>
            </div>

        </div>
    </nav>
</div>
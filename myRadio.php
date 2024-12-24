<?php
include('functions.php');

$albumId = $_GET['albumId'] ?? 0;
$searchTerm = trim($_POST['searchTerm'] ?? '');
$album = null;
$result = "";

if (isset($_POST['post-audio'])) {
    $trackName = $_POST['trackName'];
    $artistName = $_POST['artistName'];
    $trackUrl = $_POST['trackUrl'];
    $artistImg = $_POST['artistImg'];
}

if ($albumId) {
    $album = getAlbumDetails($albumId);
} elseif ($searchTerm) {
    $album = searchAlbum($searchTerm);
    if (!$album) $result = "Couldn't find album";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Radio</title>
    <link rel="stylesheet" href="css/radio.css">
</head>
<body>
    <header class="header">
        <a href="index.html"><img class="logo" src="img/logo.png" alt="logo"></a>
        <div class="choose-album">
            <a href="?albumId=1"><button class="button1">Travis</button></a>
            <a href="?albumId=2"><button class="button1">Drake</button></a>
            <a href="?albumId=3"><button class="button1">Playboi</button></a>
        </div>

        <form method="POST" class="search">
            <?= $result ?>
            <h>Search for album:</h>
            <input type="search" name="searchTerm" placeholder="Search" class="search-bar" maxlength="10">
            <button type="submit" class="search-button">🔍</button>
        </form>

        <nav class="nav">
            <a href="index.html" class="nav-item">Home</a>
            <a href="myRadio.php" class="nav-item">My Radio</a>
        </nav>
    </header>

    <div class="container-radio">
        <?php
        if (!$album) {
            displayAllAlbums(); // Display all albums when no album is selected
        } else {
            displaySingleAlbum($album, true); // Display the single album details
        }
        ?>
    </div>

    <?php if (isset($trackName)): ?>
    <footer class="footer">
        <div class="name-footer">
            <p>Now Playing: <?= $trackName ?></p>
            <p>Artist: <?= $artistName ?></p>
        </div>
        <audio controls class="audio-player" src="<?= $trackUrl ?>" autoplay></audio>
        <img class="albumCover-footer" src="<?= $artistImg ?>" alt="Album Cover">
        <a href="?albumId=0" class="button-back1"><button>Back</button></a></nav>
    </footer>
<?php endif; ?>

</body>
</html>

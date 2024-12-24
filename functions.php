<?php
session_start();

function connectDB() {
    return new mysqli("localhost", "root", "", "mymusic");
}

function getAlbumDetails($albumId) {
    $con = connectDB();
    $album = $con->query("SELECT * FROM albums WHERE albumId = $albumId")->fetch_assoc();
    $artist = $con->query("SELECT * FROM artists WHERE albumId = $albumId")->fetch_assoc();
    $trackResult = $con->query("SELECT * FROM tracks WHERE albumId = $albumId");

    $tracks = [];
    while ($track = $trackResult->fetch_assoc()) {
        $tracks[] = ['name' => $track['trackName'], 'url' => $track['TrackAudio']];
    }

    $con->close();

    return $album ? [
        'id' => $albumId,
        'title' => $album['albumTitle'],
        'cover' => $album['albumCover'],
        'video' => $album['albumVid'],
        'artist' => $artist['artistName'],
        'info' => $artist['ArtistInfo'],
        'img' => $artist['artistImg'],
        'tracks' => $tracks
    ] : null;
}

function albums() {
    $con = connectDB();
    $result = $con->query("SELECT albumId FROM albums");
    $albums = [];
    while ($row = $result->fetch_assoc()) {
        $albums[] = getAlbumDetails($row['albumId']);
    }
    $con->close();
    return $albums;
}

function displayAllAlbums() {
    foreach (albums() as $albumData) {
        echo '<div class="album-container">';
        echo '<div class="album-cover-video">';
        echo '<div class="album-cover-large">';
        echo '<a href="?albumId=' . $albumData['id'] . '"><img class="albumCover" src="' . $albumData['img'] . '" alt="Album Cover"></a>';
        echo '</div>';
        echo '<div class="album-video">';
        echo '<video src="' . $albumData['video'] . '" controls preload="metadata" poster="' . $albumData['img'] . '"></video>';
        echo '</div>';
        echo '</div>';
        
        // Display album details in a table
        echo '<table class="album-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' .'<a href="?albumId=' . $albumData['id'] .'"><img class="tableCover" src="' . $albumData['cover'] . '"></a></th>';
        echo '<th>Artist</th>';
        echo '<th>Track</th>';
        echo '<th>Play</th>';  // Play button for each track
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($albumData['tracks'] as $track) {
            echo '<tr>';
            echo '<td>' . $albumData['title'] . '</td>';
            echo '<td>' . $albumData['artist'] . '</td>';
            echo '<td>' . $track['name'] . '</td>';
            echo '<td>';
            // Display Play button for each track
            echo '<form method="POST">';
            foreach (['trackUrl' => $track['url'], 'artistImg' => $albumData['img'], 'trackName' => $track['name'], 'artistName' => $albumData['artist'], 'albumCover' => $albumData['cover']] as $name => $value) {
                echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
            }
            echo '<button class="play-button" type="submit" name="post-audio">Play</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    }
}
function displaySingleAlbum($album, $showInfo = false) {
    echo '<div class="Cover-Video">';
    
    echo '<form action="" method="POST">';
    echo '<img class="albumCover1" src="' . $album['img'] . '" alt="Album Cover">';
    echo '</form>';

    echo '<form action="" method="POST">';
    echo '<img class="albumCover1" src="' . $album['cover'] . '" alt="Album Cover">';
    if ($album['video']) {
        echo '<video src="' . $album['video'] . '" controls preload="metadata" poster="' . $album['cover'] . '"></video>';
    }
    echo '</form>';
    echo '</div>';
    echo '<div class="albumContent">';

    echo '<h3>Album: ' . $album['title'] . '</h3>';
    echo '<h3>Artist: ' . $album['artist'] . '</h3>';

    // Back Button
    echo '<a href="?albumId=0"><button class="button-back">Back</button></a>';

    // Track Details with Play button
    foreach ($album['tracks'] as $track) {
        echo '<p>Track Name: ' . $track['name'] . '</p>';
        echo '<form method="POST">';
        echo '<input type="hidden" name="trackUrl" value="' . $track['url'] . '">';
        echo '<input type="hidden" name="trackName" value="' . $track['name'] . '">';
        echo '<input type="hidden" name="artistName" value="' . $album['artist'] . '">';
        echo '<input type="hidden" name="albumCover" value="' . $album['cover'] . '">';
        echo '<input type="hidden" name="artistImg" value="' . $album['img'] . '">'; // Ensure artist image is included
        echo '<button class="play-button" type="submit" name="post-audio">Play</button>';
        echo '</form>';
    }

    // Info about the Album
    if ($showInfo) {
        echo '<h3>Info:</h3><p class="album-info">' . $album['info'] . '</p>';
    }

    echo '</div>'; // End of albumContent
}

function searchAlbum($searchTerm) {
    $searchMap = [
        "Album 1" => 1,
        "Utopia" => 1,
        "Travis" => 1,
        "Album 2" => 2,
        "Cali Love" => 2,
        "Tupac" => 2,
        "Album 3" => 3,
        "Die lit" => 3, 
        "Playboi" => 3
    ];
    return isset($searchMap[$searchTerm]) ? getAlbumDetails($searchMap[$searchTerm]) : null;
}

?>

<?php
// Load functions and connect to database
require_once '_functions.php';
$db = connectToDb();

// Check if user is logged in
isLoggedIn();

// Set page title and include top partial
$title = "Log Concert - Jukeboxd";
include 'partials/top.php';

?>

<body>
    <?php include 'partials/header.php'; ?>
    <div class="container">

        <form action="x_save_log.php" method="post" class ="card">
            <h1>Log Concert</h1>
            <?php writeMessage("empty"); ?>  

            <!-- Artist searchable dropdown -->
            <input type="text" id="artist-search" placeholder="Search for an artist..." autocomplete="off">
            <ul id="artist-suggestions"></ul>
            <input type="hidden" name="artist_id" id="artist-id-input">

            <!-- Concert dropdown — hidden until artist is chosen -->
            <select name="concert_id" id="concert-select" style="display:none;">
                <option value="">Select a concert</option>
            </select>

            <div class="rating">
                <input type="radio" name="rating" id="str5" value="5">
                <label for="str5">★</label>
                <input type="radio" name="rating" id="str4" value="4">
                <label for="str4">★</label>
                <input type="radio" name="rating" id="str3" value="3">
                <label for="str3">★</label>
                <input type="radio" name="rating" id="str2" value="2">
                <label for="str2">★</label>
                <input type="radio" name="rating" id="str1" value="1">
                <label for="str1">★</label>
                <p>Your rating:</p>
            </div>
            
            <textarea name="review" placeholder="Write a review here..." rows="6"></textarea>
            <input type="submit" value="Log">
        </form>
        
        <p>Does your artist or concert not exist in our database yet? Add it!</p>
        <a href="database.php" class="btn">To database →</a>
        
    </div>

</body>

</html>

<script>
    // Load all artists from DB into JS
    const allArtists = <?php
        $result = $db->query("SELECT id, name FROM jb_artists ORDER BY name ASC");
        $artists = [];
        while ($row = $result->fetch_assoc()) {
            $artists[] = $row;
        }
        echo json_encode($artists);
    ?>;

    const searchInput = document.getElementById('artist-search');
    const suggestionsList = document.getElementById('artist-suggestions');
    const artistIdInput = document.getElementById('artist-id-input');
    const concertSelect = document.getElementById('concert-select');

    // Filter and show suggestions as user types
    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        suggestionsList.innerHTML = '';
        artistIdInput.value = '';
        concertSelect.style.display = 'none';
        concertSelect.innerHTML = '<option value="">Select a concert</option>';

        if (!query) return;

        const matches = allArtists.filter(a => a.name.toLowerCase().includes(query));
        matches.forEach(artist => {
            const li = document.createElement('li');
            li.textContent = artist.name;
            li.dataset.id = artist.id;
            li.addEventListener('click', () => selectArtist(artist));
            suggestionsList.appendChild(li);
        });
    });

    function selectArtist(artist) {
        searchInput.value = artist.name;
        artistIdInput.value = artist.id;
        suggestionsList.innerHTML = '';

        // Fetch concerts for this artist
        fetch(`get_concerts.php?artist_id=${artist.id}`)
            .then(res => res.json())
            .then(concerts => {
                concertSelect.innerHTML = '<option value="">Select a concert</option>';
                if (concerts.length === 0) {
                    concertSelect.innerHTML += '<option disabled>No concerts found</option>';
                } else {
                    concerts.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.id;
                        option.textContent = `${c.name} — ${c.date}`;
                        concertSelect.appendChild(option);
                    });
                }
                concertSelect.style.display = 'block';
            });
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target)) {
            suggestionsList.innerHTML = '';
        }
    });
</script>

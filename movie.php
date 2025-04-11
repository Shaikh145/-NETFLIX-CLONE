<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

// Check if movie ID is provided
if(!isset($_GET['id'])) {
    echo "<script>window.location.href = 'home.php';</script>";
    exit;
}

$movie_id = $_GET['id'];

// Database connection
$conn = new mysqli('localhost', 'uklz9ew3hrop3', 'zyrbspyjlzjb', 'db9ranxpmtccqq');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get movie details
$movie_query = "SELECT * FROM movies WHERE id = $movie_id";
$movie_result = $conn->query($movie_query);

// If movie doesn't exist in database, use dummy data
if ($movie_result && $movie_result->num_rows > 0) {
    $movie = $movie_result->fetch_assoc();
} else {
    // Dummy movie data based on ID
    $dummy_movies = [
        1 => [
            'id' => 1,
            'title' => 'Stranger Things',
            'description' => 'When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.',
            'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABTAHnpUZ0JOZkZQsO9-a3xo-mGh7n6RQzBphGWjbwqgs0INVyQdPGYyJGtw7x_T_upkqZKz-P2fLEQp-dzFM0YGNKvfqeXdW1Q.jpg?r=776',
            'banner' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/6AYY37jfdO6hpXcMjf9Yu5cnmO0/AAAABer7SeWc6RxbBas7-7-6C7ydCPGDCUMsC2Qf2yAJGVDnGCHEyX7E8-NQFXUXz7jhxnlYCN2RdXAwoB2Z2mwPZi_CQbRG1_o80j9Z.jpg?r=608',
            'year' => '2016',
            'duration' => '50m',
            'category' => 'Sci-Fi & Fantasy',
            'rating' => 'TV-14',
            'cast' => 'Millie Bobby Brown, Finn Wolfhard, Noah Schnapp, Caleb McLaughlin, Gaten Matarazzo',
            'director' => 'The Duffer Brothers',
            'video_url' => 'https://www.youtube.com/embed/b9EkMc79ZSU'
        ],
        2 => [
            'id' => 2,
            'title' => 'The Witcher',
            'description' => 'Geralt of Rivia, a mutated monster-hunter for hire, journeys toward his destiny in a turbulent world where people often prove more wicked than beasts.',
            'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABbXsingJm-8NvTkn8XvBMxdUVe0RFdT93-Bhn5o9vjkHxpM-xpWWJ8J7F6YJJgbwxK7tUJfZXRyGVK1mIcTrz7NyO0McwN9D4A.jpg?r=9d5',
            'banner' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/6AYY37jfdO6hpXcMjf9Yu5cnmO0/AAAABXYofKdCJceEP7pdxcEZ9wt80GsxEyXIbnG_QM8znksNz3JexvlGVgYTDQKI_1-4pBB7_TKwHHyqtYYxh8kUChORn2H0ipk3k4Hs.jpg?r=cad',
            'year' => '2019',
            'duration' => '60m',
            'category' => 'Fantasy Drama',
            'rating' => 'TV-MA',
            'cast' => 'Henry Cavill, Anya Chalotra, Freya Allan',
            'director' => 'Lauren Schmidt Hissrich',
            'video_url' => 'https://www.youtube.com/embed/ndl1W4ltcmg'
        ],
        3 => [
            'id' => 3,
            'title' => 'Money Heist',
            'description' => 'Eight thieves take hostages and lock themselves in the Royal Mint of Spain as a criminal mastermind manipulates the police to carry out his plan.',
            'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQnCKOBbn7_kKRGdWxmIeccDjJLXZZVZPVXBTxqC4RXE9x2dOXbYZKPFNW2QRIrOJjl2-G5Vd2lCCpVlZUJYoCdpP5-8UEHBvQ.jpg?r=5e0',
            'banner' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/6AYY37jfdO6hpXcMjf9Yu5cnmO0/AAAABcgSMZVe3GtMhV9V8sPeKVktnKLuTKZUdbbcBLCJ_xHqRUXQDDnF5FI0QxfOuJJnVR3eNzWK-hCJ-O_3xaN-p-yGJHxkiAQ4i-Ib.jpg?r=6c6',
            'year' => '2017',
            'duration' => '50m',
            'category' => 'Crime Drama',
            'rating' => 'TV-MA',
            'cast' => 'Úrsula Corberó, Álvaro Morte, Itziar Ituño',
            'director' => 'Álex Pina',
            'video_url' => 'https://www.youtube.com/embed/_InqQJRqGW4'
        ],
        // Add more dummy movies as needed
        4 => [
            'id' => 4,
            'title' => 'Squid Game',
            'description' => 'Hundreds of cash-strapped players accept a strange invitation to compete in children\'s games. Inside, a tempting prize awaits — with deadly high stakes.',
            'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABfDw9SVGj3JsZmcFNNZHM1-wJnKYfQXvyMTAn5Jbn-_D2wJGR_CZtrvKxjcMyBKA6r6FQJ9CnIky-Ms7TwEzRNF9I8UUG5wrAQ.jpg?r=31d',
            'banner' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/6AYY37jfdO6hpXcMjf9Yu5cnmO0/AAAABcgSMZVe3GtMhV9V8sPeKVktnKLuTKZUdbbcBLCJ_xHqRUXQDDnF5FI0QxfOuJJnVR3eNzWK-hCJ-O_3xaN-p-yGJHxkiAQ4i-Ib.jpg?r=6c6',
            'year' => '2021',
            'duration' => '55m',
            'category' => 'Thriller',
            'rating' => 'TV-MA',
            'cast' => 'Lee Jung-jae, Park Hae-soo, Wi Ha-jun',
            'director' => 'Hwang Dong-hyuk',
            'video_url' => 'https://www.youtube.com/embed/oqxAJKy0ii4'
        ],
    ];
    
    // If the ID exists in our dummy data, use it
    if(isset($dummy_movies[$movie_id])) {
        $movie = $dummy_movies[$movie_id];
    } else {
        // Default to first movie if ID not found
        $movie = $dummy_movies[1];
    }
}

// Get similar movies (same category)
$category = $movie['category'] ?? 'Drama';
$similar_query = "SELECT * FROM movies WHERE category = '$category' AND id != $movie_id LIMIT 6";
$similar_result = $conn->query($similar_query);

// Dummy similar movies if none in database
$similar_movies = [];
if ($similar_result && $similar_result->num_rows > 0) {
    while($row = $similar_result->fetch_assoc()) {
        $similar_movies[] = $row;
    }
} else {
    // Use dummy data
    $similar_movies = [
        ['id' => 5, 'title' => 'Wednesday', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZf5kiRwzux5yAMUW4UiUK2HsQVYQQUOVT61B7kKtbzTmju_aLYk1VXeHkYqJ8cczJUex0FVb6H4RGTlo-S_-9HZjr8nZ-Rvug.jpg?r=198'],
        ['id' => 6, 'title' => 'Dark', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABd-iJOnLkBO7tPRFS6VNfDmHioAqvaD630vP1dKmMnWIJXUyMEyRj9q6WVQHWEUTcJGXPVzk0UfLQHS0ORU4lAHULuZwXCm8Pg.jpg?r=01d'],
        ['id' => 7, 'title' => 'The Crown', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABfzPBJrjkQMZ8wefQqIGZsXQFQS7yRm0ySvGJrS_YhbdHrVYgWK1E2eB6DcJirHXJT0RXXJMDaCpLLv9bWkTGDFnSiUhn5BMpQ.jpg?r=077'],
        ['id' => 8, 'title' => 'Bridgerton', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABUkSkU5s-0HYg5kYr-XZ2jf7aLLHvWw7g5zUmukGCnIZwEJEMlEqZ6KK1n_yHX91LyPJOkKZGOJkRJlUyJ2yEJxkbjs7Xrm0Ow.jpg?r=bcd'],
        ['id' => 9, 'title' => 'The Queen\'s Gambit', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABSQRoUhjzq7kOFGCaUninzg9QWcXSzZE0FSKPojKJv4HxDJw-0_pHcz_RkVRVNwPLHVL3s_8nUNHXcJAZpYmDnH0ypagRgqSIQ.jpg?r=93f'],
        ['id' => 10, 'title' => 'Ozark', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABccYrFPFsrvBLvv7T-UIiL-JqDnXRQdVTJxw3yDuRNkYFtxos8S0p2rGlz4XmOIrpIxbVw7mj7UxqDTGLCaNmgJ1obgHKL-Jrw.jpg?r=01d'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie['title']; ?> - Netflix Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #141414;
            color: #fff;
            min-height: 100vh;
        }
        
        .header {
            background-color: rgba(20, 20, 20, 0.9);
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: background-color 0.3s;
        }
        
        .header.scrolled {
            background-color: #141414;
        }
        
        .logo {
            color: #e50914;
            font-size: 2rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
        }
        
        .nav-links a {
            color: #e5e5e5;
            text-decoration: none;
            margin-left: 20px;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #b3b3b3;
        }
        
        .user-dropdown {
            position: relative;
            display: inline-block;
            margin-left: 20px;
        }
        
        .user-dropdown-btn {
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        
        .user-icon {
            width: 32px;
            height: 32px;
            background-color: #e50914;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: rgba(0, 0, 0, 0.9);
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
        }
        
        .dropdown-content a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }
        
        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .user-dropdown:hover .dropdown-content {
            display: block;
        }
        
        .movie-banner {
            height: 80vh;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 0 50px;
        }
        
        .movie-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(20, 20, 20, 1) 0%, rgba(20, 20, 20, 0) 50%);
            z-index: 1;
        }
        
        .movie-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
            margin-bottom: 50px;
        }
        
        .movie-title {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .movie-meta {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
            color: #a3a3a3;
        }
        
        .movie-meta span {
            margin-right: 15px;
        }
        
        .movie-meta .rating {
            border: 1px solid #a3a3a3;
            padding: 2px 5px;
        }
        
        .movie-description {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #e5e5e5;
            line-height: 1.5;
        }
        
        .movie-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .btn-play {
            background-color: #fff;
            color: #000;
        }
        
        .btn-play:hover {
            background-color: rgba(255, 255, 255, 0.75);
        }
        
        .btn-list {
            background-color: rgba(109, 109, 110, 0.7);
            color: #fff;
        }
        
        .btn-list:hover {
            background-color: rgba(109, 109, 110, 0.4);
        }
        
        .btn i {
            margin-right: 5px;
        }
        
        .movie-details {
            padding: 50px;
        }
        
        .details-row {
            display: flex;
            margin-bottom: 30px;
        }
        
        .details-col {
            flex: 1;
        }
        
        .details-title {
            font-size: 1.2rem;
            color: #a3a3a3;
            margin-bottom: 10px;
        }
        
        .details-content {
            font-size: 1rem;
            line-height: 1.5;
        }
        
        .section {
            padding: 20px 50px;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
        
        .movie-row {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 20px;
            scrollbar-width: none; /* Firefox */
        }
        
        .movie-row::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        
        .movie-card {
            flex: 0 0 auto;
            width: 250px;
            transition: transform 0.3s;
            position: relative;
            cursor: pointer;
        }
        
        .movie-card:hover {
            transform: scale(1.05);
            z-index: 10;
        }
        
        .movie-poster {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .movie-card-title {
            margin-top: 8px;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .movie-banner {
                height: 60vh;
                padding: 0 20px;
            }
            
            .movie-title {
                font-size: 2rem;
            }
            
            .movie-details, .section {
                padding: 20px;
            }
            
            .details-row {
                flex-direction: column;
            }
            
            .details-col {
                margin-bottom: 20px;
            }
            
            .movie-card {
                width: 150px;
            }
            
            .movie-poster {
                height: 85px;
            }
        }
    </style>
</head>
<body>
    <div class="header" id="header">
        <a href="home.php" class="logo">Netflix</a>
        
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="home.php#trending">TV Shows</a>
            <a href="home.php#new-releases">Movies</a>
            <a href="home.php#action">My List</a>
            
            <div class="user-dropdown">
                <button class="user-dropdown-btn">
                    <div class="user-icon"><?php echo substr($_SESSION['user_name'], 0, 1); ?></div>
                    <span class="caret">▼</span>
                </button>
                <div class="dropdown-content">
                    <a href="profile.php">Profile</a>
                    <a href="upload.php">Upload Movie</a>
                    <a href="logout.php" id="logout-link">Sign Out</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="movie-banner" style="background-image: url('<?php echo $movie['banner'] ?? $movie['poster']; ?>');">
        <div class="movie-content">
            <h1 class="movie-title"><?php echo $movie['title']; ?></h1>
            
            <div class="movie-meta">
                <span><?php echo $movie['year']; ?></span>
                <span class="rating"><?php echo $movie['rating']; ?></span>
                <span><?php echo $movie['duration']; ?></span>
            </div>
            
            <p class="movie-description"><?php echo $movie['description']; ?></p>
            
            <div class="movie-buttons">
                <a href="player.php?id=<?php echo $movie['id']; ?>" class="btn btn-play">▶ Play</a>
                <a href="#" class="btn btn-list">+ My List</a>
            </div>
        </div>
    </div>
    
    <div class="movie-details">
        <div class="details-row">
            <div class="details-col">
                <h3 class="details-title">Cast</h3>
                <p class="details-content"><?php echo $movie['cast']; ?></p>
            </div>
            
            <div class="details-col">
                <h3 class="details-title">Director</h3>
                <p class="details-content"><?php echo $movie['director']; ?></p>
            </div>
            
            <div class="details-col">
                <h3 class="details-title">Genre</h3>
                <p class="details-content"><?php echo $movie['category']; ?></p>
            </div>
        </div>
    </div>
    
    <div class="section">
        <h2 class="section-title">More Like This</h2>
        <div class="movie-row">
            <?php foreach($similar_movies as $similar): ?>
                <div class="movie-card" onclick="goToMovie(<?php echo $similar['id']; ?>)">
                    <img src="<?php echo $similar['poster']; ?>" alt="<?php echo $similar['title']; ?>" class="movie-poster">
                    <div class="movie-card-title"><?php echo $similar['title']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // JavaScript for header background change on scroll
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Function to navigate to movie details page
        function goToMovie(id) {
            window.location.href = 'movie.php?id=' + id;
        }
        
        // JavaScript for redirection
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'logout.php';
        });
    </script>
</body>
</html>

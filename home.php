<?php
session_start();
// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'uklz9ew3hrop3', 'zyrbspyjlzjb', 'db9ranxpmtccqq');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get trending movies
$trending_query = "SELECT * FROM movies WHERE trending = 1 ORDER BY id DESC LIMIT 10";
$trending_result = $conn->query($trending_query);

// Get new releases
$new_releases_query = "SELECT * FROM movies WHERE release_date >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY release_date DESC LIMIT 10";
$new_releases_result = $conn->query($new_releases_query);

// Get action movies
$action_query = "SELECT * FROM movies WHERE category = 'Action' ORDER BY id DESC LIMIT 10";
$action_result = $conn->query($action_query);

// Get comedy movies
$comedy_query = "SELECT * FROM movies WHERE category = 'Comedy' ORDER BY id DESC LIMIT 10";
$comedy_result = $conn->query($comedy_query);

// Get drama movies
$drama_query = "SELECT * FROM movies WHERE category = 'Drama' ORDER BY id DESC LIMIT 10";
$drama_result = $conn->query($drama_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Netflix Clone</title>
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
        
        .main-content {
            padding-top: 80px;
            padding-bottom: 50px;
        }
        
        .hero-banner {
            height: 80vh;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding: 0 50px;
        }
        
        .hero-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(20, 20, 20, 1) 0%, rgba(20, 20, 20, 0) 50%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 500px;
            margin-bottom: 50px;
        }
        
        .hero-title {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .hero-description {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #e5e5e5;
        }
        
        .hero-buttons {
            display: flex;
            gap: 10px;
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
        
        .btn-more {
            background-color: rgba(109, 109, 110, 0.7);
            color: #fff;
        }
        
        .btn i {
            margin-right: 5px;
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
        
        .movie-title {
            margin-top: 8px;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .movie-info {
            display: none;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0) 100%);
            padding: 10px;
            border-radius: 0 0 4px 4px;
        }
        
        .movie-card:hover .movie-info {
            display: block;
        }
        
        .upload-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #e50914;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            z-index: 100;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .hero-banner {
                height: 60vh;
                padding: 0 20px;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .section {
                padding: 15px 20px;
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
            <a href="home.php" class="active">Home</a>
            <a href="#trending">TV Shows</a>
            <a href="#new-releases">Movies</a>
            <a href="#action">My List</a>
            
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
    
    <div class="main-content">
        <div class="hero-banner" style="background-image: url('https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABTAHnpUZ0JOZkZQsO9-a3xo-mGh7n6RQzBphGWjbwqgs0INVyQdPGYyJGtw7x_T_upkqZKz-P2fLEQp-dzFM0YGNKvfqeXdW1Q.jpg?r=776');">
            <div class="hero-content">
                <h1 class="hero-title">Stranger Things</h1>
                <p class="hero-description">When a young boy vanishes, a small town uncovers a mystery involving secret experiments, terrifying supernatural forces and one strange little girl.</p>
                <div class="hero-buttons">
                    <a href="player.php?id=1" class="btn btn-play">▶ Play</a>
                    <a href="movie.php?id=1" class="btn btn-more">ⓘ More Info</a>
                </div>
            </div>
        </div>
        
        <div class="section" id="trending">
            <h2 class="section-title">Trending Now</h2>
            <div class="movie-row">
                <?php
                if ($trending_result && $trending_result->num_rows > 0) {
                    while($movie = $trending_result->fetch_assoc()) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    // Dummy data if no movies in database
                    $dummy_movies = [
                        ['id' => 1, 'title' => 'Stranger Things', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABTAHnpUZ0JOZkZQsO9-a3xo-mGh7n6RQzBphGWjbwqgs0INVyQdPGYyJGtw7x_T_upkqZKz-P2fLEQp-dzFM0YGNKvfqeXdW1Q.jpg?r=776'],
                        ['id' => 2, 'title' => 'The Witcher', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABbXsingJm-8NvTkn8XvBMxdUVe0RFdT93-Bhn5o9vjkHxpM-xpWWJ8J7F6YJJgbwxK7tUJfZXRyGVK1mIcTrz7NyO0McwN9D4A.jpg?r=9d5'],
                        ['id' => 3, 'title' => 'Money Heist', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQnCKOBbn7_kKRGdWxmIeccDjJLXZZVZPVXBTxqC4RXE9x2dOXbYZKPFNW2QRIrOJjl2-G5Vd2lCCpVlZUJYoCdpP5-8UEHBvQ.jpg?r=5e0'],
                        ['id' => 4, 'title' => 'Squid Game', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABfDw9SVGj3JsZmcFNNZHM1-wJnKYfQXvyMTAn5Jbn-_D2wJGR_CZtrvKxjcMyBKA6r6FQJ9CnIky-Ms7TwEzRNF9I8UUG5wrAQ.jpg?r=31d'],
                        ['id' => 5, 'title' => 'Wednesday', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZf5kiRwzux5yAMUW4UiUK2HsQVYQQUOVT61B7kKtbzTmju_aLYk1VXeHkYqJ8cczJUex0FVb6H4RGTlo-S_-9HZjr8nZ-Rvug.jpg?r=198'],
                        ['id' => 6, 'title' => 'Dark', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABd-iJOnLkBO7tPRFS6VNfDmHioAqvaD630vP1dKmMnWIJXUyMEyRj9q6WVQHWEUTcJGXPVzk0UfLQHS0ORU4lAHULuZwXCm8Pg.jpg?r=01d'],
                        ['id' => 7, 'title' => 'The Crown', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABfzPBJrjkQMZ8wefQqIGZsXQFQS7yRm0ySvGJrS_YhbdHrVYgWK1E2eB6DcJirHXJT0RXXJMDaCpLLv9bWkTGDFnSiUhn5BMpQ.jpg?r=077'],
                    ];
                    
                    foreach($dummy_movies as $movie) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="section" id="new-releases">
            <h2 class="section-title">New Releases</h2>
            <div class="movie-row">
                <?php
                if ($new_releases_result && $new_releases_result->num_rows > 0) {
                    while($movie = $new_releases_result->fetch_assoc()) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    // Dummy data if no movies in database
                    $dummy_movies = [
                        ['id' => 8, 'title' => 'Bridgerton', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABUkSkU5s-0HYg5kYr-XZ2jf7aLLHvWw7g5zUmukGCnIZwEJEMlEqZ6KK1n_yHX91LyPJOkKZGOJkRJlUyJ2yEJxkbjs7Xrm0Ow.jpg?r=bcd'],
                        ['id' => 9, 'title' => 'The Queen\'s Gambit', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABSQRoUhjzq7kOFGCaUninzg9QWcXSzZE0FSKPojKJv4HxDJw-0_pHcz_RkVRVNwPLHVL3s_8nUNHXcJAZpYmDnH0ypagRgqSIQ.jpg?r=93f'],
                        ['id' => 10, 'title' => 'Ozark', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABccYrFPFsrvBLvv7T-UIiL-JqDnXRQdVTJxw3yDuRNkYFtxos8S0p2rGlz4XmOIrpIxbVw7mj7UxqDTGLCaNmgJ1obgHKL-Jrw.jpg?r=01d'],
                        ['id' => 11, 'title' => 'Peaky Blinders', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQm4D-DFPZS0NwW3ePZ-kMYyaJwsXhSQhYLLYLnNQQnl9FGNgdMUJ9Q9WuAchovjj1CjbEgA_JgEQKj_-Nt2QI1CcKAJDWnKcw.jpg?r=5d1'],
                        ['id' => 12, 'title' => 'Cobra Kai', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABdGP9vxKXqOBUFy8Vb3fvZ8O0kh3fOP72eqBTEDXAj0ZFzFbxPeWNcgbwikLz3jsPnCjFYWvTNm0jKINqEwLB-2rcVNGYhTu_g.jpg?r=c7e'],
                        ['id' => 13, 'title' => 'The Umbrella Academy', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABaUyF3kexjPto1xVx3LK9liRefTi7QfYbxaakYpKUYQJqGpTwmzs_GnHXkDKIwFLbOxcl_GiQB-0ST-3AcR8vZ6I5Nz4Vl1Zzw.jpg?r=4c7'],
                        ['id' => 14, 'title' => 'You', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABSRtxd1-rCOpAgRiLuMvy9pqz_R1Y4cWbmL-t1hAwe9W3JpHm-bqZQhQwDKjpJ6VxJ9PskrQaE9yaZB3XLZJYn9GHG9N0s8qQA.jpg?r=2f8'],
                    ];
                    
                    foreach($dummy_movies as $movie) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="section" id="action">
            <h2 class="section-title">Action & Adventure</h2>
            <div class="movie-row">
                <?php
                if ($action_result && $action_result->num_rows > 0) {
                    while($movie = $action_result->fetch_assoc()) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    // Dummy data if no movies in database
                    $dummy_movies = [
                        ['id' => 15, 'title' => 'Extraction', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABTAJNWnZ8OMkKxaOtfPsqIBJMvH-ya3-YEJBqzGJPE1xS89-xE4oVxQRUUQxRJEULyWZXFcwUx7hIKEUOTiWJo0UOJxWEJLYlA.jpg?r=e40'],
                        ['id' => 16, 'title' => 'The Old Guard', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQnKu5c-aqUYC-n9unYYGmUO0MCbhP5GUkLAIwPbcNAIxEW7ZfZ5FVQC4VTZUBUUOBIXvM3bLEbQwHlNpJoqZnVLyAHJGcqJlA.jpg?r=b45'],
                        ['id' => 17, 'title' => 'Red Notice', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABSYTAFECLcJGDgkyQZFfPBbMen7Hd2yYQrGsRUkmV1HZcWDXqoTvZbHOUJcTOIYEFDwfNnzxeJcFmKQCUF-LxHGVXCxOXYWNiQ.jpg?r=233'],
                        ['id' => 18, 'title' => 'The Gray Man', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABfwTttu9DP-Bnbi1LQgPeVLfgbThrALzCKJGXInKWHMTGgE5CF2mmZd0Jg-XECwOGJpS_qvkiYdCQtBJEQXoLqMCjP0XnMNKrQ.jpg?r=720'],
                        ['id' => 19, 'title' => '6 Underground', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQlBF9XgaQUEZ-KFYpGjUm5wUKlZZw5qYGYZeE5cVwjamcXzxnlTBHXEmoMcWL6c0zyclkCZfBXRmoDfmwEjOKYxJhHpnr56Aw.jpg?r=e19'],
                        ['id' => 20, 'title' => 'Army of the Dead', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABbFdNS9Iu_39KF_ZEMPvCq1E-4YkCjx9OvYKSLYYquoLwbJpAUyGGcMZWMKZcrf8LZ3SDtFbwGwAZOJNki-HUmfUYXHyuGPLdQ.jpg?r=e24'],
                        ['id' => 21, 'title' => 'Triple Frontier', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABVx3476GKnz9HMW5vO3UYpZH-jkOG9jIxMwcRcfgL3LFk4SHnhYSPUJLbBPwUVaVmJbm5prQJkTcMNsZTlmDrZ1VwkHvJvK0Aw.jpg?r=608'],
                    ];
                    
                    foreach($dummy_movies as $movie) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="section" id="comedy">
            <h2 class="section-title">Comedy</h2>
            <div class="movie-row">
                <?php
                if ($comedy_result && $comedy_result->num_rows > 0) {
                    while($movie = $comedy_result->fetch_assoc()) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    // Dummy data if no movies in database
                    $dummy_movies = [
                        ['id' => 22, 'title' => 'Don\'t Look Up', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABf8J_6TRjreQKE_9aBSQoWtpwGFTLWGzCwpWGEBGT-FA-34xTpbdPX_bXQqUEl8eGF7CUlQCYEpwhjKEM_vktgTmDJx70-qgQQ.jpg?r=700'],
                        ['id' => 23, 'title' => 'Murder Mystery', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZBhxVQHzZk7iqOl9tKLRNrKI3lMgBVxz6l0M0iJ9ky9bUEg9qYwxfaUsLYL5GKV9-0XJVmCyNYQyXz5sCBLY0EFVgEbGd0FQA.jpg?r=082'],
                        ['id' => 24, 'title' => 'The Adam Project', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABaUyF3kexjPto1xVx3LK9liRefTi7QfYbxaakYpKUYQJqGpTwmzs_GnHXkDKIwFLbOxcl_GiQB-0ST-3AcR8vZ6I5Nz4Vl1Zzw.jpg?r=4c7'],
                        ['id' => 25, 'title' => 'Eurovision Song Contest', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABYCXCfVkAFjsSQJGuFsHPZyGpDCXwF7FeBTgNcnE7Qd1MK5M3knOJI8J87YcHABNTJNlNLP-Mjm_7tGE-3LQOw9RaKnCVYeU_g.jpg?r=231'],
                        ['id' => 26, 'title' => 'The Mitchells vs. The Machines', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABcZHzDzM0RXgXBKqEQcjFCEYnxEdSS5NIBcN0kKQTlEQAY0YZFNn6NZF3bZLdpxqX8UtDFxuVm_3pxKcDpeLEFNDxZCJgJAKrQ.jpg?r=a44'],
                        ['id' => 27, 'title' => 'Always Be My Maybe', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZemvLhpYdiWfTcKL-eQ9XYV7fNiPp3dMbi5XxBwUJ-F-iYl9GbCRNkjXVWQfYxMEMUTWHu_WMiva9MfwEKt7ZJzk5zZKzLDzQ.jpg?r=5c0'],
                        ['id' => 28, 'title' => 'Space Force', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQnKu5c-aqUYC-n9unYYGmUO0MCbhP5GUkLAIwPbcNAIxEW7ZfZ5FVQC4VTZUBUUOBIXvM3bLEbQwHlNpJoqZnVLyAHJGcqJlA.jpg?r=b45'],
                    ];
                    
                    foreach($dummy_movies as $movie) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
        
        <div class="section" id="drama">
            <h2 class="section-title">Drama</h2>
            <div class="movie-row">
                <?php
                if ($drama_result && $drama_result->num_rows > 0) {
                    while($movie = $drama_result->fetch_assoc()) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                } else {
                    // Dummy data if no movies in database
                    $dummy_movies = [
                        ['id' => 29, 'title' => 'The Irishman', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABbFdNS9Iu_39KF_ZEMPvCq1E-4YkCjx9OvYKSLYYquoLwbJpAUyGGcMZWMKZcrf8LZ3SDtFbwGwAZOJNki-HUmfUYXHyuGPLdQ.jpg?r=e24'],
                        ['id' => 30, 'title' => 'Marriage Story', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABQnKu5c-aqUYC-n9unYYGmUO0MCbhP5GUkLAIwPbcNAIxEW7ZfZ5FVQC4VTZUBUUOBIXvM3bLEbQwHlNpJoqZnVLyAHJGcqJlA.jpg?r=b45'],
                        ['id' => 31, 'title' => 'The Trial of the Chicago 7', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABVx3476GKnz9HMW5vO3UYpZH-jkOG9jIxMwcRcfgL3LFk4SHnhYSPUJLbBPwUVaVmJbm5prQJkTcMNsZTlmDrZ1VwkHvJvK0Aw.jpg?r=608'],
                        ['id' => 32, 'title' => 'Roma', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZBhxVQHzZk7iqOl9tKLRNrKI3lMgBVxz6l0M0iJ9ky9bUEg9qYwxfaUsLYL5GKV9-0XJVmCyNYQyXz5sCBLY0EFVgEbGd0FQA.jpg?r=082'],
                        ['id' => 33, 'title' => 'The Two Popes', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABcZHzDzM0RXgXBKqEQcjFCEYnxEdSS5NIBcN0kKQTlEQAY0YZFNn6NZF3bZLdpxqX8UtDFxuVm_3pxKcDpeLEFNDxZCJgJAKrQ.jpg?r=a44'],
                        ['id' => 34, 'title' =>  'Mank', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABZemvLhpYdiWfTcKL-eQ9XYV7fNiPp3dMbi5XxBwUJ-F-iYl9GbCRNkjXVWQfYxMEMUTWHu_WMiva9MfwEKt7ZJzk5zZKzLDzQ.jpg?r=5c0'],
                        ['id' => 35, 'title' => 'The Power of the Dog', 'poster' => 'https://occ-0-2794-2219.1.nflxso.net/dnm/api/v6/E8vDc_W8CLv7-yMQu8KMEC7Rrr8/AAAABf8J_6TRjreQKE_9aBSQoWtpwGFTLWGzCwpWGEBGT-FA-34xTpbdPX_bXQqUEl8eGF7CUlQCYEpwhjKEM_vktgTmDJx70-qgQQ.jpg?r=700'],
                    ];
                    
                    foreach($dummy_movies as $movie) {
                        echo '<div class="movie-card" onclick="goToMovie(' . $movie['id'] . ')">';
                        echo '<img src="' . $movie['poster'] . '" alt="' . $movie['title'] . '" class="movie-poster">';
                        echo '<div class="movie-title">' . $movie['title'] . '</div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    
    <a href="upload.php" class="upload-btn" title="Upload Movie">+</a>

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
        
        document.querySelector('.upload-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'upload.php';
        });
    </script>
</body>
</html>

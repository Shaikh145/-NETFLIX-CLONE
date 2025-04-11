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
    $video_url = $movie['video_url'];
    $title = $movie['title'];
} else {
    // Dummy video URLs based on ID
    $dummy_videos = [
        1 => ['title' => 'Stranger Things', 'video_url' => 'https://www.youtube.com/embed/b9EkMc79ZSU'],
        2 => ['title' => 'The Witcher', 'video_url' => 'https://www.youtube.com/embed/ndl1W4ltcmg'],
        3 => ['title' => 'Money Heist', 'video_url' => 'https://www.youtube.com/embed/_InqQJRqGW4'],
        4 => ['title' => 'Squid Game', 'video_url' => 'https://www.youtube.com/embed/oqxAJKy0ii4'],
        5 => ['title' => 'Wednesday', 'video_url' => 'https://www.youtube.com/embed/Di310WS8zLk'],
        // Add more as needed
    ];
    
    // If the ID exists in our dummy data, use it
    if(isset($dummy_videos[$movie_id])) {
        $video_url = $dummy_videos[$movie_id]['video_url'];
        $title = $dummy_videos[$movie_id]['title'];
    } else {
        // Default to first video if ID not found
        $video_url = $dummy_videos[1]['video_url'];
        $title = $dummy_videos[1]['title'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch <?php echo $title; ?> - Netflix Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #000;
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        
        .logo {
            color: #e50914;
            font-size: 1.8rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
        }
        
        .back-btn {
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        
        .back-btn:hover {
            text-decoration: underline;
        }
        
        .video-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            width: 100%;
            height: calc(100vh - 60px);
        }
        
        .video-player {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        .video-controls {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
            padding: 20px;
            display: flex;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .video-container:hover .video-controls {
            opacity: 1;
        }
        
        .play-pause {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            margin-right: 15px;
        }
        
        .progress-bar {
            flex: 1;
            height: 5px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            position: relative;
            cursor: pointer;
        }
        
        .progress {
            height: 100%;
            background-color: #e50914;
            border-radius: 5px;
            width: 0%;
        }
        
        .time {
            margin-left: 15px;
            font-size: 14px;
        }
        
        .volume-control {
            margin-left: 15px;
            display: flex;
            align-items: center;
        }
        
        .volume-icon {
            color: white;
            font-size: 20px;
            margin-right: 5px;
            cursor: pointer;
        }
        
        .volume-slider {
            width: 80px;
            height: 5px;
            background-color: rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            position: relative;
            cursor: pointer;
        }
        
        .volume-level {
            height: 100%;
            background-color: white;
            border-radius: 5px;
            width: 100%;
        }
        
        .fullscreen-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            margin-left: 15px;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .video-controls {
                padding: 10px;
            }
            
            .volume-control {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="home.php" class="logo">Netflix</a>
        <a href="movie.php?id=<?php echo $movie_id; ?>" class="back-btn">← Back to details</a>
    </div>
    
    <div class="video-container">
        <iframe class="video-player" src="<?php echo $video_url; ?>" allowfullscreen></iframe>
        
        <!-- Custom video controls (these will only work with actual video files, not YouTube embeds) -->
        <div class="video-controls">
            <button class="play-pause">▶</button>
            
            <div class="progress-bar">
                <div class="progress"></div>
            </div>
            
            <div class="time">0:00 / 0:00</div>
            
            <div class="volume-control">
                <div class="volume-icon">🔊</div>
                <div class="volume-slider">
                    <div class="volume-level"></div>
                </div>
            </div>
            
            <button class="fullscreen-btn">⛶</button>
        </div>
    </div>

    <script>
        // JavaScript for redirection
        document.querySelector('.logo').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'home.php';
        });
        
        document.querySelector('.back-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'movie.php?id=<?php echo $movie_id; ?>';
        });
        
        // Note: The custom video controls won't work with YouTube embeds
        // They would work with actual video files hosted on your server
    </script>
</body>
</html>

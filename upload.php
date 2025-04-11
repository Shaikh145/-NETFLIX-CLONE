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

$error = '';
$success = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $category = $conn->real_escape_string($_POST['category']);
    $year = $conn->real_escape_string($_POST['year']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $cast = $conn->real_escape_string($_POST['cast']);
    $director = $conn->real_escape_string($_POST['director']);
    
    // Check if poster file is uploaded
    if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $poster_file = $_FILES['poster'];
        $poster_name = $poster_file['name'];
        $poster_tmp = $poster_file['tmp_name'];
        $poster_size = $poster_file['size'];
        $poster_ext = strtolower(pathinfo($poster_name, PATHINFO_EXTENSION));
        
        // Check file size (100MB limit)
        if($poster_size > 100 * 1024 * 1024) {
            $error = "Poster file size must be less than 100MB";
        } else {
            // Valid file extensions
            $valid_extensions = array('jpg', 'jpeg', 'png', 'gif');
            
            if(in_array($poster_ext, $valid_extensions)) {
                // Upload directory
                $upload_dir = 'uploads/posters/';
                
                // Create directory if it doesn't exist
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $poster_filename = uniqid() . '.' . $poster_ext;
                $poster_path = $upload_dir . $poster_filename;
                
                // Move uploaded file
                if(move_uploaded_file($poster_tmp, $poster_path)) {
                    $poster_url = $poster_path;
                } else {
                    $error = "Failed to upload poster file";
                }
            } else {
                $error = "Invalid poster file format. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }
    } else {
        // Use default poster if none uploaded
        $poster_url = 'uploads/posters/default.jpg';
    }
    
    // Check if video file is uploaded
    if(isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
        $video_file = $_FILES['video'];
        $video_name = $video_file['name'];
        $video_tmp = $video_file['tmp_name'];
        $video_size = $video_file['size'];
        $video_ext = strtolower(pathinfo($video_name, PATHINFO_EXTENSION));
        
        // Check file size (100MB limit)
        if($video_size > 100 * 1024 * 1024) {
            $error = "Video file size must be less than 100MB";
        } else {
            // Valid file extensions
            $valid_extensions = array('mp4', 'webm', 'ogg');
            
            if(in_array($video_ext, $valid_extensions)) {
                // Upload directory
                $upload_dir = 'uploads/videos/';
                
                // Create directory if it doesn't exist
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $video_filename = uniqid() . '.' . $video_ext;
                $video_path = $upload_dir . $video_filename;
                
                // Move uploaded file
                if(move_uploaded_file($video_tmp, $video_path)) {
                    $video_url = $video_path;
                } else {
                    $error = "Failed to upload video file";
                }
            } else {
                $error = "Invalid video file format. Only MP4, WebM, and OGG are allowed.";
            }
        }
    } else {
        // Use YouTube URL if provided
        if(!empty($_POST['youtube_url'])) {
            $youtube_url = $conn->real_escape_string($_POST['youtube_url']);
            
            // Convert YouTube URL to embed format
            if(strpos($youtube_url, 'youtube.com/watch?v=') !== false) {
                $video_id = substr($youtube_url, strpos($youtube_url, 'v=') + 2);
                if(strpos($video_id, '&') !== false) {
                    $video_id = substr($video_id, 0, strpos($video_id, '&'));
                }
                $video_url = 'https://www.youtube.com/embed/' . $video_id;
            } elseif(strpos($youtube_url, 'youtu.be/') !== false) {
                $video_id = substr($youtube_url, strrpos($youtube_url, '/') + 1);
                $video_url = 'https://www.youtube.com/embed/' . $video_id;
            } else {
                $video_url = $youtube_url;
            }
        } else {
            $error = "Please upload a video file or provide a YouTube URL";
        }
    }
    
    // If no errors, insert movie into database
    if(empty($error)) {
        $trending = isset($_POST['trending']) ? 1 : 0;
        
        $sql = "INSERT INTO movies (title, description, category, year, duration, rating, cast, director, poster, video_url, trending, user_id) 
                VALUES ('$title', '$description', '$category', '$year', '$duration', '$rating', '$cast', '$director', '$poster_url', '$video_url', $trending, " . $_SESSION['user_id'] . ")";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Movie uploaded successfully!";
            // Redirect to home page after 2 seconds
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'home.php';
                }, 2000);
            </script>";
        } else {
            $error = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Movie - Netflix Clone</title>
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
            padding-top: 100px;
            padding-bottom: 50px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .page-title {
            font-size: 2rem;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .upload-form {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 30px;
            border-radius: 5px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border-radius: 4px;
            border: 1px solid #333;
            background-color: #333;
            color: #fff;
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #e50914;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-check {
            display: flex;
            align-items: center;
        }
        
        .form-check-input {
            margin-right: 10px;
        }
        
        .btn-submit {
            background-color: #e50914;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: #f40612;
        }
        
        .error-message {
            color: #e50914;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(229, 9, 20, 0.1);
            border-radius: 4px;
        }
        
        .success-message {
            color: #2ecc71;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(46, 204, 113, 0.1);
            border-radius: 4px;
        }
        
        .form-note {
            font-size: 12px;
            color: #a3a3a3;
            margin-top: 5px;
        }
        
        .form-divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .form-divider::before,
        .form-divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #333;
        }
        
        .form-divider-text {
            padding: 0 10px;
            color: #a3a3a3;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .main-content {
                padding: 80px 20px 40px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
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
    
    <div class="main-content">
        <h1 class="page-title">Upload Movie</h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form class="upload-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label" for="title">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="category">Category</label>
                <select class="form-control" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Action">Action</option>
                    <option value="Comedy">Comedy</option>
                    <option value="Drama">Drama</option>
                    <option value="Sci-Fi & Fantasy">Sci-Fi & Fantasy</option>
                    <option value="Horror">Horror</option>
                    <option value="Romance">Romance</option>
                    <option value="Documentary">Documentary</option>
                    <option value="Thriller">Thriller</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="year">Release Year</label>
                <input type="number" class="form-control" id="year" name="year" min="1900" max="2099" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="duration">Duration (e.g., "2h 30m" or "45m")</label>
                <input type="text" class="form-control" id="duration" name="duration" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="rating">Rating</label>
                <select class="form-control" id="rating" name="rating" required>
                    <option value="">Select Rating</option>
                    <option value="G">G</option>
                    <option value="PG">PG</option>
                    <option value="PG-13">PG-13</option>
                    <option value="R">R</option>
                    <option value="TV-Y">TV-Y</option>
                    <option value="TV-Y7">TV-Y7</option>
                    <option value="TV-G">TV-G</option>
                    <option value="TV-PG">TV-PG</option>
                    <option value="TV-14">TV-14</option>
                    <option value="TV-MA">TV-MA</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="cast">Cast (comma separated)</label>
                <input type="text" class="form-control" id="cast" name="cast" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="director">Director</label>
                <input type="text" class="form-control" id="director" name="director" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="poster">Poster Image</label>
                <input type="file" class="form-control" id="poster" name="poster" accept="image/*">
                <p class="form-note">Recommended size: 300x450 pixels. Max file size: 100MB.</p>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="video">Video File</label>
                <input type="file" class="form-control" id="video" name="video" accept="video/*">
                <p class="form-note">Supported formats: MP4, WebM, OGG. Max file size: 100MB.</p>
            </div>
            
            <div class="form-divider">
                <span class="form-divider-text">OR</span>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="youtube_url">YouTube URL</label>
                <input type="url" class="form-control" id="youtube_url" name="youtube_url" placeholder="https://www.youtube.com/watch?v=...">
                <p class="form-note">If you don't have a video file, you can provide a YouTube URL instead.</p>
            </div>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="trending" name="trending">
                    <label class="form-check-label" for="trending">Add to Trending</label>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Upload Movie</button>
        </form>
    </div>

    <script>
        // JavaScript for redirection
        document.getElementById('logout-link').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'logout.php';
        });
        
        document.querySelector('.logo').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'home.php';
        });
        
        // Validate file size before form submission
        document.querySelector('.upload-form').addEventListener('submit', function(e) {
            const posterFile = document.getElementById('poster').files[0];
            const videoFile = document.getElementById('video').files[0];
            const youtubeUrl = document.getElementById('youtube_url').value;
            
            // Check poster file size
            if (posterFile && posterFile.size > 100 * 1024 * 1024) {
                e.preventDefault();
                alert('Poster file size must be less than 100MB');
                return;
            }
            
            // Check video file size
            if (videoFile && videoFile.size > 100 * 1024 * 1024) {
                e.preventDefault();
                alert('Video file size must be less than 100MB');
                return;
            }
            
            // Check if either video file or YouTube URL is provided
            if (!videoFile && !youtubeUrl) {
                e.preventDefault();
                alert('Please upload a video file or provide a YouTube URL');
                return;
            }
        });
    </script>
</body>
</html>

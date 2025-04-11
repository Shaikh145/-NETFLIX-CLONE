<?php
session_start();
// Database connection
$conn = new mysqli('localhost', 'uklz9ew3hrop3', 'zyrbspyjlzjb', 'db9ranxpmtccqq');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    // Get user from database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            // Redirect to home page
            echo "<script>window.location.href = 'home.php';</script>";
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Netflix Clone</title>
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
            background-image: url('https://assets.nflxext.com/ffe/siteui/vlv3/a1dc92ca-091d-4ca9-a05b-8cd44bbfce6a/f9368347-e982-4856-a5a4-396796381f28/RS-en-20191230-popsignuptwoweeks-perspective_alpha_website_large.jpg');
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        
        .header {
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            color: #e50914;
            font-size: 2.5rem;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
        }
        
        .login-container {
            max-width: 450px;
            margin: 40px auto;
            padding: 60px 68px 40px;
            background-color: rgba(0, 0, 0, 0.75);
            border-radius: 4px;
        }
        
        .login-container h1 {
            margin-bottom: 28px;
            font-size: 32px;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-control {
            width: 100%;
            height: 50px;
            padding: 16px 20px;
            border-radius: 4px;
            border: none;
            background-color: #333;
            color: white;
            font-size: 16px;
        }
        
        .form-control:focus {
            outline: none;
            background-color: #454545;
        }
        
        .btn-login {
            width: 100%;
            height: 50px;
            background-color: #e50914;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 700;
            margin: 24px 0 12px;
            cursor: pointer;
        }
        
        .btn-login:hover {
            background-color: #f40612;
        }
        
        .signup-link {
            color: #737373;
            font-size: 16px;
            margin-top: 16px;
        }
        
        .signup-link a {
            color: white;
            text-decoration: none;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #e50914;
            margin-bottom: 16px;
        }
        
        @media (max-width: 740px) {
            .login-container {
                padding: 60px 40px 40px;
                max-width: 100%;
                margin: 0 20px;
            }
            
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="logo">Netflix</a>
    </div>
    
    <div class="login-container">
        <h1>Sign In</h1>
        
        <?php if($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email address" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn-login">Sign In</button>
            
            <div class="signup-link">
                New to Netflix? <a href="signup.php" id="signup-link">Sign up now</a>.
            </div>
        </form>
    </div>

    <script>
        // JavaScript for redirection
        document.getElementById('signup-link').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'signup.php';
        });
        
        document.querySelector('.logo').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'index.php';
        });
    </script>
</body>
</html>

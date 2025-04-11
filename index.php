<?php
session_start();
// Redirect to home if already logged in
if(isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'home.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Netflix Clone</title>
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
            height: 100vh;
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
        }
        
        .login-btn {
            background-color: #e50914;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        
        .hero {
            text-align: center;
            margin-top: 150px;
            padding: 0 20px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .hero h2 {
            font-size: 1.8rem;
            font-weight: normal;
            margin-bottom: 30px;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }
        
        .cta-btn {
            background-color: #e50914;
            color: white;
            padding: 15px 25px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.2rem;
            display: inline-block;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">Netflix</div>
        <a href="login.php" class="login-btn">Sign In</a>
    </div>
    
    <div class="hero">
        <h1>Unlimited movies, TV shows, and more.</h1>
        <h2>Watch anywhere. Cancel anytime.</h2>
        <p>Ready to watch? Create an account to start your membership.</p>
        <a href="signup.php" class="cta-btn">Get Started</a>
    </div>

    <script>
        // JavaScript for redirection
        document.querySelector('.login-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'login.php';
        });
        
        document.querySelector('.cta-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'signup.php';
        });
    </script>
</body>
</html>

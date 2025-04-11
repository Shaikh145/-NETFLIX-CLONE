<?php
/**
 * Sign Out / Logout File
 * 
 * This file handles the user logout process by destroying the session
 * and redirecting to the login page.
 */

// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// If you're using session cookies, clear the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
echo "<script>
    alert('You have been successfully logged out.');
    window.location.href = 'login.php';
</script>";
exit;
?>

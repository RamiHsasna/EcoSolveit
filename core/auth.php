<?php

/**
 * Authentication Helper Functions
 * Provides session management and email-based admin access control
 */

/**
 * Start session if not already started
 */
function ensureSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    ensureSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin based on email domain and/or database user_type
 * Admin users must have "admin" in their email domain OR user_type = 'admin'
 */
function isAdmin(): bool
{
    ensureSession();
    if (!isLoggedIn()) {
        return false;
    }

    // Method 1: Check database user_type (if available in session)
    $userType = $_SESSION['user_type'] ?? '';
    if ($userType === 'admin') {
        return true;
    }

    // Method 2: Check email domain for "admin"
    $email = $_SESSION['email'] ?? '';
    if (!empty($email)) {
        $emailParts = explode('@', $email);
        if (count($emailParts) === 2) {
            $domain = strtolower($emailParts[1]);
            return strpos($domain, 'admin') !== false;
        }
    }

    return false;
}

/**
 * Get current user information
 */
function getCurrentUser(): ?array
{
    ensureSession();
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'is_admin' => isAdmin()
    ];
}

/**
 * Require admin access - redirect to login if not admin
 */
function requireAdmin(): void
{
    if (!isAdmin()) {
        ensureSession();
        if (!isLoggedIn()) {
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';
        } else {
            $_SESSION['error'] = 'Accès refusé. Cette section est réservée aux administrateurs uniquement.';
        }

        // Redirect to login page
        header('Location: /EcoSolveit/views/FrontOffice/login.php');
        exit;
    }
}

/**
 * Require login - redirect to login if not authenticated
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        ensureSession();
        $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page.';

        // Redirect to login page
        header('Location: /EcoSolveit/views/FrontOffice/login.php');
        exit;
    }
}

/**
 * Logout user
 */
function logout(): void
{
    ensureSession();

    // Destroy all session data
    $_SESSION = [];

    // Delete the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();

    // Redirect to homepage
    header('Location: /EcoSolveit/index.html');
    exit;
}

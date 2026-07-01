<?php
function startCVSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(CV_SESSION_NAME);
        session_start();
    }
}

function isLoggedIn(): bool {
    startCVSession();
    return !empty($_SESSION['cv_logged_in']) && $_SESSION['cv_logged_in'] === true;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . cvUrl('index.php'));
        exit;
    }
}

function login(string $username, string $password): bool {
    if ($username !== CV_USERNAME) return false;
    if (empty(CV_PASSWORD_HASH)) return false;
    if (!password_verify($password, CV_PASSWORD_HASH)) return false;
    startCVSession();
    session_regenerate_id(true);
    $_SESSION['cv_logged_in'] = true;
    return true;
}

function logout(): void {
    startCVSession();
    $_SESSION = [];
    session_destroy();
}

// Retourne l'URL d'un fichier dans /cv/
function cvUrl(string $file = ''): string {
    return CV_BASE_URL . '/' . ltrim($file, '/');
}

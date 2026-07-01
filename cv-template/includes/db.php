<?php
// Connexion PDO à la base de données
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . CV_DB_HOST . ';dbname=' . CV_DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, CV_DB_USER, CV_DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Erreur de connexion base de données.']));
        }
    }
    return $pdo;
}

// Helper : détecte la plateforme depuis une URL
function detectPlatform(string $url): string {
    if (empty($url)) return '';
    $map = [
        'linkedin.com'           => 'LinkedIn',
        'apec.fr'                => 'APEC',
        'hellowork.com'          => 'HelloWork',
        'cadremploi.fr'          => 'Cadremploi',
        'michaelpage.fr'         => 'Michael Page',
        'robertwalters.fr'       => 'Robert Walters',
        'roberthalffrance.fr'    => 'Robert Half',
        'indeed.com'             => 'Indeed',
        'welcometothejungle.com' => 'WTTJ',
        'monster.fr'             => 'Monster',
        'jobteaser.com'          => 'JobTeaser',
        'regionsjob.com'         => 'RégionsJob',
    ];
    foreach ($map as $domain => $label) {
        if (str_contains($url, $domain)) return $label;
    }
    $host = parse_url($url, PHP_URL_HOST);
    return $host ? preg_replace('/^www\./', '', $host) : 'Lien';
}

// Helper : retourne un JSON et stoppe l'exécution
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

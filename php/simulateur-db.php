<?php
/**
 * Gestion des tokens d'accès au Simulateur LLM
 */

require_once __DIR__ . '/config.php';

function getSimulateurDb() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        return null;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

/**
 * Valide un token et crée la session si valide.
 * Retourne true si accès accordé.
 */
function validateToken(string $token): bool {
    if (empty($token) || strlen($token) !== 64) return false;

    $db = getSimulateurDb();
    if (!$db) return false;

    $stmt = $db->prepare(
        "SELECT id FROM simulateur_tokens
         WHERE token = ? AND active = 1
         LIMIT 1"
    );
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $db->close();
        return false;
    }

    $row = $result->fetch_assoc();

    // Mise à jour compteur d'usage
    $now = date('Y-m-d H:i:s');
    $upd = $db->prepare(
        "UPDATE simulateur_tokens
         SET use_count = use_count + 1,
             last_used_at = ?,
             first_used_at = COALESCE(first_used_at, ?)
         WHERE id = ?"
    );
    $upd->bind_param('ssi', $now, $now, $row['id']);
    $upd->execute();

    $db->close();
    return true;
}

/**
 * Génère un token unique pour un prospect et l'enregistre en base.
 * Retourne le token ou null en cas d'erreur.
 */
function createToken(string $email, string $name): ?string {
    $db = getSimulateurDb();
    if (!$db) return null;

    // Désactiver les anciens tokens pour cet email
    $stmt = $db->prepare(
        "UPDATE simulateur_tokens SET active = 0 WHERE email = ?"
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();

    // Générer un token unique
    $token = bin2hex(random_bytes(32)); // 64 chars hex

    $stmt = $db->prepare(
        "INSERT INTO simulateur_tokens (token, email, name, created_at, active)
         VALUES (?, ?, ?, NOW(), 1)"
    );
    $stmt->bind_param('sss', $token, $email, $name);

    if (!$stmt->execute()) {
        $db->close();
        return null;
    }

    $db->close();
    return $token;
}

/**
 * Génère le lien d'accès admin (signé HMAC) à inclure dans l'email de notification.
 */
function buildGrantLink(string $name, string $email): string {
    $data = base64_encode($name) . '|' . base64_encode($email);
    $sig  = hash_hmac('sha256', $data, ADMIN_SECRET);
    return SITE_URL . '/php/grant-access.php'
        . '?name=' . urlencode(base64_encode($name))
        . '&email=' . urlencode(base64_encode($email))
        . '&sig=' . $sig;
}

/**
 * Vérifie la signature HMAC du lien admin.
 */
function verifyGrantSig(string $name_b64, string $email_b64, string $sig): bool {
    $data     = $name_b64 . '|' . $email_b64;
    $expected = hash_hmac('sha256', $data, ADMIN_SECRET);
    return hash_equals($expected, $sig);
}

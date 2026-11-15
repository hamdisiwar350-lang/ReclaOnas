<?php
function postString(string $key): ?string {
    $value = $_POST[$key] ?? null;
    return is_string($value) ? $value : null;
}

function postInt(string $key): ?int {
    $value = postString($key);
    if ($value === null || filter_var($value, FILTER_VALIDATE_INT) === false) {
        return null;
    }
    return (int) $value;
}

function queryInt(string $key): ?int {
    $value = $_GET[$key] ?? null;
    if (!is_string($value) || filter_var($value, FILTER_VALIDATE_INT) === false) {
        return null;
    }
    return (int) $value;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Requête refusée.');
    }
}
?>
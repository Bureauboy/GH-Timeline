<?php
require_once 'functions.php';

// TODO: Implement the form and logic for email unsubscription.

// Simple file-based storage for unsubscribe codes (for demo purposes)
function store_unsubscribe_code($email, $code) {
    $file = __DIR__ . '/unsubscribe_codes.txt';
    $lines = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $lines = array_filter($lines, function($line) use ($email) {
        return strpos($line, strtolower($email) . '|') !== 0;
    });
    $lines[] = strtolower($email) . '|' . $code;
    file_put_contents($file, implode("\n", $lines) . "\n");
}

function get_unsubscribe_code($email) {
    $file = __DIR__ . '/unsubscribe_codes.txt';
    if (!file_exists($file)) return null;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($e, $c) = explode('|', $line);
        if ($e === strtolower($email)) return $c;
    }
    return null;
}

function remove_unsubscribe_code($email) {
    $file = __DIR__ . '/unsubscribe_codes.txt';
    if (!file_exists($file)) return;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_filter($lines, function($line) use ($email) {
        return strpos($line, strtolower($email) . '|') !== 0;
    });
    file_put_contents($file, implode("\n", $lines) . "\n");
}

// Handle form submissions
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = '<span style="color:red">Invalid email address.</span>';
        } else {
            $code = generateVerificationCode();
            store_unsubscribe_code($email, $code);
            if (sendVerificationEmail($email, $code)) {
                $message = 'Unsubscribe verification code sent to your email.';
            } else {
                $message = '<span style="color:red">Failed to send verification email.</span>';
            }
        }
    } elseif (isset($_POST['unsubscribe_verification_code'], $_POST['unsubscribe_verify_email'])) {
        $email = trim($_POST['unsubscribe_verify_email']);
        $input_code = trim($_POST['unsubscribe_verification_code']);
        $real_code = get_unsubscribe_code($email);
        if ($real_code && $input_code === $real_code) {
            if (unsubscribeEmail($email)) {
                $message = '<span style="color:green">You have been unsubscribed.</span>';
            } else {
                $message = '<span style="color:orange">Email not found or already unsubscribed.</span>';
            }
            remove_unsubscribe_code($email);
        } else {
            $message = '<span style="color:red">Invalid verification code.</span>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Unsubscribe from GitHub Timeline Updates</title>
</head>
<body>
    <h1>Unsubscribe from GitHub Timeline Updates</h1>
    <?php if ($message) echo '<p>' . $message . '</p>'; ?>
    <form method="post">
        <label for="unsubscribe_email">Email:</label>
        <input type="email" name="unsubscribe_email" required>
        <button id="submit-unsubscribe" type="submit">Unsubscribe</button>
    </form>
    <br>
    <form method="post">
        <label for="unsubscribe_verify_email">Email:</label>
        <input type="email" name="unsubscribe_verify_email" required>
        <label for="unsubscribe_verification_code">Verification Code:</label>
        <input type="text" name="unsubscribe_verification_code" required>
        <button id="verify-unsubscribe" type="submit">Verify</button>
    </form>
</body>
</html>

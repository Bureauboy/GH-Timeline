<?php
require_once 'functions.php';

// TODO: Implement the form and logic for email registration and verification

// Simple file-based storage for verification codes (for demo purposes)
function store_verification_code($email, $code) {
    $file = __DIR__ . '/verification_codes.txt';
    $lines = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $lines = array_filter($lines, function($line) use ($email) {
        return strpos($line, strtolower($email) . '|') !== 0;
    });
    $lines[] = strtolower($email) . '|' . $code;
    file_put_contents($file, implode("\n", $lines) . "\n");
}

function get_verification_code($email) {
    $file = __DIR__ . '/verification_codes.txt';
    if (!file_exists($file)) return null;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($e, $c) = explode('|', $line);
        if ($e === strtolower($email)) return $c;
    }
    return null;
}

function remove_verification_code($email) {
    $file = __DIR__ . '/verification_codes.txt';
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
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = '<span style="color:red">Invalid email address.</span>';
        } else {
            $code = generateVerificationCode();
            store_verification_code($email, $code);
            if (sendVerificationEmail($email, $code)) {
                $message = 'Verification code sent to your email.';
            } else {
                $message = '<span style="color:red">Failed to send verification email.</span>';
            }
        }
    } elseif (isset($_POST['verification_code'], $_POST['verify_email'])) {
        $email = trim($_POST['verify_email']);
        $input_code = trim($_POST['verification_code']);
        $real_code = get_verification_code($email);
        if ($real_code && $input_code === $real_code) {
            if (registerEmail($email)) {
                $message = '<span style="color:green">Email verified and registered!</span>';
            } else {
                $message = '<span style="color:orange">Email already registered.</span>';
            }
            remove_verification_code($email);
        } else {
            $message = '<span style="color:red">Invalid verification code.</span>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register for GitHub Timeline Updates</title>
</head>
<body>
    <h1>Register for GitHub Timeline Updates</h1>
    <?php if ($message) echo '<p>' . $message . '</p>'; ?>
    <form method="post">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <button id="submit-email" type="submit">Submit</button>
    </form>
    <br>
    <form method="post">
        <label for="verify_email">Email:</label>
        <input type="email" name="verify_email" required>
        <label for="verification_code">Verification Code:</label>
        <input type="text" name="verification_code" maxlength="6" required>
        <button id="submit-verification" type="submit">Verify</button>
    </form>
</body>
</html>

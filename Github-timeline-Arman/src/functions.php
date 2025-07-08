<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    // Generate and return a 6-digit numeric code
    return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    // Send an email containing the verification code in HTML format
    $subject = 'Your Verification Code';
    $message = "<html><body>"
        . "<h2>Email Verification</h2>"
        . "<p>Your verification code is: <strong>" . htmlspecialchars($code) . "</strong></p>"
        . "</body></html>";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@example.com" . "\r\n";
    return mail($email, $subject, $message, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $email = trim(strtolower($email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    // Read existing emails
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    $emails = array_map('strtolower', $emails);
    if (in_array($email, $emails)) {
        return false; // Already registered
    }
    $emails[] = $email;
    // Write back to file
    return file_put_contents($file, implode("\n", $emails) . "\n") !== false;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $email = trim(strtolower($email));
    if (!file_exists($file)) {
        return false;
    }
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_map('strtolower', $emails);
    $new_emails = array_filter($emails, function($e) use ($email) {
        return $e !== $email;
    });
    if (count($emails) === count($new_emails)) {
        return false; // Email not found
    }
    return file_put_contents($file, implode("\n", $new_emails) . "\n") !== false;
}

/**
 * Fetch GitHub timeline.
 */
function fetchGitHubTimeline() {
    // NOTE: The actual GitHub timeline API (https://www.github.com/timeline) is deprecated.
    // For demonstration, we'll fetch the public events API instead.
    $url = 'https://api.github.com/events';
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: PHP-GitHub-Timeline',
                'Accept: application/vnd.github.v3+json'
            ]
        ]
    ];
    $context = stream_context_create($opts);
    $json = @file_get_contents($url, false, $context);
    if ($json === false) {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

/**
 * Format GitHub timeline data. Returns a valid HTML sting.
 */
function formatGitHubData(array $data): string {
    // Convert fetched data into formatted HTML
    $html = "<h2>Latest GitHub Public Events</h2><ul>";
    $count = 0;
    foreach ($data as $event) {
        if (++$count > 10) break; // Show only 10 events
        $type = htmlspecialchars($event['type'] ?? 'Event');
        $repo = htmlspecialchars($event['repo']['name'] ?? 'Unknown Repo');
        $user = htmlspecialchars($event['actor']['login'] ?? 'Unknown User');
        $html .= "<li><strong>{$type}</strong> in <em>{$repo}</em> by {$user}</li>";
    }
    $html .= "</ul>";
    return $html;
}

/**
 * Send the formatted GitHub updates to registered emails.
 */
function sendGitHubUpdatesToSubscribers(): void {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) return;
    $data = fetchGitHubTimeline();
    $html = formatGitHubData($data);
    $subject = 'GitHub Timeline Updates';
    foreach ($emails as $email) {
        $unsubscribe_link = "http://localhost:8000/src/unsubscribe.php?email=" . urlencode($email);
        $message = $html . "<br><br><a href='$unsubscribe_link'>Unsubscribe</a>";
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: noreply@example.com\r\n";
        mail($email, $subject, $message, $headers);
    }
}

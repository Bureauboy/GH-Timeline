# GH-Timeline

**GH-Timeline** is a PHP-based email verification and notification system that lets users subscribe to GitHub timeline updates by email. This project showcases user authentication via email, periodic GitHub event fetching, and email broadcasting — all written in pure PHP, with no external dependencies or databases.

---

## ✨ Features

- **Email Verification:**  
  - Users sign up with their email and receive a 6-digit verification code.
  - Verification codes are sent via email and must be entered to complete registration.
  - Registered emails are stored in a flat file (`registered_emails.txt`).

- **GitHub Timeline Updates:**  
  - Every 5 minutes, a CRON job fetches the latest public events from GitHub and sends a formatted HTML digest to all registered users.
  - Emails contain a table of the latest events and a personalized unsubscribe link.

- **Unsubscribe Mechanism:**  
  - Each email includes a one-click unsubscribe link.
  - Users must confirm unsubscription with a verification code sent to their email.
  - Unsubscribed emails are removed from future newsletters.

- **Pure PHP, No DB or Libraries:**  
  - No external PHP libraries or frameworks.
  - No database — uses plain text files for storage.
  
- **HTML Email Templates:**  
  - All emails are sent as HTML, matching strict format guidelines.

---

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/Bureauboy/GH-Timeline.git
cd GH-Timeline/Github-timeline-Arman  # Main code is inside this subdirectory
```

### 2. Setup Environment

- **PHP version:** 8.3+ recommended.
- **Mail Testing:** Use [Mailpit](https://mailpit.axllent.org/) for local email testing.
- No need for a public host; local testing is sufficient.

### 3. Install CRON Job

To automatically send GitHub timeline updates every 5 minutes, run:

```bash
cd src
bash setup_cron.sh
```

This will configure a CRON job that runs `cron.php` at the required interval.

---

## 📝 Usage

### Register

1. Open `src/index.php` in your browser (using PHP's built-in server or similar).
2. Enter your email to receive a 6-digit verification code.
3. Enter the code to complete registration.

### Receive Updates

- Registered users receive a GitHub events digest every 5 minutes.

### Unsubscribe

- Click the "Unsubscribe" link in any update email.
- Enter your email and the received confirmation code to complete unsubscription.

---

## 📦 Project Structure

```
Github-timeline-Arman/
├── src/
│   ├── index.php          # Registration & verification UI
│   ├── unsubscribe.php    # Handles unsubscription flow
│   ├── cron.php           # Sends GitHub timeline updates
│   ├── functions.php      # All core functionality
│   ├── registered_emails.txt # Stores subscribed emails
│   └── setup_cron.sh      # CRON setup script
├── README.md
└── ...
```

---

## 🛠 Implementation Notes

- **All business logic** is in `src/functions.php` (see function stubs there).
- **No changes outside `src/`** are permitted for feature implementation.
- **Do not hardcode emails or codes**; always use the provided files.
- **All forms** (email, verification code, unsubscribe) must always be visible on their respective pages.

---

## 📋 Email Format Examples

**Verification Email**
- Subject: `Your Verification Code`
- Body:
  ```html
  <p>Your verification code is: <strong>123456</strong></p>
  ```

**GitHub Updates Email**
- Subject: `Latest GitHub Updates`
- Body:
  ```html
  <h2>GitHub Timeline Updates</h2>
  <table border="1">
    <tr><th>Event</th><th>User</th></tr>
    <tr><td>Push</td><td>testuser</td></tr>
    <!-- ... -->
  </table>
  <p><a href="unsubscribe_url" id="unsubscribe-button">Unsubscribe</a></p>
  ```

**Unsubscribe Confirmation**
- Subject: `Confirm Unsubscription`
- Body:
  ```html
  <p>To confirm unsubscription, use this code: <strong>654321</strong></p>
  ```

---

## ⚠️ Disqualification Criteria

- Hardcoding verification codes.
- Using a database (must use `registered_emails.txt`).
- Modifying anything outside the `src/` directory.
- Changing function names or file structure.
- Not implementing a working CRON job.
- Not sending HTML emails.
- Using any third-party PHP libraries.

---

## 🧑‍💻 Contribution & Submission

- **Clone** (do not fork).
- **Create a branch** from `main`.
- **Implement** inside `src/` only.
- **Push** to your branch and **open ONE pull request** against `main` when done.

---

## 📄 License

*No license specified.* Please contact the repository owner for usage terms.

---

## 🙋‍♂️ Author

- [Bureauboy](https://github.com/Bureauboy)

---

**GH-Timeline** — Stay up to date with GitHub, right from your inbox!

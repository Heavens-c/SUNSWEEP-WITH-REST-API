<?php
/**
 * reset_admin.php
 * One-time script to recreate the default admin account.
 * Run this in your browser once, then delete it for security.
 */

require_once __DIR__ . '/includes/database.php';

try {
    // Create bcrypt hash for admin123
    $hash = password_hash('admin123', PASSWORD_DEFAULT);

    // Delete any existing admin user
    $pdo->exec("DELETE FROM users WHERE username='admin'");

    // Insert fresh admin record
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $hash, 'admin']);

    echo "<h2>✅ Admin account has been reset.</h2>";
    echo "<p>Username: <strong>admin</strong><br>Password: <strong>admin123</strong></p>";
    echo "<p><small>Bcrypt hash: {$hash}</small></p>";
    echo "<p>Now you can <a href='/sunsweep/auth/login.php'>login here</a>.</p>";

} catch (Exception $e) {
    echo "<h2>❌ Error:</h2><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}

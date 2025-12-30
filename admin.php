<?php
session_start();
require_once 'auth.php';

if (!is_logged_in()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
        if (login($_POST['password'])) {
            header("Location: admin.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body class="center-vh">
        <form method="POST" class="auth-card">
            <h2>Admin Access</h2>
            <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
            <input type="password" name="password" placeholder="Password" required autofocus>
            <button type="submit">Login</button>
        </form>
    </body>
    </html>
    <?php
    exit;
}

$config = include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_key'])) {
    $new_config = [
        'api_key' => $_POST['api_key'],
        'model' => $_POST['model'],
        'admin_password' => $_POST['admin_password'] ?: $config['admin_password']
    ];
    $content = "<?php\nreturn " . var_export($new_config, true) . ";";
    file_put_contents('config.php', $content);
    $config = $new_config;
    $success = "Settings saved.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Settings</h1>
            <a href="index.php">Back to App</a>
        </header>
        <main>
            <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
            <form method="POST">
                <div class="form-group">
                    <label>OpenRouter API Key</label>
                    <input type="password" name="api_key" value="<?php echo htmlspecialchars($config['api_key']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Default AI Model</label>
                    <input type="text" name="model" value="<?php echo htmlspecialchars($config['model']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Change Admin Password (leave blank to keep)</label>
                    <input type="password" name="admin_password" placeholder="New Password">
                </div>
                <button type="submit">Save Settings</button>
                <a href="auth.php?logout=1" class="btn-logout">Logout</a>
            </form>
        </main>
    </div>
</body>
</html>

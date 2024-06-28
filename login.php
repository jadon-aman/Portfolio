<?php
require_once __DIR__ . '/../db/db_connect.php';

// Handle form submission
$error = $success = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password === $confirm_password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
            if ($stmt === false) {
                error_log("Prepare failed: " . $conn->error);
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ss", $username, $password_hash);

            if ($stmt->execute()) {
                $success = "New admin created successfully.";
            } else {
                $error = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error = "Passwords do not match.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Admin</title>
</head>
<body>
    <h2>Add New Admin</h2>
    <?php if (!empty($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <?php if (!empty($success)) { echo "<p style='color: green;'>$success</p>"; } ?>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Add Admin">
    </form>
</body>
</html>

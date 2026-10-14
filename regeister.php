```php
<?php
require "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // College email domain
    $college_domain = "@acro.in";

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid name and email.";
    }

    // Only college email allowed
    elseif (!str_ends_with(strtolower($email), $college_domain)) {
        $error = "Only Acropolis college email is allowed (example: student@acro.in).";
    }

    // Strong password validation
    elseif (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^a-zA-Z0-9]/', $password)
    ) {
        $error = "Password must have 8 characters, uppercase, lowercase, number and special character.";
    }

    else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            header("Location: login.php?registered=1");
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() === "23000") {
                $error = "This email is already registered.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Student Registration</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="container">

    <div class="card auth-card">

        <h1>Create Account</h1>

        <p class="muted">
            Register using your college email address.
        </p>

        <?php if ($error): ?>

            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label>Full Name</label>

            <input
                type="text"
                name="name"
                required
                maxlength="100"
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
            >

            <label>College Email Address</label>

            <input
                type="email"
                name="email"
                required
                maxlength="150"
                placeholder="example@acro.in"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            >

            <small>
                Only @acro.in college email is allowed.
            </small>

            <label>Password</label>

            <input
                type="password"
                name="password"
                required
                minlength="8"
                autocomplete="new-password"
                placeholder="Enter strong password"
            >

            <button type="submit">
                Register
            </button>

        </form>

        <p>
            Already registered?
            <a href="login.php">Login here</a>
        </p>

    </div>

</div>

</body>
</html>

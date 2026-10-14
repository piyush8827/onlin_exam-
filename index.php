
<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) FROM questions");
$totalQuestions = (int) $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <strong>Online Exam System</strong>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="history.php">My Results</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="card">
        <h1>Welcome, <?= htmlspecialchars($_SESSION["user_name"]) ?>!</h1>
        <p>You are logged in successfully.</p>
        <p>Total questions available: <strong><?= $totalQuestions ?></strong></p>

        <?php if ($totalQuestions > 0): ?>
            <p>Read each question carefully and select one answer.</p>
            <a class="btn" href="exam.php">Start Examination</a>
        <?php else: ?>
            <p class="error">No questions available yet.</p>
        <?php endif; ?>

        <p><a href="history.php">View your previous results</a></p>
    </div>
</div>
</body>
</html>

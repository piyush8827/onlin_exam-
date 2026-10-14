
<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare(
    "SELECT id, score, total_questions, submitted_at
     FROM results
     WHERE user_id = ?
     ORDER BY submitted_at DESC"
);
$stmt->execute([$_SESSION["user_id"]]);
$results = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Results</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <strong>My Exam History</strong>
    <div>
        <a href="index.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="card">
        <h1>Previous Attempts</h1>

        <?php if (!$results): ?>
            <p>No exam attempts yet.</p>
        <?php else: ?>
            <?php foreach ($results as $r): ?>
                <div class="card">
                    <h3>
                        Score: <?= (int) $r["score"] ?> /
                        <?= (int) $r["total_questions"] ?>
                    </h3>

                    <p>
                        Date:
                        <?= htmlspecialchars($r["submitted_at"]) ?>
                    </p>

                    <a class="btn"
                       href="result.php?id=<?= (int) $r["id"] ?>">
                        View Result
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <a class="btn" href="exam.php">Take Another Exam</a>
    </div>
</div>
</body>
</html>

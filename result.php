
<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid result ID.");
}

$stmt = $pdo->prepare(
    "SELECT * FROM results WHERE id = ? AND user_id = ?"
);
$stmt->execute([$id, $_SESSION["user_id"]]);
$result = $stmt->fetch();

if (!$result) {
    http_response_code(404);
    exit("Result not found.");
}

$stmt = $pdo->prepare(
    "SELECT q.question, q.option_a, q.option_b,
            q.option_c, q.option_d, q.correct_option,
            ra.selected_option, ra.is_correct
     FROM result_answers ra
     JOIN questions q ON q.id = ra.question_id
     WHERE ra.result_id = ?
     ORDER BY q.id"
);
$stmt->execute([$id]);
$answers = $stmt->fetchAll();

$percentage = $result["total_questions"] > 0
    ? round($result["score"] * 100 / $result["total_questions"], 2)
    : 0;

function optionText($q, $key) {
    $map = [
        "A" => $q["option_a"],
        "B" => $q["option_b"],
        "C" => $q["option_c"],
        "D" => $q["option_d"]
    ];

    return $map[$key] ?? "Not answered";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Result</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <strong>Online Exam System</strong>
    <a href="index.php">Dashboard</a>
</nav>

<div class="container">
    <div class="card">
        <h1>Examination Result</h1>
        <p>Student: <?= htmlspecialchars($_SESSION["user_name"]) ?></p>

        <p class="score">
            <?= (int) $result["score"] ?> /
            <?= (int) $result["total_questions"] ?>
        </p>

        <p>Percentage: <strong><?= $percentage ?>%</strong></p>
        <p class="muted">
            Submitted on:
            <?= htmlspecialchars($result["submitted_at"]) ?>
        </p>

        <h2>Answer Review</h2>

        <?php foreach ($answers as $i => $a): ?>
            <div class="card">
                <h3>
                    Q<?= $i + 1 ?>.
                    <?= htmlspecialchars($a["question"]) ?>
                </h3>

                <p>
                    Your answer:
                    <?= htmlspecialchars(
                        optionText($a, $a["selected_option"])
                    ) ?>
                </p>

                <p>
                    Correct answer:
                    <?= htmlspecialchars(
                        optionText($a, $a["correct_option"])
                    ) ?>
                </p>

                <?php if ($a["is_correct"]): ?>
                    <p class="success">Correct answer!</p>
                <?php else: ?>
                    <p class="error">Incorrect answer.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <a class="btn" href="history.php">View Result History</a>
        <a class="btn" href="index.php">Dashboard</a>
    </div>
</div>
</body>
</html>

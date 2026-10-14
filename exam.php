
<?php
require "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM questions ORDER BY id");
$questions = $stmt->fetchAll();

if (!$questions) {
    exit("No questions available.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $answers = $_POST["answers"] ?? [];

    if (!is_array($answers)) {
        $answers = [];
    }

    $score = 0;
    $total = count($questions);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "INSERT INTO results (user_id, score, total_questions)
             VALUES (?, 0, ?)"
        );
        $stmt->execute([$_SESSION["user_id"], $total]);
        $resultId = (int) $pdo->lastInsertId();

        $saveAnswer = $pdo->prepare(
            "INSERT INTO result_answers
             (result_id, question_id, selected_option, is_correct)
             VALUES (?, ?, ?, ?)"
        );

        foreach ($questions as $q) {
            $id = (int) $q["id"];
            $selected = $answers[$id] ?? "";

            // Accept only A, B, C or D
            if (!is_string($selected) ||
                !in_array($selected, ["A", "B", "C", "D"], true)) {
                $selected = "";
            }

            $correct = ($selected !== "" &&
                        $selected === $q["correct_option"]);

            if ($correct) {
                $score++;
            }

            $saveAnswer->execute([
                $resultId,
                $id,
                $selected !== "" ? $selected : null,
                $correct ? 1 : 0
            ]);
        }

        $update = $pdo->prepare(
            "UPDATE results SET score = ? WHERE id = ?"
        );
        $update->execute([$score, $resultId]);

        $pdo->commit();

        header("Location: result.php?id=" . $resultId);
        exit;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        exit("Could not submit the exam. Please try again.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Online Examination</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <strong>Online Examination</strong>
    <a href="logout.php">Logout</a>
</nav>

<div class="container">
    <div class="card">
        <h1>PHP & Web Development Quiz</h1>
        <p class="muted">
            Total questions: <?= count($questions) ?>.
            Each correct answer gives 1 mark.
        </p>

        <form method="POST"
              onsubmit="return confirm('Are you sure you want to submit your exam?');">

            <?php foreach ($questions as $index => $q): ?>
                <div class="card">
                    <h3>
                        Q<?= $index + 1 ?>.
                        <?= htmlspecialchars($q["question"]) ?>
                    </h3>

                    <?php
                    $options = [
                        "A" => $q["option_a"],
                        "B" => $q["option_b"],
                        "C" => $q["option_c"],
                        "D" => $q["option_d"]
                    ];
                    ?>

                    <?php foreach ($options as $key => $value): ?>
                        <label class="option">
                            <input type="radio"
                                   name="answers[<?= (int) $q["id"] ?>]"
                                   value="<?= $key ?>">
                            <?= $key ?>.
                            <?= htmlspecialchars($value) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit">Submit Examination</button>
        </form>
    </div>
</div>
</body>
</html>


<?php
session_start();

$host = "localhost";
$dbname = "online_exam";
$username = "root";
$password = ""; // Default XAMPP password is often empty

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );
} catch (PDOException $e) {
    exit("Database connection failed. Check config.php and MySQL.");
}
?>

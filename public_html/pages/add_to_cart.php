<?php
require_once 'check_user.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? intval($_POST['book_id']) : 0;

if ($book_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM carts WHERE user_id = ? AND book_id = ?");
    $stmt->execute([$user_id, $book_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE carts SET quantity = quantity + 1 WHERE cart_id = ?");
        $stmt->execute([$existing['cart_id']]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO carts (user_id, book_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$user_id, $book_id]);
    }
}

header('Location: ../index.php');
exit;
?>
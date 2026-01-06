<?php
session_start();
require_once __DIR__ . '/../content/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    exit("<script>alert('You must be logged in to add books to your reading list.'); window.history.back();</script>");
}

$user_id = $_SESSION['user_id'];

// Check if book ID is provided
if (!isset($_GET['id'])) {
    exit("<script>alert('No book specified.'); window.history.back();</script>");
}

$book_id = intval($_GET['id']);

// Check if the book exists
$stmt = $conn->prepare("SELECT id, title FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    exit("<script>alert('Book not found.'); window.history.back();</script>");
}

// Check if the book is already in the reading list
$stmt = $conn->prepare("SELECT id FROM reading_list WHERE user_id = ? AND book_id = ?");
$stmt->bind_param("ii", $user_id, $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    exit("<script>alert('Book is already in your reading list.'); window.history.back();</script>");
}

// Insert into reading list
$insert = $conn->prepare("INSERT INTO reading_list (user_id, book_id) VALUES (?, ?)");
$insert->bind_param("ii", $user_id, $book_id);
$insert->execute();

echo "<script>alert('Book added to your reading list!'); window.location.href='manage_books.php';</script>";

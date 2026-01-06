<?php
session_start();
require_once __DIR__ . '/../content/db.php';

if(!isset($_SESSION['user_id'])) exit('Not logged in');
if(!isset($_POST['book_id']) || !isset($_POST['notes'])) exit('Invalid request');

$book_id = intval($_POST['book_id']);
$user_id = $_SESSION['user_id'];
$notes = $_POST['notes'];

$stmt = $conn->prepare("UPDATE reading_list SET notes=? WHERE user_id=? AND book_id=?");
$stmt->bind_param("sii",$notes,$user_id,$book_id);
$stmt->execute();
echo "Saved";
?>

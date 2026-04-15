<?php
require_once 'config.php';

if (!isset($_POST['school_id'])) {
    echo "error";
    exit;
}

$school_id = trim($_POST['school_id']);

$stmt = $db->prepare("SELECT id FROM valid_faculty_ids WHERE school_id = ?");
$stmt->execute([$school_id]);

if ($stmt->rowCount() > 0) {
    echo "valid";
} else {
    echo "invalid";
}

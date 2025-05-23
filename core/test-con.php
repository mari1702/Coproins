<?php
require_once 'Database.php';

try {
    $db = Database::getConnection();
    echo "Connected successfully";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

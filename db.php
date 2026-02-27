<?php

try {
    $dbh = new PDO(
        "mysql:host=localhost;dbname=bookshelf",
        "root",
        "root",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo "Error! " . $e->getMessage();
    exit;
}
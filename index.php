<?php

require_once("./db.php");

$book_id = filter_input(INPUT_GET, 'book_id', FILTER_VALIDATE_INT);

if (!$book_id) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid book_id']);
    exit;
}

$sql = "SELECT 
            b.id AS book_id,
            b.title,
            b.isbn,
            l.language_name,
            a.id AS author_id,
            a.name AS author_name,
            g.id AS genre_id,
            g.genre_name
        FROM books b
        LEFT JOIN languages l ON b.language_id = l.id
        LEFT JOIN book_authors ba ON b.id = ba.book_id
        LEFT JOIN authors a ON ba.author_id = a.id
        LEFT JOIN book_genres bg ON b.id = bg.book_id
        LEFT JOIN genres g ON bg.genre_id = g.id
        WHERE b.id = :book_id";

$stmt = $dbh->prepare($sql);
$stmt->execute(['book_id' => $book_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$book = null;

if ($results) {
    foreach ($results as $row) {
        if ($book === null) {
            $book = [
                'id' => $row['book_id'],
                'title' => $row['title'],
                'isbn' => $row['isbn'],
                'language' => $row['language_name'],
                'authors' => [],
                'genres' => []
            ];
        }

        if (!empty($row['author_id']) && !isset($book['authors'][$row['author_id']])) {
            $book['authors'][$row['author_id']] = [
                'id' => $row['author_id'],
                'name' => $row['author_name']
            ];
        }

        if (!empty($row['genre_id']) && !isset($book['genres'][$row['genre_id']])) {
            $book['genres'][$row['genre_id']] = [
                'id' => $row['genre_id'],
                'name' => $row['genre_name']
            ];
        }
    }

    $book['authors'] = array_values($book['authors']);
    $book['genres'] = array_values($book['genres']);
}

header('Content-Type: application/json');
if ($book) {
    echo json_encode($book, JSON_PRETTY_PRINT);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Book not found']);
}
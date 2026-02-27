<?php
    require_once("./db.php");

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed. Use POST.']);
        exit;
    }

    $title = $_POST["title"] ?? null;
    $authorName = $_POST["author"] ?? null; 
    $isbn = $_POST["isbn"] ?? null; 

    if (!$title || !$authorName || !$isbn) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing title, author or ISBN']);
        exit;
    }

    try {
        
        $dbh->beginTransaction();

        $sqlBook = "INSERT INTO books (title, isbn) VALUES(:title, :isbn)";
        $stmtBook = $dbh->prepare($sqlBook);
        $stmtBook->bindParam("title", $_POST["title"]);
        $stmtBook->bindParam("isbn", $_POST["isbn"]);
        $stmtBook->execute();
        $newBookId = $dbh->lastInsertId();

        $sqlCheckAuth = "SELECT id FROM authors WHERE name = :name LIMIT 1";
        $stmtCheckAuth = $dbh->prepare($sqlCheckAuth);
        $stmtCheckAuth->execute(['name' => $authorName]);
        $existingAuthor = $stmtCheckAuth->fetch();

        if ($existingAuthor) {
            $authorId = $existingAuthor['id'];
        } else {
            
            $sqlAuth = "INSERT INTO authors (name) VALUES(:name)";
            $stmtAuth = $dbh->prepare($sqlAuth);
            $stmtAuth->execute(['name' => $authorName]);
            $authorId = $dbh->lastInsertId();
        }

        $sqlLink = "INSERT INTO book_authors (book_id, author_id) VALUES(:book_id, :author_id)";
        $stmtLink = $dbh->prepare($sqlLink);
        $stmtLink->execute(['book_id' => $newBookId, 'author_id' => $authorId]);

        $dbh->commit();
        
        http_response_code(201);
        echo json_encode([
            'message' => 'Book created successfully',
            'book_id' => $newBookId,
            'author_id' => $authorId,
            'title' => $title,
            'author' => $authorName,
            'isbn' => $isbn
        ]);

    } catch (Exception $e) {
        if ($dbh->inTransaction()) {
            $dbh->rollBack();
        }
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
?>


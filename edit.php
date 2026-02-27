<?php 
    require_once("./db.php");

    header('Content-Type: application/json');


    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed. Use PUT.']);
        exit;
    }

    $inputData = json_decode(file_get_contents("php://input"), true);

    $bookId = $inputData["id"] ?? null;
    $title = $inputData["title"] ?? null;
    $authorName = $inputData["author"] ?? null; 
    $isbn = $inputData["isbn"] ?? null; 

    if (!$bookId || !$title || !$authorName || !$isbn) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing id, title, author or ISBN']);
        exit;
    }

    try {

        $dbh->beginTransaction();


        $sqlBook = "UPDATE books SET title = :title, isbn = :isbn WHERE id = :id";
        $stmtBook = $dbh->prepare($sqlBook);
        $stmtBook->bindParam(":title", $title);
        $stmtBook->bindParam(":isbn", $isbn);
        $stmtBook->bindParam(":id", $bookId);
        $stmtBook->execute();


        if ($stmtBook->rowCount() === 0) {
        }

        $sqlCheckAuth = "SELECT id FROM authors WHERE name = :name LIMIT 1";
        $stmtCheckAuth = $dbh->prepare($sqlCheckAuth);
        $stmtCheckAuth->execute([':name' => $authorName]);
        $existingAuthor = $stmtCheckAuth->fetch();

        if ($existingAuthor) {
            $authorId = $existingAuthor['id'];
        } else {

            $sqlAuth = "INSERT INTO authors (name) VALUES(:name)";
            $stmtAuth = $dbh->prepare($sqlAuth);
            $stmtAuth->execute([':name' => $authorName]);
            $authorId = $dbh->lastInsertId();
        }

        $sqlDeleteLinks = "DELETE FROM book_authors WHERE book_id = :book_id";
        $stmtDeleteLinks = $dbh->prepare($sqlDeleteLinks);
        $stmtDeleteLinks->execute([':book_id' => $bookId]);

        $sqlLink = "INSERT INTO book_authors (book_id, author_id) VALUES(:book_id, :author_id)";
        $stmtLink = $dbh->prepare($sqlLink);
        $stmtLink->execute([':book_id' => $bookId, ':author_id' => $authorId]);

        $dbh->commit();
        
        http_response_code(200);
        echo json_encode([
            'message' => 'Book updated successfully',
            'book_id' => $bookId,
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
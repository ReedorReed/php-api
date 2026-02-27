<?php 
    if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
        http_response_code(405);
        echo "METHOD NOT ALLOWED";
        exit;
    }

    if (!$_GET["id"]) {
        http_response_code(405);
        echo "MISSING ID";
        exit;
    }

    require_once("./db.php");

    $sql = "DELETE FROM books WHERE id = :id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam("id", $_GET["id"], PDO::PARAM_INT);
    $stmt->execute();

    http_response_code(200);
    echo "Successfully deleted";
    exit;
?>
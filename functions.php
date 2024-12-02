<?php

function createDatabaseIfNotExists($pdo, $db_name) {
    // Проверка существования базы данных
    $stmt = $pdo->prepare("SELECT COUNT(*) 
                           FROM information_schema.schemata 
                           WHERE schema_name = :database_name");
    $stmt->execute([':database_name' => $db_name]);

    if ($stmt->fetchColumn() == 0) {
        // Если базы данных не существует, создаем её
        $pdo->exec("CREATE DATABASE `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
        echo "1. База данных `$db_name` успешно создана. <br>";
    } else {
        echo "1. База данных `$db_name` уже существует. <br>";
    }
}

function executeSQLFile($pdo, $filePath) {
    $sql = file_get_contents($filePath);
    if ($sql === false) {
        throw new Exception("Не удалось открыть файл $filePath");
    }
    $pdo->exec($sql);
}

function fetchData($url) {
    $json = file_get_contents($url);
    return json_decode($json, true);
}

function savePosts($db_connect, $posts) {
    $stmt = $db_connect->prepare("INSERT IGNORE INTO posts (id, title, body) VALUES (:id, :title, :body)");
    foreach ($posts as $post) {
        $stmt->execute([
            ':id' => $post['id'],
            ':title' => $post['title'],
            ':body' => $post['body']
        ]);
    }
}

function saveComments($db_connect, $comments) {
    $stmt = $db_connect->prepare("INSERT IGNORE INTO comments (id, postId, name, email, body) VALUES (:id, :postId, :name, :email, :body)");
    foreach ($comments as $comment) {
        $stmt->execute([
            ':id' => $comment ['id'],
            ':postId' => $comment['postId'],
            ':name' => $comment['name'],
            ':email' => $comment['email'],
            ':body' => $comment['body']
        ]);
    }
}

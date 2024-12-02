<?php

require_once __DIR__ . '/functions.php';

$config = require_once __DIR__ . '/config/database.php';

try {
    $pdo = new PDO("{$config['driver']}:host={$config['host']};port={$config['port']}",
        $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    createDatabaseIfNotExists($pdo, $config['dbname']);
    // Подключение к базе данных после её создания
    $db_connect = new PDO("{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['dbname']}",
        $config['user'], $config['password']);
    $db_connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "2. Подключение к базе данных успешно установлено. <br>";

    // Создание таблиц
    $path = __DIR__ . '/sql/create_tables.sql';
    executeSQLFile($db_connect, $path);
    echo "3. Таблицы успешно созданы. <br>";

    // Загрузка данных
    $posts = fetchData('https://jsonplaceholder.typicode.com/posts');
    savePosts($db_connect, $posts);
    $comments = fetchData('https://jsonplaceholder.typicode.com/comments');
    saveComments($db_connect, $comments);

    echo "4. Загружено " . count($posts) . " записей и " . count($comments) . " комментариев. <br>";
} catch (PDOException $exception) {
    echo "Ошибка: " . $exception->getMessage();
    die();
}

// Форма поиска
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['query'])) {
    $query = $_GET['query'];
    if (strlen($query) < 3) {
        echo "<br> Введите минимум 3 символа для поиска.";
    } else {
        $stmt = $db_connect->prepare("
            SELECT posts.title, comments.body 
            FROM comments 
            JOIN posts ON comments.postId = posts.id 
            WHERE comments.body LIKE :query
        ");
        $stmt->execute([':query' => '%' . $query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $result) {
            echo "<h2>{$result['title']}</h2>";
            echo "<p>{$result['body']}</p>";
        }
    }
}

?>

<form method="GET" action="index.php">
    <input type="text" name="query" placeholder="Введите текст комментария">
    <button type="submit">Найти</button>
</form>

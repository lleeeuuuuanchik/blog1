<?php
session_start();

// connect to db
$db = new PDO('mysql:host=localhost;dbname=blog-first', 'root', 'root');

/**
 * User
 */
$user = isset($_SESSION['user_id']) ? getUser($_SESSION['user_id']) : false;

if(isset($forAuth) && !$user) {
    redirect('login.php');
}

/**
 * Functions
 */
function dump($parameter) {
    echo '<pre>';
    var_dump($parameter);
    echo '</pre>';
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function getUser($id) {
    global $db;

    $user = $db->query("SELECT * FROM users WHERE id = " . intval($id))->fetch();

    if($user) {
        $user['name'] = htmlspecialchars($user['name']);
    }

    return $user;
}

function preparePost($post) {
    $post['title'] = htmlspecialchars($post['title']);
    $post['description'] = htmlspecialchars($post['description']);
    $post['content'] = htmlspecialchars($post['content']);
    $post['date'] = date('d.m.Y H:i', strtotime($post['date'])) ? date('d.m.Y H:i', strtotime($post['date'])) : 'date empty';
    $post['author'] = getUser($post['user_id']);

    return $post;
}

function getPost($id) {
    global $db;

    $post = $db->query("SELECT * FROM posts WHERE id = " . intval($id))->fetch();

    return $post ? preparePost($post) : false;
}

function hasAccess($post) {
    global $user;

    return $user && $post && $user['id'] === $post['user_id'];
}

function uploadImage($image) {
    // разбиваем строку название файла на массив используя разделитель '.'
    $extensionArray = explode('.', $image['name']);
    // получаем последний элемент массива, т.е. extension
    $extension = $extensionArray[count($extensionArray) - 1];
    // генерируем уникальное имя файла и подставляем расширение файла
    $fileName = uniqid() . '.' . $extension;
    // указываем путь к файлу от корня
    $imagePath = 'images/' . $fileName;

    // перемещаем файл из временной директории в нашу
    move_uploaded_file($image['tmp_name'], $imagePath);

    return $imagePath;
}

function validatePost($title, $description, $content, $image, $isEdit = false) {
    $errors = [];

    // проверяем отправлен ли файл
    if($image['size'] > 0 || $isEdit === false) {
        // объявляем массив с разрешенными типами файлов
        $types = [
            'image/jpeg',
            'image/png',
            'image/gif',
        ];
        // проверяем входит ли тип файла в разрешенные типы
        if (!in_array($image['type'], $types)) {
            $errors['image'] = 'Incorrect file type';
        }
        // проверяем размер файла в байтах
        if ($image['size'] > 1 * 1024 * 1024) {
            $errors['image'] = 'Incorrect image size';
        }
    }

    $titleLength = mb_strlen($title);
    if(!$title || $titleLength > 255) {
        $errors['title'] = 'Incorrect title';
    }

    $descriptionLength = mb_strlen($description);
    if(!$description || $descriptionLength > 500) {
        $errors['description'] = 'Incorrect description';
    }

    if(!$content) {
        $errors['content'] = 'Field is required';
    }

    return $errors;
}

<?php
$forAuth = true;
include 'includes/header.php';

$post = getPost($_GET['id']);
if(!hasAccess($post)) {
    redirect('index.php');
}

$title = $post['title'];
$description = $post['description'];
$content = $post['content'];

$errors = [];
if(isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $image = $_FILES['image'];

    $errors = validatePost($title, $description, $content, $image, true);

    if(count($errors) === 0) {
        $imagePath = $post['image_path'];
        if($image['size'] > 0) {
            // удаляем старый файл
            unlink($post['image_path']);
            $imagePath = uploadImage($image);
        }

        $query = $db->prepare("UPDATE posts SET image_path = :image_path, title = :title, description = :description, content = :content WHERE id = :id");
        $query->execute([
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'id' => $post['id'],
            'image_path' => $imagePath,
        ]);
        redirect('post.php?id=' . $post['id']);
    }
}

?>

<h1>Edit post</h1>
<form action="edit.php?id=<?= $post['id'] ?>" novalidate method="post" enctype="multipart/form-data">
    <div>
        <label>
            Post title:<br>
            <input type="text" placeholder="Post title" name="title" value="<?= $title ?? '' ?>">
            <?= $errors['title'] ?? '' ?>
        </label>
    </div>
    <div>
        <label>
            Post description:<br>
            <textarea placeholder="Post description" name="description"><?= $description ?? '' ?></textarea>
            <?= $errors['description'] ?? '' ?>
        </label>
    </div>
    <div>
        <label>
            Post content:<br>
            <textarea placeholder="Post content" name="content"><?= $content ?? '' ?></textarea>
            <?= $errors['content'] ?? '' ?>
        </label>
    </div>
    <div>
        <label>
            Post image:<br>
            <img src="<?= $post['image_path'] ?>" alt="" style="width: 150px;"><br>
            <input type="file" name="image">
            <?= $errors['image'] ?? '' ?>
        </label>
    </div>

    <div>
        <input type="submit" value="Edit" name="submit">
    </div>
</form>


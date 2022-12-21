<?php
$forAuth = true;
include 'includes/header.php';

$errors = [];
if(isset($_POST['submit'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $content = trim($_POST['content']);
    $image = $_FILES['image'];

    $errors = validatePost($title, $description, $content, $image);

    if(count($errors) === 0) {
        $imagePath = uploadImage($image);

        $query = $db->prepare("INSERT INTO posts (title, description, content, user_id, image_path) VALUES (:title, :description, :content, :user_id, :image_path)");
        $query->execute([
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'user_id' => $user['id'],
            'image_path' => $imagePath,
        ]);

        redirect('index.php');
    }
}
?>

    <h1>Create post</h1>
    <form action="create.php" novalidate method="post" enctype="multipart/form-data">
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
                <input type="file" name="image">
                <?= $errors['image'] ?? '' ?>
            </label>
        </div>

        <div>
            <input type="submit" value="Create" name="submit">
        </div>
    </form>

<?php
include 'includes/footer.php';

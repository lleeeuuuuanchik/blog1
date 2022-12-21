<?php
include 'includes/header.php';
?>

<?php
// идентификатор пользователя
$userId = $_GET['user_id'] ?? null;
// условие для выборки потстов определенного пользователя из БД, изначально пустое
$userWhere = '';
// кол-во постов на одной странице
$perPage = 2;
// берем текущую страницу, если не указано, то это первая страница
$page = $_GET['page'] ?? 1;
// вычисляем кол-во постов, которое нужно пропустить
// для первой страницы нужно пропустить 0 постов, поэтому начинаем с 0
$offset = ($page - 1) * $perPage;

// если указан идентификатор пользователя, то меняем условие для выборки постов
if($userId) {
    $userWhere = "WHERE user_id = " . intval($userId);
}

$sqlString = "SELECT * FROM posts $userWhere ORDER BY date DESC LIMIT $perPage OFFSET $offset";

$posts = $db->query($sqlString)->fetchAll();
?>

<?php foreach ($posts as $post):
    $post = preparePost($post);
    ?>
    <article>
        <img src="<?= $post['image_path'] ?>" alt="" style="width: 300px">
        <h2><?= $post['title'] ?></h2>
        <p>
            <b><?= $post['date'] ?></b>
            <a href="index.php?user_id=<?= $post['author']['id'] ?>"><?= $post['author']['name'] ?></a>
        </p>
        <p><?= $post['description'] ?></p>
        <p><a href="post.php?id=<?= $post['id'] ?>">Read more...</a></p>
    </article>
    <hr>
<?php endforeach; ?>


<?php
// SQL-запрос на получение всех постов, не забываем указать фильтр для пользователя
$sqlCount = "SELECT * FROM posts $userWhere";
// выполняем запрос и получаем кол-во строк
$postsCount = $db->query($sqlCount)->rowCount();
// вычисляем номер последней страницы, округляем в большую сторону дробную часть
$lastPage = ceil($postsCount / $perPage);
?>
<nav>
    <?php if($page == 1): ?>
        <span>Prev</span> |
    <?php else: ?>
        <a href="index.php?page=<?= $page - 1 ?>">Prev</a> |
    <?php endif; ?>

    <?php for($i = 1; $i <= $lastPage; $i++): ?>
        <?php if($page == $i): ?>
            <span><b><?= $i ?></b></span> |
        <?php else: ?>
            <a href="index.php?page=<?= $i ?>"><?= $i ?></a> |
        <?php endif; ?>
    <?php endfor; ?>

    <?php if($page >= $lastPage): ?>
        <span>Next</span>
    <?php else: ?>
        <a href="index.php?page=<?= $page + 1 ?>">Next</a>
    <?php endif; ?>
</nav>

<?php
include 'includes/footer.php';
?>
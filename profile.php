<?php
$forAuth = true;
include 'includes/header.php';

// Заполняем данные пользователя
$name = $user['name'];
$email = $user['email'];

// Создаем массив для ошибок
$errors = [];
// проверяем отправку формы
if(isset($_POST['submit'])) {
    // Берем данные из формы
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $newPassword = $_POST['new_password'];
    $newPasswordConfirm = $_POST['new_password_confirm'];

    // Проверка длины строки name
    $nameLen = mb_strlen($name);
    if($nameLen < 1 || $nameLen > 30) {
        $errors['name'] = 'Incorrect name length (1 - 30)';
    }

    // валидация на корректность email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Incorrect email';
    }
    // Подготовка запроса на уникальность email
    $emailQuery = $db->prepare("SELECT * FROM users WHERE email = :email AND id <> :user_id");
    // выполнение запроса
    $emailQuery->execute([
        'email' => $email,
        'user_id' => $user['id'],
    ]);
    // Получение первой строки из БД (массив или false)
    if($emailQuery->fetch()) {
        $errors['email'] = 'Email is exist';
    }

    // если указан новый пароль (т.е. если не пустая строка)
    if($newPassword) {
        // длина строки нового пароля
        $newPasswordLen = mb_strlen($newPassword);
        if($newPasswordLen < 3) {
            $errors['new_password'] = 'Incorrect new password length';
        }

        // сравниваем строки нового пароля и его повторения
        if($newPassword !== $newPasswordConfirm) {
            $errors['new_password_confirm'] = 'Incorrect confirmation';
        }

        // сравниваем хэш старого пароля из формы с хэшем пароля из БД
        if(md5($password) !== $user['password']) {
            $errors['password'] = 'Incorrect password';
        }
    }

    // если нет ошибок
    if(count($errors) === 0) {
        // если указан новый пароль, то заполняем его хэш, иначе берем старый хэш из БД
        $password = $newPassword ? md5($newPassword) : $user['password'];
        // подготовка запроса
        $query = $db->prepare("UPDATE users SET name = :name, email = :email, password = :password WHERE id = :user_id");
        // выполнение запроса
        $query->execute([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'user_id' => $user['id']
        ]);

        // флаг успешного сохранения для вывода сообщения
        $successUpdate = true;
    }
}
?>

<h1>Profile</h1>
<?= isset($successUpdate) ? 'Success update' : '' ?>
<form action="profile.php" method="post" novalidate>
    <label>
        Name:<br>
        <input type="text" name="name" value="<?= $name ?>">
        <?= $errors['name'] ?? '' ?>
    </label><br>
    <label>
        Email:<br>
        <input type="text" name="email" value="<?= $email ?>">
        <?= $errors['email'] ?? '' ?>
    </label><br>
    <label>
        Password:<br>
        <input type="password" name="password">
        <?= $errors['password'] ?? '' ?>
    </label><br>
    <label>
        New password:<br>
        <input type="password" name="new_password">
        <?= $errors['new_password'] ?? '' ?>
    </label><br>
    <label>
        New password confirm:<br>
        <input type="password" name="new_password_confirm">
        <?= $errors['new_password_confirm'] ?? '' ?>
    </label><br>
    <input type="submit" name="submit" value="Save">
</form>
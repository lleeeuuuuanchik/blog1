<?php
include 'includes/header.php';

$errors = [];
if(isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $nameLength = mb_strlen($name);
    if($nameLength < 1 || $nameLength > 30) {
        $errors['name'] = 'Name is not correct';
    }

    if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = 'Email is not correct';
    }

    $existEmailQuery = $db->prepare("SELECT * FROM users WHERE email = :email");
    $existEmailQuery->execute([
        'email' => $email,
    ]);
    if($existEmailQuery->fetch() !== false) {
        $errors['email'] = 'EMail is exist';
    }

    $passwordLength = mb_strlen($password);
    if($passwordLength < 3) {
        $errors['password'] = 'Password is not correct';
    }

    if(count($errors) === 0) {
        $insertQuery = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $insertQuery->execute([
            'name' => $name,
            'email' => $email,
            'password' => md5($password),
        ]);

        redirect('login.php');
    }
}
?>

    <h1>Registration</h1>
    <form action="registration.php" novalidate method="post">
        <div>
            <label>
                Name:
                <input type="text" placeholder="Your name" name="name"
                       value="<?= $name ?? '' ?>"
                >
                <?= $errors['name'] ?? '' ?>
            </label>
        </div>
        <div>
            <label>
                E-Mail:
                <input type="email" placeholder="Your email" name="email"
                       value="<?= $email ?? '' ?>">
                <?= $errors['email'] ?? '' ?>
            </label>
        </div>
        <div>
            <label>
                Password:
                <input type="password" placeholder="Your password" name="password">
                <?= $errors['password'] ?? '' ?>
            </label>
        </div>

        <div>
            <input type="submit" name="submit" value="Registration">
        </div>
    </form>

<?php
include 'includes/footer.php';

<?php
include 'includes/header.php';

$errors = [];
if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$email) {
        $errors['email'] = 'field is required';
    }
    if (!$password) {
        $errors['password'] = 'field is required';
    }

    if(count($errors) === 0) {
        $query = $db->prepare("SELECT * FROM users WHERE email = :email");
        $query->execute([
            'email' => $email,
        ]);
        $user = $query->fetch();

        if($user) {
            if(md5($password) === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                redirect('index.php');
            }
        }

        $errors['email'] = 'Data is incorrect';
    }
}
?>

    <h1>Login</h1>
    <form action="login.php" novalidate method="post">
        <div>
            <label>
                E-Mail:
                <input type="email" placeholder="Your email" name="email"
                       value="<?= $email ?? '' ?>"
                >
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
            <input type="submit" value="Login" name="submit">
        </div>
    </form>

<?php
include 'includes/footer.php';
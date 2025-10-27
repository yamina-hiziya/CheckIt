<?php
require_once __DIR__ . '/templates/header.php';
require_once __DIR__ . '/lib/pdo.php';
require_once __DIR__ . '/lib/user.php';

$errors = [];

if (isset($_POST['loginUser'])) {
    $user = verifyUserLoginPassword($pdo, $_POST['email'], $_POST['password']);

    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit();
    } else {
        $errors[] = "Email ou mot de passe incorrect";
    }
}
?>


<div class="container col-xxl-8 px-4 py-5">
    <h1>Se Connecter</h1>
    <?php
    foreach ($errors as $error) { ?>
        <div class="alert alert-danger" role="alert">
            <?= $error; ?>
        </div>
    <?php } ?>
    <form action="" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>

        <input type="submit" value="Connexion" name="loginUser" class="btn btn-primary">
    </form>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
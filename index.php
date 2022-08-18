<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcs.php';


if (isset($_POST['register'])) {
    registration();
    header("Location: index.php");
    die;
}

if (isset($_POST['auth'])) {
    login();
    header("Location: index.php");
    die;
}

if (isset($_POST['upload'])) {
    upload();
    header("Location: index.php");
    die;
}

if (isset($_GET['do']) && $_GET['do'] == 'exit') {
    if (!empty($_SESSION['user'])) {
        unset($_SESSION['user']);
    }
    header("Location: index.php");
    die;
}

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Галлерея</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
<body>

<div class="container">

    <div class="row my-3">
        <div class="col">
            <?php if (!empty($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['errors'];
                    unset($_SESSION['errors']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($_SESSION['user']['login'])): ?>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h3>Регистрация</h3>
            </div>
        </div>

        <form action="index.php" method="post" class="row g-3">
            <div class="col-md-6 offset-md-3">
                <div class="form-floating mb-3">
                    <input type="text" name="login" class="form-control" id="floatingInput" placeholder="Имя">
                    <label for="floatingInput">Логин</label>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <div class="form-floating">
                    <input type="password" name="pass" class="form-control" id="floatingPassword"
                           placeholder="Password">
                    <label for="floatingPassword">Пароль</label>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <div class="form-floating">
                    <input type="password" name="confirm_pass" class="form-control" id="floatingPassword"
                           placeholder="Password">
                    <label for="floatingPassword">Подтверждение пароля</label>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <button type="submit" name="register" class="btn btn-primary">Зарегистрироваться</button>
            </div>
        </form>

        <div class="row mt-3">
            <div class="col-md-6 offset-md-3">
                <h3>Авторизация</h3>
            </div>
        </div>

        <form action="index.php" method="post" class="row g-3">
            <div class="col-md-6 offset-md-3">
                <div class="form-floating mb-3">
                    <input type="text" name="login" class="form-control" id="floatingInput" placeholder="Имя">
                    <label for="floatingInput">Логин</label>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <div class="form-floating">
                    <input type="password" name="pass" class="form-control" id="floatingPassword"
                           placeholder="Password">
                    <label for="floatingPassword">Пароль</label>
                </div>
            </div>

            <div class="col-md-6 offset-md-3">
                <button type="submit" name="auth" class="btn btn-primary">Войти</button>
            </div>
        </form>

    <?php else: ?>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <p>Добро пожаловать в галлерею, <?= $_SESSION['user']['login'] ?>! <a href="?do=exit">Log out</a>
            <h4>Загрузите ваше изображение</h4>
            <p>Максимальный размер файла:
            <?php echo UPLOAD_MAX_SIZE / 1000000; ?>Мб.</p>
            <p>Допустимые форматы:
            <?php echo implode(', ', ALLOWED_TYPES) ?>.</p>
            </p>
        </div>
    </div>

    <div class="col-md-6 offset-md-3">
        <form method='post' action="/index.php" enctype="multipart/form-data">
            <input type='file' name='file[]' class="form-control" id='file-drop' multiple required><br>
            <input name="upload" type='submit' value='Загрузить' class="form-control">
        </form>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="g-3 col-md-6 offset-md-3">
            <h3><a href="/gallary.php">Перейти в галлерею</a></h3>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
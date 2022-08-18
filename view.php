<?php
// Скрипт для отображения картинки и комментариев к ней
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcs.php';

$filePath = UPLOAD_DIR . '/' . $_GET['file'];
$fileName = $_GET['file'];

if (isset($_POST['add'])) {
    $fileName = $_POST['file'];
    save_comments();
    header("Location: /view.php/?file=$fileName");
    die;
}

if (isset($_GET['del_com'])) { //гет запрос для удаления коммента к определенной картинке
    delete_comment($_GET['del_com']);
    header("Location: /view.php/?file=$fileName");
    die;
}

$comments = get_comments($fileName);
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
    </div

    <div class="row">
        <div class="g-3 col-md-6 offset-md-3">
            <a href="/index.php">На главную</a><br>
            <a href="/gallary.php">Назад</a>
        </div>
    </div>
    <br>

    <div class="col-md-6 offset-md-3">
        <div class="form-floating mb-3">
            <img width="1000px" src="../<?= $filePath ?>" alt="Не удалось загрузить картинку">
        </div>
    </div>

    <p><a name="comm"></a></p>
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <h3>Комментарии</h3>
        </div>
    </div>
    <form action="view.php" method="post" class="row g-3 mb-5">
        <div class="col-md-6 offset-md-3">
            <div class="form-floating">
                <textarea class="form-control" name="comment" placeholder="Leave a comment here"
                          id="floatingTextarea" style="height: 100px;"></textarea>
                <label for="floatingTextarea">Сообщение</label>
            </div>
        </div>
        <input type="hidden" name="file" value="<?= $fileName ?>">

        <div class="col-md-6 offset-md-3">
            <button type="submit" name="add" class="btn btn-primary">Отправить</button>
        </div>
    </form>


    <?php if (!empty($comments)): ?>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <hr>
                <?php foreach ($comments as $comment): ?>
                    <div class="card my-3">
                        <div class="card-body">
                            <h5 class="card-title">Автор: <?= htmlspecialchars($comment['login']) ?></h5>
                            <?php if ($comment['login'] == $_SESSION['user']['login']): ?>
                            <p><a href="/view.php/?file=<?= $fileName ?>&del_com=<?= $comment['id'] ?>">Удалить</a></p>
                            <?php endif; ?>
                            <p class="card-text"><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                            <p>Дата: <?= $comment['date'] ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
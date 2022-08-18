<?php
// Скрипт для отображения галлереи
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/funcs.php';

if (isset($_GET['del'])) {              //гет запрос для удаления определенной картинки
    delete_image($_GET['del']);
    header("Location: /gallary.php");
    die;
}

$images = get_images();

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
    <link rel="stylesheet" type="text/css" href="custom.css">
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

    <div class="row">
        <div class="g-3 col-md-6 offset-md-3">
            <a href="/index.php">На главную</a>
        </div>
    </div>

    <?php
    foreach ($images as $image) {

        $fileName = basename($image['path']);
        if ($_SESSION['user']['login'] == $image['login']) {
            $del = "Это ваша картинка <a href='gallary.php/?del=$fileName'>Удалить</a>";
        } else {
            $del = '';
        }

        echo <<<END
    <figure class="caption-border">
        <p>Загрузил: {$image['login']} {$image['date']}</p>
         <p>$del</p>
        <a href="/view.php/?file=$fileName"><img src="{$image['path']}"></a></p>
        <figcaption>
            <p><a href='/view.php/?file=$fileName#comm'>Комментарии</a></p>
        </figcaption>
    </figure>
END;
    }
    ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>
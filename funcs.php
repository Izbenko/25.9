<?php

function registration(): bool //регистриция
{
    global $pdo;

    $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
    $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';
    $cpass = !empty($_POST['confirm_pass']) ? trim($_POST['confirm_pass']) : '';

    if (empty($login) || empty($pass) || empty($cpass)) {
        $_SESSION['errors'] = 'Поля логин/пароль обязательны';
        return false;
    }

    if ($pass !== $cpass) {
        $_SESSION['errors'] = 'Пароли не совпадают';
        return false;
    }

    $res = $pdo->prepare("SELECT COUNT(*) FROM users WHERE login = ?");
    $res->execute([$login]);
    if ($res->fetchColumn()) {
        $_SESSION['errors'] = 'Данный логин уже используется';
        return false;
    }

    $pass = password_hash($pass, PASSWORD_DEFAULT);
    $res = $pdo->prepare("INSERT INTO users (login, pass) VALUES (?,?)");
    if ($res->execute([$login, $pass])) {
        $_SESSION['success'] = 'Успешная регистрация';
        return true;
    } else {
        $_SESSION['errors'] = 'Ошибка регистрации';
        return false;
    }
}

function login(): bool //вход
{
    global $pdo;
    $login = !empty($_POST['login']) ? trim($_POST['login']) : '';
    $pass = !empty($_POST['pass']) ? trim($_POST['pass']) : '';

    if (empty($login) || empty($pass)) {
        $_SESSION['errors'] = 'Поля логин/пароль обязательны';
        return false;
    }

    $res = $pdo->prepare("SELECT * FROM users WHERE login = ?");
    $res->execute([$login]);
    if (!$user = $res->fetch()) {
        $_SESSION['errors'] = 'Логин/пароль введены неверно';
        return false;
    }

    if (!password_verify($pass, $user['pass'])) {
        $_SESSION['errors'] = 'Логин/пароль введены неверно';
        return false;
    } else {
        $_SESSION['success'] = 'Вы успешно авторизовались';
        $_SESSION['user']['login'] = $user['login'];
        $_SESSION['user']['id'] = $user['id'];
        return true;
    }
}

function upload(): bool //загрузка изображения
{
    global $pdo;


    if (!empty($_FILES)) {
        for ($i = 0; $i < count($_FILES['file']['name']); $i++) {

            $fileName = $_FILES['file']['name'][$i];

            if ($_FILES['file']['size'][$i] > UPLOAD_MAX_SIZE) {
                $_SESSION['errors'] = 'Недопустимый размер файла ' . $fileName;
                return false;
            }

            if (!in_array($_FILES['file']['type'][$i], ALLOWED_TYPES)) {
                $_SESSION['errors'] = 'Недопустимый формат файла ' . $fileName;
                return false;
            }

            $filePath = UPLOAD_DIR . '/' . time() . basename($fileName);

            if (!move_uploaded_file($_FILES['file']['tmp_name'][$i], $filePath)) {
                $_SESSION['errors'] = 'Ошибка загрузки файла ' . $fileName;
                return false;
            } else {

                $res = $pdo->prepare("INSERT INTO images (path, user_id) VALUES (?,?)");
                $res->execute([$filePath, $_SESSION['user']['id']]);
            }
        }
        $_SESSION['success'] = 'Файлы загружены';
        return true;
    }
}

function  save_comments(): bool //отправить коммментарий
{
    global $pdo;

    $res = $pdo->prepare("SELECT id FROM images WHERE path = ?");
    $res->execute([UPLOAD_DIR . '/' . $_POST['file']]);
    $res = $res->fetchAll();
    $id = $res[0]['id'];

    $comment = !empty($_POST['comment']) ? trim($_POST['comment']) : '';

    if (!isset($_SESSION['user']['login'])) {
        $_SESSION['errors'] = 'Необходимо авторизоваться';
        return false;
    }

    if (empty($comment)) {
        $_SESSION['errors'] = 'Введите текст сообщения';
        return false;
    }

    $res = $pdo->prepare("INSERT INTO comments (login, text, img_id) VALUES (?,?,?)");
    if ($res->execute([$_SESSION['user']['login'], $comment, $id])) {
        $_SESSION['success'] = 'Сообщение добавлено';
        return true;
    } else {
        $_SESSION['errors'] = 'Ошибка!';
        return false;
    }
}

function get_images(): array //возвращает массив всех изображений
{
    global $pdo;
    $res = $pdo->query("SELECT * FROM images JOIN users ON images.user_id=users.id ORDER BY date DESC");
    return $res->fetchAll();
}

function delete_image($fileName): bool //удалить изображение
{
    $fileName = UPLOAD_DIR . '/' . $fileName;
    if (!isset($_SESSION['user']['id'])) {
        $_SESSION['errors'] = 'Ошибка удаления';
        return false;
    } else {
        $id = $_SESSION['user']['id'];
    }

    global $pdo;

    $res = $pdo->prepare("SELECT path, user_id FROM images WHERE path = ?");
    $res->execute([$fileName]);
    $res = $res->fetchAll();

    if (empty($res) or $res[0]['user_id'] != $id){ //проверка на возможность удаления
        $_SESSION['errors'] = 'Ошибка удаления';
        return false;
    } else {
        $res = $pdo->prepare("DELETE FROM images WHERE user_id = ? AND path = ?");
        $res->execute([$id, $fileName]);
        unlink($fileName);
        $_SESSION['success'] = 'Картинка удалена';
        return true;
    }
}

function get_comments($fileName): array //массив всех комментов у картинки
{
    $filePath = UPLOAD_DIR . '/' . basename($fileName);
    global $pdo;
    $res = $pdo->prepare("SELECT comments.id as id, text, comments.date as date, login FROM comments join images ON images.id=comments.img_id WHERE images.path = ? ORDER BY date DESC");
    $res->execute([$filePath]);
    return $res->fetchAll();
}

function delete_comment($id): bool //удаление комментария по его id
{
    global $pdo;

    $res = $pdo->query("SELECT login FROM comments WHERE id=$id");
    $res = $res->fetchAll();
    $user = $res[0]['login'];

    if ($user == $_SESSION['user']['login']) {                           //проверка на возможность удаления
        $pdo->query("DELETE FROM comments WHERE id =$id");
        $_SESSION['success'] = 'Комментарий удален';
        return true;
    } else {
        $_SESSION['errors'] = 'Ошибка удаления';
        return false;
    }
}


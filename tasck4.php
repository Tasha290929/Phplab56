<?php
/**
 * Sanitizes the given data.
 * @param string $data The data to sanitize.
 * @return string The sanitized data.
 */
function sanitizeData(string $data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES);
}

$errors = [];

// Обработчик регистрации
if (isset($_POST["register"])) {
    // Валидация данных
    if (empty($_POST['login'])) {
        $errors['login'][] = 'Введите имя!';
    }
    if (empty($_POST['password'])) {
        $errors['password'][] = 'Введите пароль!';
    }

    // Другие проверки могут быть добавлены здесь

    if (count($errors) === 0) {
        $data = [
            'name' => sanitizeData($_POST['login']),
            'password' => md5($_POST['password']) // Хеширование пароля
        ];

        // Проверка наличия пользователя
        $log = fopen("users.txt", "r") or die("Недоступный файл!");
        while (!feof($log)) {
            $line = trim(fgets($log));
            if (strpos($line, $data['name']) !== false) {
                $errors['login'][] = 'Пользователь с таким именем уже существует!';
                fclose($log);
                break;
            }
        }

        // Действия, если пользователя не существует
        if (!isset($errors['login'])) {
            // Сохранение данных в файл
            $log = fopen("users.txt", "a+") or die("Недоступный файл!");
            fwrite($log, $data['name'] . ":" . $data['password'] . PHP_EOL);
            fclose($log);

            // Добавьте сообщение об успешной регистрации
        }
    }
}

// Обработчик аутентификации
if (isset($_POST["auth"])) {
    $data = [
        'login' => sanitizeData($_POST['login']),
        'password' => md5($_POST['password'])
    ];

    $log = fopen("users.txt", "r") or die("Недоступный файл!");
    while (!feof($log)) {
        $line = trim(fgets($log));
        if (strpos($line, $data['login']) !== false) {
            $line = explode(":", $line);
            if ($line[1] === $data['password']) { // Сравнение хэшей паролей
                header(/* Перенаправление пользователя на страницу с изображениями */);
            } else {
                $errors['auth'][] = 'Неверный логин или пароль!';
            }
            fclose($log);
            break;
        }
    }

    // Действия, если пользователя не найден
    if (!isset($errors['auth'])) {
        $errors['auth'][] = 'Пользователь не найден!';
    }
}

?>

<div>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
        <label>
            <span>Name</span>
            <input name="login"/>
            <?php if (!empty($errors["login"])) : ?>
                <?php foreach ($errors["login"] as $error) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </label>
        <label>
            <span>Password</span>
            <input type="password" name="password">
            <?php if (!empty($errors["password"])) : ?>
                <?php foreach ($errors["password"] as $error) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </label>
        <input type="submit" name="register" value="Регистрация"/>
    </form>
</div>

<div>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
        <label>
            <span>Name</span>
            <input name="login"/>
        </label>
        <label>
            <span>Password</span>
            <input type="password" name="password">
        </label>
        <input type="submit" name="auth" value="Аутентификация"/>
        <?php if (!empty($errors["auth"])) : ?>
            <?php foreach ($errors["auth"] as $error) : ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
    </form>
</div>

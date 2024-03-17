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
    return htmlspecialchars($data);
}
$errors = [];
$loginValue = '';
$passwordValue = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Валидация данных
    if (empty($_POST['login'])) {
        $errors['login'][] = 'Введите имя!';
        $loginValue = ''; // Стираем неправильный логин
    } else {
        $loginValue = sanitizeData($_POST['login']);
    }
    if (empty($_POST['password'])) {
        $errors['password'][] = 'Введите пароль!';
        $passwordValue = ''; // Стираем неправильный пароль
    } else {
        $password = sanitizeData($_POST['password']);
        if (strlen($password) < 8) {
            $errors['password'][] = 'Пароль должен содержать минимум 8 символов!';
            $passwordValue = ''; // Стираем неправильный пароль
        } elseif (!preg_match("/[A-Z]/", $password)) {
            $errors['password'][] = 'Пароль должен содержать хотя бы одну заглавную букву!';
            $passwordValue = ''; // Стираем неправильный пароль
        } elseif (!preg_match("/\d/", $password)) {
            $errors['password'][] = 'Пароль должен содержать хотя бы одну цифру!';
            $passwordValue = ''; // Стираем неправильный пароль
        } elseif (!preg_match("/[^a-zA-Z0-9]/", $password)) {
            $errors['password'][] = 'Пароль должен содержать хотя бы один специальный символ!';
            $passwordValue = ''; // Стираем неправильный пароль
        } else {
            $passwordValue = $password;
        }
    }


   
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <title>Document</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        main {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            text-align: center;
        }

        .form-signin {
            width: 100%;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }
    </style>
    <link href="sign-in.css" rel="stylesheet">
    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/sign-in/">
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">


<main class="form-signin w-100 m-auto">
<?php
  if (count($errors) === 0) {
    // Проверяем, существует ли уже пользователь с таким логином
    $usersData = file_get_contents("users.txt");
    $users = explode(PHP_EOL, $usersData);
    $loginExists = false;

    foreach ($users as $userData) {
        list($savedLogin, $savedPassword) = explode(':', $userData);
        if ($savedLogin === $loginValue) {
            $loginExists = true;
            break;
        }
    }

    if ($loginExists) {
        $errors['login'][] = 'Пользователь с таким логином уже существует!';
    } else {
        // Если пользователя с таким логином нет, добавляем его в файл
        $userData = sanitizeData($_POST['login']) . ':' . md5($_POST['password']) . PHP_EOL;
        file_put_contents("users.txt", $userData, FILE_APPEND | LOCK_EX);
        // Устанавливаем HTTP-код 201 (Created)
        http_response_code(201);
        ?>
        <div class="alert alert-success" role="alert">
            Регистрация прошла успешно!
        </div>
        <?php
    }
}
?>
    <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">

        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>

        <div class="form-floating">
            <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="login" value="<?php echo $loginValue; ?>">
            <label for="floatingInput">Email address </label>
            <?php if (isset($errors["login"])) : ?>
                <?php foreach ($errors["login"] as $error) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" value="<?php echo $passwordValue; ?>">
            <label for="floatingPassword">Password </label>
            <?php if (isset($errors["password"])) : ?>
                <?php foreach ($errors["password"] as $error) : ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="form-check text-start my-3">
            <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
            <label class="form-check-label" for="flexCheckDefault">
                Remember me
            </label>
        </div>
        <button class="btn btn-primary w-100 py-2" type="submit" name="register">Sign in</button>
        
    </form>

</main>


    <script src="/docs/5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

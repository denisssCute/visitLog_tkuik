<?php
require_once '..\vendor\connect.php';
require '..\vendor\db_for_rb.php';

$data = $_POST;
$errors = array();
if (isset($data['do_signup'])) {
    if ($data['name'] == '') {
        $errors[] = 'Введите имя!';
    }
    if ($data['disciplina'] == '') {
        $errors[] = 'Введите дисциплину 1!';
    }
    if ($data['login'] == '') {
        $errors[] = 'Введите логин!';
    }
    if ($data['password'] == '') {
        $errors[] = 'Введите пароль!';
    }
    
    if(empty($errors)) {
        $user = R::dispense('teachers');
        $user->name = $data['name'];
        $user->disciplina = $data['disciplina'];
        if($data['disciplina2'] == "") {
            $user->disciplina2 = NULL;
        } else {$user->disciplina2 = $data['disciplina2'];}
        if($data['disciplina3'] == "") {
            $user->disciplina3 = NULL;
        } else {$user->disciplina3 = $data['disciplina3'];}
        $user->login = $data['login'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        R::store($user);
    } else {
        echo array_shift($errors);
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../index.css">
    <title>Журнал</title>
</head>
<body>
    <div class="container">
<!--        <div>-->
<!--            <form class="container-child" action="REGISTRATION.php" method="post">-->
<!--                <input type="text" name="name" placeholder="Фамилия Имя Отчество...">-->
<!--                <input type="text" name="disciplina" placeholder="Дисциплина 1...">-->
<!--                <input type="text" name="disciplina2" placeholder="Дисциплина 2...">-->
<!--                <input type="text" name="disciplina3" placeholder="Дисциплина 3...">-->
<!---->
<!--                <input type="text" name="login" placeholder="Логин...">-->
<!--                <input type="text" name="password" placeholder="Пароль...">-->
<!--                <button id="to_main" name="do_signup" type="submit">ЗАРЕГИСТРИРОВАТЬ</button>-->
<!--            </form>-->
<!--        </div>-->
        <div class="reg-win">
            <form class="form-main" action="REGISTRATION.php" method="post">
                <h1 class="form-title">Регистрация</h1>
                <div class="form-group">
                    <input type="text" class="form-input" name="name" placeholder=" ">
                    <label class="form-label">Фамилия Имя Отчество</label>
                </div>
                <div class="form-group">
                    <input class="form-input" type="text" name="disciplins_input" placeholder=" ">
                    <label class="form-label">Название предмета</label>
                    <div class="add_disc_header">
                        <span align="left" style="text-decoration: underline; font-family: Calibri; font-weight: 600">Предметы</span>
                        <button class="form-button addDiscBtn" onclick="addDisc()">Добавить</button>
                    </div>
                    <ul class="disciplins-ul">
                        <li align="left">Матан</li>
                        <li align="left">Физкультура</li>
                        <li align="left">Русский язык</li>

                    </ul>
                </div>
                <div class="form-group">
                    <input type="text" class="form-input" name="login" placeholder=" ">
                    <label class="form-label">Логин</label>
                </div>
                <div class="form-group">
                    <input type="text" class="form-input" name="password" placeholder=" ">
                    <label class="form-label">Пароль</label>
                </div>
                <button class="form-button" id="to_main" name="do_login" type="submit">Зарегистрировать</button>
            </form>
        </div>
        <p class="go-to-reg-text">Уже есть аккаунт? <a href="../index.php">Войдите</a></p>
    </div>
<script src="../main_content/js.js"></script>
<script src="registration.js"></script>
</body>
</html>
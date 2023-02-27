<?php
require_once '..\vendor\connect.php';

session_start();

if ($_SESSION['loggedin'] == false || !isset($_SESSION['id'])) {
    header('Location: ../index.php');
}

if (!isset($_SESSION['disciplina'])) {
    header('Location: ../select_disciplina/select_disciplina.php');
}

if(isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 4800) {
        header('Location: ../logout.php');
    }
}

$_SESSION['last_activity'] = time();

$idTeacher = $_SESSION['id'];

$discTeacher = $_SESSION['disciplina'];
$nameTeacher = $_SESSION['name_teacher'];

$nameDisc = $_SESSION['disciplina']; //запрос(следующие несколько строчек), возвращающий номер таблицы с предметом, для последующего взаимодействия с этой таблицей
$number_table = mysqli_query($connect, "SELECT lessons_hours.table_number FROM lessons_hours WHERE lessons_hours.discName = '$nameDisc'");
$number_table = mysqli_fetch_all($number_table);
$number_table =  $number_table[0][0]; //номер таблицы с предметом

$_SESSION['number_table'] = $number_table;

$listGroup = mysqli_query($connect, "SELECT students.group_number FROM students JOIN disciplina_$number_table ON disciplina_$number_table.id = students.id;"); //создание списка всех сущ-их групп для тэга select
$listGroup = mysqli_fetch_all($listGroup);
$listGroupUnique = array();
foreach($listGroup as $itemListGroup) {
foreach ($itemListGroup as $item) {
        $listGroupUnique[] = $item;
    }
}
$listGroupUnique = array_unique($listGroupUnique); //после всех предыдущих манипуляций получаем список с группами преподавателя на конкретном предмете

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css.css">
    <title>Журнал</title>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-content-header">
                <div class="menu-button">
                    <a href="../logout.php"onclick="logout()" class="link-nav logoutBtn">
                        Выйти
                    </a>
                    <a href="../select_disciplina/select_disciplina.php" class="link-nav">
                        Выбрать предмет
                    </a>
                </div>
                <div class="info-teacher">
                    <div>
                        <span>Предмет: </span>
                        <span class='title-header'><?=$discTeacher?></span>
                    </div>
                    <div>
                        <span>Преподаватель: </span>
                        <span class='title-header'><?=$nameTeacher?></span>
                    </div>
                </div>
            </div>
            
            <form action="main.php" class="show_group" method="post">
                <select name="group_number" id="search_group" onchange="SaveValueDiscLS(this)">
                    <option value="Группа">Группа</option>
                    <?php foreach($listGroupUnique as $group) { // заполняем select группами
                        echo "<option value='$group'>$group</option>";
                    }?>
                </select>
                <button class="form-button" type="submit">Показать</button>
            </form>
            <a href="../add_group/add.php" class="link-nav addBtn">Добавить группу</a>
        </div>
        <div class="content">
            <div class="left-content">

            </div>
            <div class="right-content">
                <div class="right-content-top">
                    
                </div>
                <div class="right-content-bottom">
                </div>
            </div>
        </div>
    </div>
<script src="js.js"></script>
</body>
</html>
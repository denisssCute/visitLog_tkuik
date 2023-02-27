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
$number_table = $_SESSION['number_table'];
$discTeacher = $_SESSION['disciplina'];
$nameTeacher = $_SESSION['name_teacher'];

$query = "SELECT completed_state FROM `lessons_hours` WHERE discName = ?;";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "s", $discTeacher);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$result = mysqli_fetch_all($result);
$json = $result[0][0];
$json_data = json_decode($json, true);


$listGroup = mysqli_query($connect, "SELECT students.group_number FROM students JOIN disciplina_$number_table ON disciplina_$number_table.id = students.id;");
$listGroup = mysqli_fetch_all($listGroup);
$listGroupUnique = array();

foreach ($listGroup as $itemListGroup) {
    foreach ($itemListGroup as $item) {
        $listGroupUnique[] = $item;
    }
}

$listGroupUnique = array_unique($listGroupUnique);

$group = $_POST['group_number'];
if ($group == 'Группа') {
    header('Location: select.php');
}

$disciplinaCookie = $_COOKIE['sql_disciplina'];
$banTems = array();
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
                    <a href="../logout.php" onclick="logout()" class="link-nav logoutBtn">
                        Выйти
                    </a>
                    <a href="../select_disciplina/select_disciplina.php"class="link-nav">
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
            <a href="../add_group/add.php" class="link-nav">Добавить группу</a>
        </div>
        <div class="content">
            <div class="left-content">
                <?php
                $sql = "SELECT completed_state FROM `lessons_hours` WHERE discName = '$discTeacher'";
                $json = mysqli_query($connect, $sql);
                $json = mysqli_fetch_all($json);
                ?>
                <textarea type="text" id="json_completed" class="invisibleTextarea" style="display: none" ><?=$json[0][0]?></textarea>
                <table id="main_table">

                        <tr>
                            <th style="background: rgb(150,220,253); "><?=$group?></th>
                            <?php
                                $query = "SELECT * FROM `lessons_hours` WHERE lessons_hours.discName = ? AND lessons_hours.group_number = ?;";
                                $stmt = mysqli_prepare($connect, $query);
                                mysqli_stmt_bind_param($stmt, "ss", $discTeacher,$group);
                                mysqli_stmt_execute($stmt);
                                $list = mysqli_stmt_get_result($stmt);
                                $list = mysqli_fetch_all($list);

                                $t=1;
                                while ($t <= $list[0][8] / 2) {
                                    if ($json_data["tema_$t"]['complete'] == 0) {
                                        echo "<th id='tema$t'class=\"thMainTable\">$t</th>";
                                        echo "<div class=\"modal\" id='modal$t'>";
                                        echo "<div class=\"header-modal\">";
                                        echo "<span>Тема №$t</span>";
                                        echo "<span class=\"closeBtn\" id=\"btn$t\">&times;</span>";
                                        echo "</div>";
                                        echo "<input class=\"inputComplete\" value=\"1111-11-11\" type=\"date\" id=\"input$t\" placeholder=\"Дата урока\">";
                                        echo "<button id=\"completeBtn$t\" class=\"completeBtn\">Завершить урок</button>";
                                        echo "</div>";
                                    } else {
                                        $date = $json_data["tema_$t"]['date'];
                                        array_push($banTems, $t);
                                        echo "<th id='tema$t' class='thMainTable' style='background-color: rgb(182,215,255);cursor: pointer;'>$t</th>";
                                        echo "<div class=\"modal\" id='modal$t'>";
                                        echo "<div class=\"header-modal\">";
                                        echo "<span style='font-weight: 600'>Тема №$t</span>";
                                        echo "<span class=\"closeBtn\" id=\"btn$t\">&times;</span>";
                                        echo "</div>";
                                        echo "<span>Тема завершена $date</span>";
                                        echo "</div>";
                                    }

                                    $t++;
                                }

                                while ($t <= $list[0][9] / 2 + $list[0][8] / 2) {
                                    if ($json_data["tema_$t"]['complete'] == 0) {
                                        echo "<th id='tema$t'class=\"thMainTable\" >$t</th>";
                                        echo "<div class=\"modal\" id='modal$t'>";
                                        echo "<div class=\"header-modal\">";
                                        echo "<span>Тема №$t</span>";
                                        echo "<span class=\"closeBtn\" id=\"btn$t\">&times;</span>";
                                        echo "</div>";
                                        echo "<input class=\"inputComplete\" value=\"1111-11-11\" type=\"date\" id=\"input$t\" placeholder=\"Дата урока\">";
                                        echo "<button id=\"completeBtn$t\" class=\"completeBtn\">Завершить урок</button>";
                                        echo "</div>";
                                    } else {
                                        $date = $json_data["tema_$t"]['date'];
                                        array_push($banTems, $t);
                                        echo "<th id='tema$t' class='thMainTable' style='background-color: rgb(182,215,255);cursor: pointer;' >$t</th>";
                                        echo "<div class=\"modal\" id='modal$t'>";
                                        echo "<div class=\"header-modal\">";
                                        echo "<span style='font-weight: 600'>Тема №$t</span>";
                                        echo "<span class=\"closeBtn\" id=\"btn$t\">&times;</span>";
                                        echo "</div>";
                                        echo "<span>Тема завершена $date</span>";
                                        echo "</div>";
                                    }

                                    $t++;
                                }
                            ?>

                        </tr>
                    <?php
                        $sql = "SELECT * FROM `students` JOIN disciplina_$number_table ON students.id = disciplina_$number_table.id
                        WHERE students.group_number = '$group' ORDER BY students.name";
                        $info = mysqli_query($connect, $sql);
                        $info = mysqli_fetch_all($info);
                        foreach ($info as $item) {
                            ?>
                            <tr class="trMainTable">
                                <td class="name_for_js" id="<?=$item[0]?>" onclick="showPersonStats(<?=$item[0]?>)"><?=$item[1]?></td>
                                <?php
                                $td = 4;
                                $numberCletki = 1;
                                while ($td <= ($list[0][8] / 2 + $list[0][9] / 2) + 3) {
                                    if (!in_array($numberCletki,$banTems)) {
                                        echo "<td><textarea onclick='putH(this)' class='Н_$item[0]_$numberCletki' spellcheck='false' maxlength='1'value=''>$item[$td]</textarea></td>";
                                    } else {
                                        echo "<td><textarea class='Н_$item[0]_$numberCletki' spellcheck='false' maxlength='1' readonly style='background: rgb(234, 234, 234); cursor: default'>$item[$td]</textarea></td>";
                                    }
                                    $td++;
                                    $numberCletki++;
                                }
                                ?>
                            </tr>
                            <?php
                        }
                    ?>
                </table>
            </div>
            <div class="right-content">
                <div class="right-content-top"></div>
                <div class="right-content-bottom">
                    <form action='update.php' method='post'>
                        <h3>Обновить данные:</h3>
                        <select id="selectUpdateGroup" onchange="SaveValueDiscLS(this)">
                        <?php foreach($listGroupUnique as $groupForSelect2) { // заполняем select группами
                            echo "<option value='$groupForSelect2'>$groupForSelect2</option>";
                        }?>
                        </select>
                        <textarea type="text" id="disciplinaName" name="disciplinaName" class="invisible"></textarea>
                        <textarea name='SfQL' type="text"  id='toSQLinput' class="invisible"></textarea>
                        <textarea name='cmplt' type="text"  id='completionInput' class="invisible"></textarea>
                        <button type="submit" id="updateBtn" class="form-button" onclick="toSQL() putJsonInCompleteInput()">Обновить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="js.js"></script>
<script src="main.js"></script>
</body>
</html>

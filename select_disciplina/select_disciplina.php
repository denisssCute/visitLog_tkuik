<?php
require_once '..\vendor\connect.php';


session_start();

if ($_SESSION['loggedin'] == false || !isset($_SESSION['id'])) {
    header('Location: ../index.php');
}

if(isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > 4800) {
        header('Location: ../logout.php');
    }
}

$_SESSION['last_activity'] = time();

$idTeacher = $_SESSION['id'];

$nameTeacher = $_SESSION['name_teacher'];

$data = $_POST;
if (isset($data['do_select_disciplina']) && $data['select-disciplina'] != 'Выберите предмет...') {
    $_SESSION['disciplina'] = $data['select-disciplina'];
    header('Location: ../main_content/select.php');
}

$query = "SELECT teachers.disciplins FROM `teachers` WHERE teachers.name = ?;"; //составление списка предметов преподавателя
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, "s", $nameTeacher);
mysqli_stmt_execute($stmt);
$disciplins = mysqli_stmt_get_result($stmt);
$disciplins = mysqli_fetch_all($disciplins);
$disciplins = $disciplins[0][0];
$disciplins = json_decode($disciplins, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../main_content/css.css">
    <title>Добавить группу</title>
</head>
<body>
    <a href="../logout.php" class="back-to-login link-nav logoutBtn">
        Выйти
    </a>
    <div class="container-add-group-main">
        <div class="modal-win">
            <form action="select_disciplina.php" method="post" class="select-disciplina-form">
                <h3>Пожалуйста, выберите предмет, с которым хотите работать: </h3>
                <select name="select-disciplina" id="">
                    <option value="Выберите предмет...">Выберите предмет...</option>
                    <?php foreach($disciplins as $disc) {
                        if ($disc != NULL) {
                            echo "<option value='$disc'>$disc</option>";
                        }
                    }?>
                </select>
                <button name="do_select_disciplina" class="form-button">Далее</button>
            </form>
        </div>
    </div>


<script src="../main_content/js.js"></script>
</body>
</html>
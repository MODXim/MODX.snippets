<?php
//Обращаемся к специальному файлу для генерирования картинки
//После генерации текстовое содержимое записывается в $_SESSION['veriword']
$var = $modx->config['base_url'].'manager/includes/veriword.php?rand='.rand();

//форма
echo "<img src=\"$var\" alt=\"verification code\" /><br />";
echo <<<HERE
        
</div> <div class="form">
        <form method="post" action="[~[*id*]~]">
        
 
        <label class="lname">Вводим код</label>
        <input type="text"  name="code" value="" />

        <input type="submit" name="submit" value="Прверим!">
HERE;
//подготовка переменных
$input = $_POST['code'];
$code = $_SESSION['veriword'];
//Проверки
if (isset($_POST['submit'])){
        if ($input==$code){
                echo "ура!";
                }
        else {
                echo "не попал";
                }
}
?>
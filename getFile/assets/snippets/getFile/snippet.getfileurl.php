<?php
if ($_GET['id']) {
    return 'download.html?id='.$_GET['id'].'&go=true';
} else { die('Error: Missing parameter id'); }
?>
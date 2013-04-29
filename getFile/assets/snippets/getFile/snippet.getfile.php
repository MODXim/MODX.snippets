<?php
function is_bot() { 
    $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
    "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
    "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
    "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp",
    "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
    "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
    "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
    "Butterfly","Twitturls","Me.dium","Twiceler");
 
    foreach($botlist as $bot) {
        if(strpos($_SERVER['HTTP_USER_AGENT'],$bot)!==false)
        return true;    // Это бот
    }
 
    return false;   // Не бот
}

if ($_GET['id']) {
    global $modx;
    $id_tv = $modx->getTemplateVarOutput(array('file'), $_GET['id']); // Получаем значение TV file
    $file = $id_tv['file']; // Получаем путь к файлу
    
    if (file_exists($file)) { // Если файл существует...
        if (!is_bot() && $_GET['go'] == true) { // ... а посетитель не бот, и стоит GET-параметр go, то отдаем файл на закачку 
        
            $table = $modx->getFullTableName('downloads');
            $id = $modx->db->escape($_GET['id']);
            $file_path = $modx->db->escape($file);
            $sql = "INSERT INTO $table (id, file, count) VALUES ('$id', '$file_path', 1) ON DUPLICATE KEY UPDATE count=count+1"; // Готовим строку запроса
            $result = $modx->db->query($sql); // Обновляем данные счетчика
            if (!$result) die('Error: Query Failed!');
            
            $file_name = basename($file); // Получаем имя файла
            $fsize = filesize($file); // и его размер
            $ftime = date("D, d M Y H:i:s T", filemtime($file)); 
            
            $file_handler = @fopen($file, "rb"); 
            
            if (!$file_handler) {
                header ("HTTP/1.0 403 Forbidden"); 
                exit; 
            }
            
            if ($HTTP_SERVER_VARS["HTTP_RANGE"]) {
            
                $range = $HTTP_SERVER_VARS["HTTP_RANGE"]; 
                $range = str_replace("bytes=", "", $range); 
                $range = str_replace("-", "", $range); 
                
                if ($range) {
                    fseek($file_handler, $range);
                }
            }
            
            
            if ($range) {
                header("HTTP/1.1 206 Partial Content"); 
            } else {
                header("HTTP/1.1 200 OK"); 
            }
            
            header("Content-Disposition: attachment; filename=$file_name"); 
            header("Last-Modified: $ftime"); 
            header("Accept-Ranges: bytes"); 
            header("Content-Length: ".($fsize-$range)); 
            header("Content-Range: bytes $range-".($fsize-1)."/".$fsize); 
            header("Content-type: application/octet-stream"); 
            
            while (!feof ($file_handler)) {
                echo fread($file_handler, 100);
            
            }
            // Отдали файл на закачку
            
            fclose($file_handler); 
            exit;
        }
    } else { // Если файл не существует, возвращаем 404
        header ("HTTP/1.0 404 Not Found"); 
        exit;
    };
    
} else { die('Error: Missing parameter id'); }
?>
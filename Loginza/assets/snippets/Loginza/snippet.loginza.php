<?php
global $modx;
include_once('assets/lib/json.php');   // подключение библиотеки для парсинга JSON, путь можно указать свой
mt_srand(microtime());

// конфигурация по-умолчанию //

if(!$providers) $providers = 'google,yandex,mailru,vkontakte,facebook,openid';
if(!$login_id) $login_id = $_GET['target'];     // ресурс, на который будет переадресован пользователь после авторизации
if(!$login_id) $login_id = 1;
if(!$register_id) $register_id = 1;             // ресурс, на который будет переадресован пользователь после регистраици
if(!$landing_url) $landing_url = '[(site_url)][~[*id*]~]';  // ресурс, на котором установлен сниппет [!loginza!]
$salt = 'example salt'; // изменить это!

$debug = 1; // для отладки присвоить 1
// При отладке выводится массив переданный Логинзой, массив основных парамеров пользователя, массив дополнительных параметров пользователя. Также при отладке не выполняется регистрация и авторизация пользователей

// шаблоны //

$tpl = array();
$tpl['button'] = '<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>
<h3><a href="https://loginza.ru/api/widget?providers_set='.$providers.'&token_url='.$landing_url.'" class="loginza"><img src="http://loginza.ru/favicon.ico" alt="Loginza"/> Войти через OpenID</a></h3>';
$tpl['user'] = '';
$tpl['msg'] = '';
$tpl['errors'] = '';
$tpl_outer = '<div class="loginza_form">[+button+][+user+][+msg+][+errors+]</div>';

function tpl_process($tpl, $values, $ignore='') {
    $pairs = array();
    if($ignore) $ignore = explode(',', $ignore); 
    foreach($values as $k=>$v) {
        if(is_array($ignore) && in_array($k,$ignore)) $v = '';
        $pairs['[+'.$k.'+]'] = $v; 
    } 
//   print_r($values); 
    return strtr($tpl, $pairs);
}

// бизнес-логика //

$target_id = $login_id;
$output = '';
$error = '';
$docid = $modx->documentIdentifier;

// 1) пользователь нажал "выйти", выходим, показываем форму с кнопкой
if($_REQUEST['webloginmode']=='lo') {
    $modx->runSnippet('WebLogin');
    return tpl_process($tpl_outer, $tpl);
} 

// 2) пользователь уже авторизован на сайте - выводим инфо о пользователе и заканчиваем работу
if($user_id = $modx->getLoginUserID()) { 
    $user_fullname = $modx->db->getValue( $modx->db->select('fullname',$modx->getFullTableName("web_user_attributes"),'id="'.$user_id.'"')); 

    $username = $modx->getLoginUserName();    
    $username_r = explode('@',$username);
    if(strlen($username_r[1])>3) $user_type = $username_r[1];
    $tpl['user'] = 'Приветствуем, <img src="http://'.$user_type.'/favicon.ico" alt="'.$user_type.'"/> <b>'.$user_fullname.'</b>!
 <a href="[~[*id*]~]?webloginmode=lo">Выйти</a>';
    return tpl_process($tpl_outer, $tpl, 'button');
}

// 3) пользователь не авторизован - проверяем, не запросил ли пользователь авторизацию через Логинзу

// получаем токен Логинзы
$loginza_token = $_POST['token'];

if($loginza_token)
{
    $json = new Services_JSON();    // получаем и декодируем данные от Логинзы
    $loginza_data = file_get_contents('http://loginza.ru/api/authinfo?token='.$loginza_token);
    $ldata = $json->decode($loginza_data);
    if(isset($ldata->error_type)) $tpl['error'] = 'Произошла ошибка: '.$ldata->error_message.'
 Попробуйте еще раз.'; 
} else {
// не запросил, выводим форму c кнопкой
    return tpl_process($tpl_outer, $tpl);
}

// логинза была запрошена, осуществляем процессинг
if(!$tpl['error']) {

    // определяем имя провайдера, логин и никнейм пользователя
    $ltype = parse_url($ldata->provider);
    $ltype = $ltype['host'];
    if(!isset($ldata->uid)) {
        $luid = parse_url($ldata->identity);
        $luid = explode('.',$luid['host']); 
        $luid = $luid[0]; 
    } else {
        $luid =  $ldata->uid;
    }
    if(!isset($ldata->nickname)) $lnick = $luid; else $lnick = $ldata->nickname;

    // здесь нужно помещать обработку для отдельных провайдеров: 
    if($ltype='openid.yandex.ru') $ltype = 'yandex.ru';

    // приводим данные, сообщенные Логинзой к формату данных пользователя MODx
    // стандартные атрибуты пользователя
    $udata = array();   
    $udata['username'] = $lnick.'@'.$ltype;
    $udata['email'] = $udata['username']; 
    $udata['password'] = substr(md5($udata['username'].$salt), 0, 6); 
    $udata['confirmpassword'] = $udata['password'];
    $udata['fullname'] = $lnick; 
    if(isset($ldata->name->full_name)) 
        $udata['fullname'] = $ldata->name->full_name;
    elseif( isset($ldata->name->last_name) && isset($ldata->name->first_name) )
        $udata['fullname'] = $ldata->name->last_name.' '.$ldata->name->first_name;
   
    // расширенные атрибуты пользователя   
    $uattr = array();   
    if($ldata->gender=="M") { $uattr['gender'] = "1"; } else if ($ldata->gender=="F") { $uattr['gender'] = "2"; } else { $uattr['gender'] = ""; }
    if($ldata->dob)         { $uattr['dob'] = date(strtotime($ldata->dob)); }  
    if($ldata->nickname)    { $uattr['comment'] = $modx->db->escape( $ldata->nickname ); } 
    if($ldata->photo)       { $uattr['photo'] = $modx->db->escape( $ldata->photo ); } 

    // вывод отладочной информации
    if($debug) { 
        echo '<pre>'; print_r($ldata); print_r($udata); print_r($uattr); echo '</pre>';
    }
    
    // заполняем массив _POST для сниппетов weblogin / websignup
    foreach($udata as $k=>$v) { $_POST[$k] = $v; }  
        
    // создаем пользователя, если такого еще не существует 
    if( !$modx->db->getValue( $modx->db->select('count(*)',$modx->getFullTableName("web_users"),'username="'.$modx->db->escape($udata['username']).'"'))) 
    { 
        if(!$debug) {
            // регистрируем через websignup 
            $_POST['cmdwebsignup']='Signup'; 
            $tpl['msg'] .= $modx->runSnippet('WebSignup',array('groups'=>'stud','useCaptcha'=>'0')); 

            // заполняем доп. атрибуты
            $uid = $modx->db->getValue( $modx->db->select('id',$modx->getFullTableName("web_users"),'username="'.$modx->db->escape($udata['username']).'"'));
            if($uid) {
                $modx->db->update($uattr, $modx->getFullTableName("web_user_attributes"), 'internalKey="'.$uid.'"'); 
            } 
            $target_id = $register_id; 
        } else {
            echo '<p>Do signup</p>'; 
        } 
    }
        
    // авторизуем пользователя через weblogin
    $_POST['cmdweblogin']='Login'; 
    if(!$debug) 
        $tpl['msg'] .= $modx->runSnippet('WebLogin', array('loginhomeid'=>$target_id) );
    else
        echo '<p>Do login</p>';
   
    // выводим инфо о пользователе, без кнопки
    return tpl_process($tpl_outer, $tpl, 'button');     
        
} else { 
// что-то пошло не так, выводим форму с кнопкой и ошибками

    if(!$tpl['errors']) $tpl['errors'] = 'Произошла ошибка, попробуйте еще раз.';
    $tpl['errors'] = '<div class="error">'.$tpl['errors'].'</div>';
    return tpl_process($tpl_outer, $tpl);
}
?>
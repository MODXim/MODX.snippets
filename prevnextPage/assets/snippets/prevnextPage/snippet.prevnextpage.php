<?php
/**
 * @name prevnextPage
 * @author Gorbarov Iliya at gorbarov.ru
 * @version 1.1
 * @desc Создает ссылки на предыдущую и следующую страницы
 *
 * Сниппет устанавливает плейсхолдеры [+pnp_prev+] [+pnp_next+]
 *
 * В плейсхолдерах находятся ссылки на предыдущую и следующую страницы, в зависимости от настроек сниппета
 * Плейсхолдеры устанавливаются только для документов в папке c &folderId
 *
 * &folderId - id папки
 * &sortBy - поле в базе данных для сортировки, например pub_date
 * &sortDir - Порядок сортировки ASC или DESC
 * &showHidden - Показывать скрытые пункты в меню, по умолчанию - 0
 * &letsCycle - Зациклить ссылки, по умолчанию - нет.
 * &prevTpl - Чанк для предыдущей ссылки
 * &nextTpl - Чанк для следующей ссылки
 * &id - Префикс плейсхолдеров для мультивызовов. По умолчанию 'pnp_'
 * &nextClass - CSS класс
 * &prevClass - CSS класс
 * &directOutput - Выводить результат напрямую, по умолчанию - использовать плейсхолдеры
 * &directOutputTpl - Шаблоны прямого вывода, строка, по умолчанию, [+prev+] | [+next+]
 *
 * Пример самого "обычного вызова": [[prevnextPage? &directOutput=`1`]]
 *
 */

if (!isset($folderId)) $folderId = 'parent';
if (!isset($sortBy)) $sortBy = 'menuindex';
if (!isset($sortDir)) $sortDir = 'ASC';
if (!isset($showHidden)) $showHidden = 0;
if (!isset($letsCycle)) $letsCycle = 0;
if (!isset($nextTpl) || !isset($prevTpl)) $useBuildInTpl = true;
if (!isset($nextClass)) $nextClass = '';
if (!isset($prevClass)) $prevClass = '';
if (!isset($directOutput)) $directOutput = 0;
if (!isset($directOutputTpl)) $directOutputTpl = '[+prev+] | [+next+]';
if (!isset($prevTpl)) $prevTpl = '<a href="[~[+id+]~]" class="'.$prevClass.'" title="[+pagetitle+]">[+pagetitle+]</a>';
if (!isset($nextTpl)) $nextTpl = '<a href="[~[+id+]~]" class="'.$nextClass.'" title="[+pagetitle+]">[+pagetitle+]</a>';
if (!isset($id)) $id = 'pnp_';

if (!function_exists('parseString')) {
    function parseString($tpl,$data,$prefix = '[+',$suffix = '+]') {
        foreach($data as $k => $v) {
            $tpl = str_replace($prefix.(string)$k.$suffix, (string)$v, $tpl);
        }
        return $tpl;
    };
}

$curId = $modx->documentIdentifier;
if ($folderId == 'parent') $folderId = array_pop($modx->getParentIds($curId,1));

$docs = $modx->getDocumentChildren ($folderId, 1, 0, 'id, pagetitle, parent', 'hidemenu = '.$showHidden, $sortBy, $sortDir, '');

foreach ($docs as $key=>$doc) {
    if ($doc['id'] == $curId) $curKey = $key;
}

$curDoc = $docs[$curKey];
$next = $docs[$curKey + 1];
$prev = $docs[$curKey - 1];

if (!isset($next) && $letsCycle) {
    $next = $docs[0];
}

if (!isset($prev) && $letsCycle) {
    $prev = $docs[count($docs) - 1];
}

if ($useBuildInTpl) {
    $rNext = parseString($nextTpl,array('id'=>$next['id'],'pagetitle'=>$next['pagetitle']));
    $rPrev = parseString($prevTpl,array('id'=>$prev['id'],'pagetitle'=>$prev['pagetitle']));
} else {
    $rNext = $modx->parseChunk($nextTpl,array('id'=>$next['id'],'pagetitle'=>$next['pagetitle']),'[+','+]');
    $rPrev = $modx->parseChunk($prevTpl,array('id'=>$prev['id'],'pagetitle'=>$prev['pagetitle']),'[+','+]');
}

if ($curDoc['parent'] == $folderId) {
    if ($directOutput) {
        echo parseString($directOutputTpl,array('next' => $rNext, 'prev' => $rPrev));
    } else {
        if (isset($next)) $modx->setPlaceholder($id.'next',$rNext);
        if (isset($prev)) $modx->setPlaceholder($id.'prev',$rPrev);
    }
}
?>
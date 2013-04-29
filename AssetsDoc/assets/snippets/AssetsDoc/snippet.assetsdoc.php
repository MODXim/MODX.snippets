<?php
/**
* AssetsDoc snippet for MODX Evolution
* @desc Список опубликованных документов с определенной группой к которой имеет доступ текущий пользователь. 
 
* @version 0.1
* @author Borisov Evgeniy aka Agel Nash (agel-nash@xaker.ru)
* @date 04.01.2013
*
* @category snippet
* @internal @modx_category Web-User
*
* @example
* [[AssetsDoc]] 
* список всех документов закрытых через веб-группы к которым имеет доступ текущий пользователь. Список документов разделен запятыми
*
* [[AssetsDoc? &ret=`array`]]
* Список документов отдается в виде массива
*
* [[AssetsDoc? &template=`7`]]
* Список документов с шаблоном под ID=7
*
* [[AssetsDoc? &folder=`1`]]
*  Документы должны быть контейнерами
*
*/
$userID = (isset($userID) && (int)$userID>0) ? (int)$userID : $modx->getLoginUserID();
$template = (isset($template) && (int)$template>0) ? (int)$template : 0;
$out=array();
if($userID>0){
  $sql=$modx->db->query("
	SELECT 
		DISTINCT c.id
	FROM 
		".$modx->getFullTableName('web_groups')." as wg 
	LEFT JOIN 
		".$modx->getFullTableName('webgroup_access')." as wa
			on wa.webgroup=wg.webgroup
	LEFT JOIN
		".$modx->getFullTableName('document_groups')." as dg
			on dg.document_group=wa.documentgroup
	LEFT JOIN
					".$modx->getFullTableName('site_content')." as c
						on c.id=dg.document
	WHERE 
		wg.webuser='1' 
		".((isset($folder) && in_array((int)$folder,array(0,1))) ? "AND c.isfolder='".(int)$folder."'" : "")."
		AND c.published='1'
		".(($template>0) ? ("AND c.template='".$template."'") : "")
	);
	$sql=$modx->db->makeArray($sql);
	foreach($sql as $item){
		$out[]=$item['id'];
	}
}
 
return (isset($ret) && $ret=='array') ? $out : implode(",",$out);
 
?>
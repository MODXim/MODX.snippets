<?php
$id=(isset($id) && (int)$id>0)?(int)$id:$modx->getLoginUserID(($mode=='mgr'?'mgr':'web'));
if($id>0){
        if(isset($info) && $info!='internalKey'){
                $data=($mode=='mgr')?$modx->getUserInfo($id):$modx->getWebUserInfo($id);
                if(isset($data[$info])) return $data[$info];
        }else{
                return $id;
        }
}
return '';
?>
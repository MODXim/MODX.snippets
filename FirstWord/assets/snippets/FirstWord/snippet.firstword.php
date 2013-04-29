<?php
$pagename=explode(' ',$modx->documentObject['pagetitle'],2);
return "<strong>".$pagename[0]."</strong> ".(isset($pagename[1]) ? $pagename[1] : '');
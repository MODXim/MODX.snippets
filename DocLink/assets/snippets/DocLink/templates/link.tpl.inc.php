<?php
/* link elements template for DocLink */

$this->set('tplOuter', '@CODE:[+inner+]');
$this->set('tplInner', '@CODE:[+home+][+parent+][+first+][+prev+][+next+][+last+]');
$this->set('tplHome', '@CODE:<link href="[(base_url)][+home.uri+]" rel="start" title="[+home.title+]" />' . "\n");
$this->set('tplParent', '@CODE:<link href="[(base_url)][+parent.uri+]" rel="up" title="[+parent.title+]" />' . "\n");
$this->set('tplFirst', '@CODE:<link href="[(base_url)][+first.uri+]" rel="first" title="[+first.title+]" />' . "\n");
$this->set('tplPrev', '@CODE:<link href="[(base_url)][+prev.uri+]" rel="prev" title="[+prev.title+]" />' . "\n");
$this->set('tplNext', '@CODE:<link href="[(base_url)][+next.uri+]" rel="next" title="[+next.title+]" />' . "\n");
$this->set('tplLast', '@CODE:<link href="[(base_url)][+last.uri+]" rel="last" title="[+last.title+]" />' . "\n");
$this->set('tplHomeOff', '');
$this->set('tplParentOff', '');
$this->set('tplFirstOff', '');
$this->set('tplPrevOff', '');
$this->set('tplNextOff', '');
$this->set('tplLastOff', '');
?>
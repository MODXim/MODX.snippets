<?php
/* simple template for DocLink */

$this->set('tplOuter', '@CODE:<div class="doclink doclink-simple"><ul>[+inner+]</ul></div>');
$this->set('tplInner', '@CODE:[+prev+][+next+]');
$this->set('tplHome', '');
$this->set('tplParent', '');
$this->set('tplFirst', '');
$this->set('tplPrev', '@CODE:<li class="previous enabled"><a href="[(base_url)][+prev.uri+]" title="[+prev.title+]">&laquo;&nbsp;Previous</a></li>');
$this->set('tplNext', '@CODE:<li class="next enabled"><a href="[(base_url)][+next.uri+]" title="[+next.title+]">Next&nbsp;&raquo;</a></li>');
$this->set('tplLast', '');
$this->set('tplHomeOff', '');
$this->set('tplParentOff', '');
$this->set('tplFirstOff', '');
$this->set('tplPrevOff', '@CODE:<li class="previous disabled">&laquo;&nbsp;Previous</li>');
$this->set('tplNextOff', '@CODE:<li class="next disabled">Next&nbsp;&raquo;</li>');
$this->set('tplLastOff', '');
?>
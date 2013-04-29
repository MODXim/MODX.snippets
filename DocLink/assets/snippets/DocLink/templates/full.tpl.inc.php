<?php
/* full template for DocLink */

$this->set('tplOuter', '@CODE:<div class="doclink doclink-full">[+inner+]</div>');
$this->set('tplInner', '@CODE:<ul>[+first+][+prev+][+home+][+next+][+last+]</ul>' . "\n" . '<p>[+page.current+] of [+page.total+] pages.</p>');
$this->set('tplHome', '@CODE:<li class="home enabled"><a href="[(base_url)][+home.uri+]" title="[+home.title+]">Home</a></li>');
$this->set('tplParent', '');
$this->set('tplFirst', '@CODE:<li class="first enabled"><a href="[(base_url)][+first.uri+]" title="[+first.title+]">&laquo;&nbsp;First</a></li>');
$this->set('tplPrev', '@CODE:<li class="previous enabled"><a href="[(base_url)][+prev.uri+]" title="[+prev.title+]">&lt;&nbsp;Previous</a></li>');
$this->set('tplNext', '@CODE:<li class="next enabled"><a href="[(base_url)][+next.uri+]" title="[+next.title+]">Next&nbsp;&gt;</a></li>');
$this->set('tplLast', '@CODE:<li class="last enabled"><a href="[(base_url)][+last.uri+]" title="[+last.title+]">Last&nbsp;&raquo;</a></li>');
$this->set('tplHomeOff', '@CODE:<li class="home disabled">Home</li>');
$this->set('tplParentOff', '');
$this->set('tplFirstOff', '@CODE:<li class="first disabled">&laquo;&nbsp;First</li>');
$this->set('tplPrevOff', '@CODE:<li class="previous disabled">&lt;&nbsp;Previous</li>');
$this->set('tplNextOff', '@CODE:<li class="next disabled">Next&nbsp;&gt;</li>');
$this->set('tplLastOff', '@CODE:<li class="last disabled">Last&nbsp;&raquo;</li>');
?>
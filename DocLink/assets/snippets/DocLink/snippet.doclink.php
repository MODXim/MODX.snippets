<?php
/* Title      : DocLink
 * Category   : Snippet
 * Author     : Phize
 * Author URI : http://phize.net
 * License    : GNU General Public License(http://www.gnu.org/licenses/gpl.html)
 * Version    : 1.0.0
 * Last Update: 2008-11-01
 */

$basePath = isset($basePath) ? $basePath : 'assets/snippets/doclink/';
$filename = $modx->config['base_path'] . $basePath . 'doclink.class.inc.php';

if (file_exists($filename)) include_once($filename); else return '';
if (class_exists('doclink')) $doclink = new doclink(); else return '';



$doclink->set('basePath', $basePath);

$doclink->set('home', $home, $modx->getConfig('site_start'));

$doclink->set('sortBy', $sortBy, 'createdon');
$doclink->set('sortDir', $sortDir, 'ASC');
$doclink->set('showInMenuOnly', $showInMenuOnly, false);
$doclink->set('showPublishedOnly', $showPublishedOnly, true);
$doclink->set('hideFolders', $hideFolders, true);
$doclink->set('exclude', explode(',', $exclude), array());

$doclink->set('template', $template, 'simple');
$doclink->set('style', $style, 'plane');

$doclink->set('titleSource', $titleSource, 'pagetitle');
$doclink->set('alwaysShowLinks', $alwaysShowLinks, true);
$doclink->set('alwaysOutput', $alwaysOutput, false);

$doclink->set('tplOuter', $tpl, '');
$doclink->set('tplInner', $tpl, '');
$doclink->set('tplHome', $tplHome, '');
$doclink->set('tplParent', $tplParent, '');
$doclink->set('tplFirst', $tplFirst, '');
$doclink->set('tplPrev', $tplPrev, '');
$doclink->set('tplNext', $tplNext, '');
$doclink->set('tplLast', $tplLast, '');
$doclink->set('tplHomeOff', $tplHomeOff, '');
$doclink->set('tplParentOff', $tplParentOff, '');
$doclink->set('tplFirstOff', $tplFirstOff, '');
$doclink->set('tplPrevOff', $tplPrevOff, '');
$doclink->set('tplNextOff', $tplNextOff, '');
$doclink->set('tplLastOff', $tplLastOff, '');



return $doclink->render();
?>
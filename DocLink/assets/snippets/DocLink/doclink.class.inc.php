<?php
/* Title      : DocLink class
 * Category   : Snippet
 * Author     : Phize
 * Author URI : http://phize.net
 * License    : GNU General Public License(http://www.gnu.org/licenses/gpl.html)
 * Version    : 1.0.0
 * Last Update: 2008-11-01
 */

class doclink {
    var $params = array();  // parameters
    var $docs = array();  // document objects
    var $total = 0;  // the number of total page
    var $current = null;  // the number of current page

    // constructor
    function __construct() {
    }

    // constructor for PHP4
    function doclink() {
        $this->__construct();
    }

    // set parameter
    function set($name, $param, $default = null) {
        $this->params[$name] = isset($param) ? $param : $default;
    }

    // get parameter
    function get($name) {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }

    // get the document excludes private it
    //
    // this function is based on DocumentParser::getDocument
    function getDocument($id = 0, $fields = '*') {
        if ($id == 0) {
            return false;
        } else {
            $ids = array($id);
            $docs = $this->getDocuments($ids, '', '', $fields);

            return ($docs != false) ? $docs[0] : false;
        }
    }

    // get the documents exclude private them
    //
    // this function is based on DocumentParser::getDocuments
    function getDocuments($ids = array(), $sort = 'menuindex', $dir = 'ASC', $fields = '*') {
        global $modx;

        if (count($ids) == 0) {
            return false;
        } else {
            $tblsc = $modx->getFullTableName('site_content');
            $tbldg = $modx->getFullTableName('document_groups');

            // add sc. to field names to refere to the table
            $fields = 'sc.' . implode(',sc.', preg_replace('/^\s/i', '', explode(',', $fields)));
            $sort = ($sort == '') ? '' : 'sc.' . implode(',sc.', preg_replace('/^\s/i', '', explode(',', $sort)));

            // get document groups for current user
            if ($docgrp = $modx->getUserDocGroups()) {
                $docgrp = implode(',', $docgrp);
            }

            // build the query to get documents
            $access = ($modx->isFrontend() ? 'sc.privateweb=0' : "1='" . $_SESSION['mgrRole'] . "' OR sc.privatemgr=0") .
                      (!$docgrp ? '' : ' OR dg.document_group IN (' . $docgrp . ')');

            $sql = 'SELECT DISTINCT ' . $fields . ' FROM ' . $tblsc . ' sc ' .
                   'LEFT JOIN ' . $tbldg . ' dg on dg.document = sc.id ' .
                   'WHERE (sc.id IN (' . implode(',', $ids) . ')) ' .
                   'AND (' . $access . ') ' .
                   'GROUP BY sc.id' .
                   ($sort ? ' ORDER BY ' . $sort . ' ' . $dir : '');

            $result = $modx->dbQuery($sql);
            $resource = array();

            // convert resources to array
            for ($i = 0; $i < @ $modx->recordCount($result); $i ++) {
                array_push($resource, @ $modx->fetchRow($result));
            }

            return $resource;
        }
    }

    // get a template
    //
    // this function is based on the code written by Doze
    // http://modxcms.com/forums/index.php/topic,5344.msg41096.html#msg41096
    function getTemplate($param){
        global $modx;

        $template = '';

        if ($modx->getChunk($param) != '') {
            $template = $modx->getChunk($param);
        } else if(substr($param, 0, 6) == '@FILE:') {
            $template = $this->get_file_contents(substr($param, 6));
        } else if(substr($param, 0, 6) == '@CODE:') {
            $template = substr($param, 6);
        } else if(substr($param, 0, 5) == '@FILE') {
            $template = $this->get_file_contents(trim(substr($param, 5)));
        } else if(substr($param, 0, 5) == '@CODE') {
            $template = trim(substr($param, 5));
        } else {
            $template = '';
        }

        return $template;
    }

    // return the contents of file
    // 
    // this function is based on the code written by Ryan Nutt
    // http://www.nutt.net/2006/07/08/file_get_contents-function-for-php-4/#more-210
    function get_file_contents($filename) {
        if (!function_exists('file_get_contents')) {
            $fhandle = fopen($filename, 'r');
            $fcontents = fread($fhandle, filesize($filename));
            fclose($fhandle);
        } else {
            $fcontents = file_get_contents($filename);
        }

        return $fcontents;
    }

    // set values to the placeholders of the template
    //
    // this function is based on the code written by Mark Kaplan(Ditto developper)
    function setPlaceholders($placeholders, $tpl) {
        $keys = array();
        $values = array();

        foreach ($placeholders as $key => $value) {
            $keys[] = '[+' . $key . '+]';
            $values[] = $value;
        }

        return str_replace($keys, $values, $tpl);
    }

    // set default templates
    function setTemplate($template) {
        global $modx;

        if (trim($template) == '' || $template == 'html') return;

        $filename = $modx->config['base_path'] . $this->get('basePath') . 'templates/' . $template . '.tpl.inc.php';

        if (file_exists($filename)) include($filename);
    }

    // set default style
    function setStyle($style) {
        global $modx;

        if (trim($style) == '' || $style == 'none') return;

        $filename = $this->get('basePath') . 'styles/' . $style . '.css';

        if (file_exists($modx->config['base_path'] . $filename)) $modx->regClientCSS($modx->config['base_url'] . $filename);
    }

    // retrieve documents
    function retrieveDocuments() {
        global $modx;

        // initialize the home of pages and documents
        $this->pages['total'] = 0;
        $this->docs['home'] = null;
        $this->docs['parent'] = null;
        $this->docs['start'] = null;
        $this->docs['first'] = null;
        $this->docs['prev'] = null;
        $this->docs['current'] = null;
        $this->docs['next'] = null;
        $this->docs['last'] = null;
        $this->docs['end'] = null;

        // setting parameters
        $current_id = $modx->documentObject['id'];
        $parent_id = $modx->documentObject['parent'];

        $this->set('parent', $parent_id);
        $home_id = $this->get('home');
        $sortBy = $this->get('sortBy');
        $sortDir = $this->get('sortDir');
        $showInMenuOnly = $this->get('showInMenuOnly');
        $showPublishedOnly = $this->get('showPublishedOnly');
        $hideFolders = $this->get('hideFolders');
        $exclude = $this->get('exclude');

        $fields = 'id,pagetitle,longtitle,alias,isfolder,hidemenu,published,deleted';

        // get children
        $children = $modx->getAllChildren($parent_id, $sortBy, $sortDir, $fields);

        $current_index = null;
        $home_index = null;
        $docs = array();
        $i = 0;

        // get children are based on parameters
        foreach ($children as $child) {
            if (!$child['deleted'] &&
                (!$showInMenuOnly || ($showInMenuOnly && !$child['hidemenu'])) &&
                (!$showPublishedOnly || ($showPublishedOnly && $child['published'])) &&
                (!$hideFolders || ($hideFolders && !$child['isfolder'])) &&
                !in_array($child['id'], $exclude)) {

                $docs[$i] = $child;
                if ($docs[$i]['id'] == $current_id) {
                    $current_index = $i;
                    $this->current = $i + 1;
                }

                if ($docs[$i]['id'] == $home_id) {
                    $home_index = $i;
                }

                $i ++;
            }
        }

        // current page cannot be found
        if ($current_index === null) return false;

        // store the document objects
        $this->total = $i;
        $this->docs['start'] = $docs[0];
        // $this->docs['first'] = ($i >= 3 && $current_index >= 2) ? $docs[0] : null;
        $this->docs['first'] = ($i >= 2 && $current_index >= 1) ? $docs[0] : null;
        $this->docs['prev'] = ($i >= 2 && $current_index >= 1) ? $docs[$current_index - 1] : null;
        $this->docs['next'] = ($i >= 2 && $current_index <= $i - 2) ? $docs[$current_index + 1] : null;
        // $this->docs['last'] = ($i >= 3 && $current_index <= $i - 3) ? $docs[$i - 1] : null;
        $this->docs['last'] = ($i >= 2 && $current_index <= $i - 2) ? $docs[$i - 1] : null;
        $this->docs['end'] = $docs[$i - 1];

        // get parent
        $parent = $this->getDocument($parent_id, $fields);

        if ($parent !== false &&
            !$parent['deleted'] &&
            (!$showInMenuOnly || $showInMenuOnly && !$parent['hidemenu']) &&
            (!$showPublishedOnly || $showPublishedOnly && $parent['published']) &&
            !in_array($parent['id'], $exclude)) {
            $this->docs['parent'] = $parent;
        }

        // get home
        if ($home_index !== null) {  // home page is found in children
            $this->docs['home'] = $docs[$home_index];
        } elseif ($home_id == $parent_id) {  // for optimization
                                              // home page is the same as parent page
            $this->docs['home'] = $parent;
        } else {
            $home = $this->getDocument($home_id, $fields);

            if ($home !== false &&
                !$home['deleted'] &&
                (!$showInMenuOnly || $showInMenuOnly && !$home['hidemenu']) &&
                (!$showPublishedOnly || $showPublishedOnly && $home['published']) &&
                !in_array($home['id'], $exclude)) {
                $this->docs['home'] = $home;
            }
        }

        return true;
    }

    // build output
    function buildOutput() {
        global $modx;

        // get templates excludes outer & inner template
        $tpls = array();
        $tpl_names = array('tplHome', 'tplParent', 'tplFirst', 'tplPrev', 'tplNext', 'tplNext', 'tplLast',
                         'tplHomeOff', 'tplParentOff', 'tplFirstOff', 'tplPrevOff', 'tplNextOff', 'tplLastOff');

        foreach ($tpl_names as $tpl_name) {
            $tpls[$tpl_name] = $this->getTemplate($this->get($tpl_name));
        }

        // set values to the placeholders of page tempaltes
        $ph = array();
        $ph['page.total'] = $this->total;
        $ph['page.current'] = $this->current;

        $page_names = array('parent' => 'tplParent', 'home' => 'tplHome',
                            'first' => 'tplFirst', 'prev' => 'tplPrev',
                            'next' => 'tplNext', 'last' => 'tplLast');

        $titleSource = $this->get('titleSource');

        foreach ($page_names as $name => $tpl) {
            if ($this->docs[$name] !== null) {
                $ph[$name . '.id'] = $this->docs[$name]['id'];
                $ph[$name . '.uri'] = '[~' . $this->docs[$name]['id'] . '~]';
                $ph[$name . '.title'] = $this->docs[$name][$titleSource];
                $ph[$name . '.alias'] = $this->docs[$name]['alias'];
            } else {
                $ph[$name . '.id'] = '';
                $ph[$name . '.uri'] = '';
                $ph[$name . '.title'] = '';
                $ph[$name . '.alias'] = '';
            }
        }

        $ph['start.id'] = $this->docs['start']['id'];
        $ph['start.uri'] = '[~' . $this->docs['start']['id'] . '~]';
        $ph['start.title'] = $this->docs['start'][$titleSource];
        $ph['start.alias'] = $this->docs['start']['alias'];

        $ph['end.id'] = $this->docs['end']['id'];
        $ph['end.uri'] = '[~' . $this->docs['end']['id'] . '~]';
        $ph['end.title'] = $this->docs['end'][$titleSource];
        $ph['end.alias'] = $this->docs['end']['alias'];

        $parent_id = $this->get('parent');
        $home_id = $this->get('home');

        if ($home_id == 0) {
            $ph['home.id'] = 0;
            $ph['home.uri'] = '[(site_url)]';
            $ph['home.title'] = '[(site_name)]';
            $ph['home.alias'] = '';
        }

        if ($parent_id == 0) {
            $ph['parent.id'] = 0;
            $ph['parent.uri'] = '[(site_url)]';
            $ph['parent.title'] = '[(site_name)]';
            $ph['parent.alias'] = '';
        }

        // get page templates which placeholders are replaced with the value
        foreach ($tpls as $name => $tpl) {
            $output[$name] = $this->setPlaceholders($ph, $tpl);
        }



        // set the value to the placeholders of inner template
        foreach ($page_names as $name => $tpl) {
            if ($this->docs[$name] !== null) {
                $ph[$name] = $output[$tpl];
            } else {
                $ph[$name] = $this->get('alwaysShowLinks') ? $output[$tpl . 'Off'] : '';
            }
        }

        if ($home_id == 0) {
            $ph['home'] = $output['tplHome'];
        }

        if ($parent_id == 0) {
            $ph['parent'] = $output['tplParent'];
        }

        // get inner templates which placeholders are replaced with the value
        $tpls['tplInner'] = $this->getTemplate($this->get('tplInner'));
        $output['tplInner'] = $this->setPlaceholders($ph, $tpls['tplInner']);

        // get outer templates which placeholders are replaced with the value
        $ph['inner'] = $output['tplInner'];
        $tpls['tplOuter'] = $this->getTemplate($this->get('tplOuter'));
        $output['tplOuter'] = $this->setPlaceholders($ph, $tpls['tplOuter']);

        // for &alwaysOutput
        if (($alwaysOutput = $this->get('alwaysOutput')) ||
            (!$alwaysOutput && trim($output['tplInner']) != '')) {
            return $output['tplOuter'];
        } else {
            return '';
        }
    }

    // return result
    function render() {
        // check &titleSource
        if ($this->get('titleSource') != 'pagetitle' &&
            $this->get('titleSource') != 'longtitle') {
            $this->set('titleSource', 'pagetitle');
        }

        // set default templates
        $this->setTemplate($template = $this->get('template'));

        // set default style
        if ($template != 'link') $this->setStyle($this->get('style'));

        return $this->retrieveDocuments() ? $this->buildOutput() : '';
    }
}
?>
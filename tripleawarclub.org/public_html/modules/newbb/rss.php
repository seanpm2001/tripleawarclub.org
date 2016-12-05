<?php
// $Id: rss.php 62 2012-08-17 10:15:26Z alfred $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: phppp (D.J., infomax@gmail.com)                                  //
//  URL: http://xoops.org                                                    //
// Project: Article Project                                                 //
// ------------------------------------------------------------------------ //

include_once __DIR__ . '/header.php';
include_once $GLOBALS['xoops']->path('class/template.php');
include_once $GLOBALS['xoops']->path('modules/newbb/include/functions.rpc.php');

if (!empty($GLOBALS['xoopsModuleConfig']['do_rewrite'])) {
    include_once 'seo_url.php';
}
/* for seo */

error_reporting(0);
$xoopsLogger->activated = false;

$forums   = array();
$category = XoopsRequest::getString('c', '', 'GET');
if (XoopsRequest::getString('f', 0, 'GET')) {
    $forums = array_map('intval', array_map('trim', explode('|', XoopsRequest::getString('f', 0, 'GET'))));
    //$forums[] = (int)($_GET["f"]);
}

$forumHandler =& xoops_getmodulehandler('forum', 'newbb');
$topicHandler =& xoops_getmodulehandler('topic', 'newbb');
$validForums  = $forumHandler->getIdsByPermission(); // get all accessible forums

if (is_array($forums) && count($forums) > 0) {
    $validForums = array_intersect($forums, $validForums);
} elseif ($category > 0) {
    $crit_top = new CriteriaCompo(new Criteria('cat_id', $category));
    $crit_top->add(new Criteria('forum_id', '(' . implode(', ', $validForums) . ')', 'IN'));
    $forums_top  = $forumHandler->getIds($crit_top);
    $validForums = array_intersect($forums_top, $validForums);
}
if (count($validForums) === 0) {
    newbb_trackback_response(1, _NOPERM);
}

$charset = 'UTF-8';
header('Content-Type:text/xml; charset=' . $charset);

$tpl                 = new XoopsTpl();
$tpl->caching        = 2;
$tpl->cache_lifetime = $GLOBALS['xoopsModuleConfig']['rss_cachetime'] * 60;
if (!empty($GLOBALS['xoopsConfig']['rewrite'])) {
    $tpl->load_filter('output', 'xoRewriteModule');
}

mod_loadFunctions('cache');
$xoopsCachedTemplateId = md5(mod_generateCacheId_byGroup() . str_replace(XOOPS_URL, '', $_SERVER['REQUEST_URI']));
$compile_id            = null;
if (!$tpl->is_cached('db:newbb_rss.tpl', $xoopsCachedTemplateId, $compile_id)) {
    mod_loadFunctions('time', 'newbb');

    $xmlrss_handler =& xoops_getmodulehandler('xmlrss', 'newbb');
    $rss            = $xmlrss_handler->create();

    $rss->setVarRss('channel_title', $GLOBALS['xoopsConfig']['sitename'] . ' :: ' . _MD_FORUM);
    $rss->channel_link = XOOPS_URL . '/';
    $rss->setVarRss('channel_desc', $GLOBALS['xoopsConfig']['slogan'] . ' :: ' . $xoopsModule->getInfo('description'));
    // There is a "bug" with xoops function formatTimestamp(time(), 'rss')
    // We have to make a customized function
    //$rss->channel_lastbuild = formatTimestamp(time(), 'rss');
    $rss->setVarRss('channel_lastbuild', newbb_formatTimestamp(time(), 'rss'));
    $rss->channel_webmaster = $GLOBALS['xoopsConfig']['adminmail'];
    $rss->channel_editor    = $GLOBALS['xoopsConfig']['adminmail'];
    $rss->setVarRss('channel_category', $xoopsModule->getVar('name'));
    $rss->channel_generator = 'NewBB ' . $xoopsModule->getInfo('version');
    $rss->channel_language  = _LANGCODE;
    $rss->xml_encoding      = $charset;
    $rss->image_url         = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/' . $xoopsModule->getInfo('image');

    $dimension = @getimagesize($GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/' . $xoopsModule->getInfo('image')));
    if (empty($dimension[0])) {
        $width = 88;
    } else {
        $width = ($dimension[0] > 144) ? 144 : $dimension[0];
    }
    if (empty($dimension[1])) {
        $height = 31;
    } else {
        $height = ($dimension[1] > 400) ? 400 : $dimension[1];
    }
    $rss->image_width  = $width;
    $rss->image_height = $height;

    $rss->max_items            = $GLOBALS['xoopsModuleConfig']['rss_maxitems'];
    $rss->max_item_description = $GLOBALS['xoopsModuleConfig']['rss_maxdescription'];

    $forumCriteria = ' AND t.forum_id IN (' . implode(',', $validForums) . ')';
    unset($validForums);
    $approveCriteria = ' AND t.approved = 1 AND p.approved = 1';

    $query = 'SELECT' . '    f.forum_id, f.forum_name,' . '    t.topic_id, t.topic_title, t.type_id,' . '    p.post_id, p.post_time, p.subject, p.uid, p.poster_name, p.post_karma, p.require_reply, ' . '    pt.dohtml, pt.dosmiley, pt.doxcode, pt.dobr,' . '    pt.post_text' . '    FROM ' . $GLOBALS['xoopsDB']->prefix('bb_posts') . ' AS p' . '    LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('bb_topics') . ' AS t ON t.topic_last_post_id=p.post_id' . '    LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('bb_posts_text') . ' AS pt ON pt.post_id=p.post_id' . '    LEFT JOIN ' . $GLOBALS['xoopsDB']->prefix('bb_forums') . ' AS f ON f.forum_id=p.forum_id' . '    WHERE 1=1 ' . $forumCriteria . $approveCriteria . ' ORDER BY p.post_id DESC';
    $limit = (int)($GLOBALS['xoopsModuleConfig']['rss_maxitems'] * 1.5);
    if (!$result = $GLOBALS['xoopsDB']->query($query, $limit)) {
        newbb_trackback_response(1, _MD_ERROR);
        //xoops_error($GLOBALS['xoopsDB']->error());
        //return $xmlrss_handler->get($rss);
    }
    $rows  = array();
    $types = array();
    while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
        $users[$row['uid']] = 1;
        if ($row['type_id'] > 0) {
            $types[$row['type_id']] = 1;
        }
        $rows[] = $row;
    }

    if (count($rows) < 1) {
        newbb_trackback_response(1, _MD_NORSS_DATA);
        //return $xmlrss_handler->get($rss);
    }
    $users = newbb_getUnameFromIds(array_keys($users), $GLOBALS['xoopsModuleConfig']['show_realname']);
    if (count($types) > 0) {
        $typeHandler = &xoops_getmodulehandler('type', 'newbb');
        $type_list   = $typeHandler->getList(new Criteria('type_id', '(' . implode(', ', array_keys($types)) . ')', 'IN'));
    }

    foreach ($rows as $topic) {
        if ($topic['post_karma'] > 0 && $GLOBALS['xoopsModuleConfig']['enable_karma']) {
            continue;
        }
        if ($topic['require_reply'] && $GLOBALS['xoopsModuleConfig']['allow_require_reply']) {
            continue;
        }
        if (!empty($users[$topic['uid']])) {
            $topic['uname'] = $users[$topic['uid']];
        } else {
            $topic['uname'] = ($topic['poster_name']) ? $myts->htmlSpecialChars($topic['poster_name']) : $myts->htmlSpecialChars($GLOBALS['xoopsConfig']['anonymous']);
        }
        $description            = $topic['forum_name'] . '::';
        $topic['topic_subject'] = empty($type_list[$topic['type_id']]) ? '' : '[' . $type_list[$topic['type_id']] . '] ';
        $description .= $topic['topic_subject'] . $topic['topic_title'] . "<br />\n";
        $description .= $myts->displayTarea($topic['post_text'], $topic['dohtml'], $topic['dosmiley'], $topic['doxcode'], $topic['dobr']);
        $label = _MD_BY . ' ' . $topic['uname'];
        $time  = newbb_formatTimestamp($topic['post_time'], 'rss');
        $link  = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewtopic.php?post_id=' . $topic['post_id'] . '';
        if (!empty($GLOBALS['xoopsModuleConfig']['do_rewrite'])) {
            $link   = XOOPS_URL . '/' . REAL_MODULE_NAME . '/viewtopic.php?post_id=' . $topic['post_id'] . '';
            $oldurl = '<a href=\'' . $link . '\'>';
            $newurl = seo_urls($oldurl);
            $newurl = str_replace('<a href=\'', '', $newurl);
            $newurl = str_replace('\'>', '', $newurl);
            $link   = $newurl;
        }
        $title = $topic['subject'];
        if (!$rss->addItem($title, $link, $description, $label, $time)) {
            break;
        }
    }

    $rss_feed = $xmlrss_handler->get($rss);

    $tpl->assign('rss', $rss_feed);
    unset($rss);
}
$tpl->display('db:newbb_rss.tpl', $xoopsCachedTemplateId, $compile_id);

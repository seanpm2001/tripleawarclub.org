<?php
/**
 * NewBB 4.3x, the forum module for XOOPS project
 *
 * @copyright      XOOPS Project (http://xoops.org)
 * @license        http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author         Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since          4.00
 * @version        $Id $
 * @package        module::newbb
 */
include_once __DIR__ . '/header.php';

$forum_id = XoopsRequest::getInt('forum_id', 0, 'POST');
$topic_id = XoopsRequest::getArray('topic_id', null, 'POST');

$op = XoopsRequest::getCmd('op', '', 'POST');
$op = in_array($op, array('approve', 'delete', 'restore', 'move'), true) ? $op : '';

if (0 === count($topic_id) || 0 === count($op)) {
    // irmtfan - issue with javascript:history.go(-1)
    redirect_header($_SERVER['HTTP_REFERER'], 2, _MD_NORIGHTTOACCESS);
}

$topic_id     = array_values($topic_id);
$topicHandler =& xoops_getmodulehandler('topic', 'newbb');
$forumHandler =& xoops_getmodulehandler('forum', 'newbb');

$isadmin = newbb_isAdmin($forum_id);

if (!$isadmin) {
    redirect_header(XOOPS_URL . '/index.php', 2, _MD_NORIGHTTOACCESS);
}
switch ($op) {
    case 'restore':
        $forums     = array();
        $topics_obj =& $topicHandler->getAll(new Criteria('topic_id', '(' . implode(',', $topic_id) . ')', 'IN'));
        foreach (array_keys($topics_obj) as $id) {
            $topic_obj =& $topics_obj[$id];
            $topicHandler->approve($topic_obj);
            $topicHandler->synchronization($topic_obj);
            $forums[$topic_obj->getVar('forum_id')] = 1;
        }
        $criteria_forum = new Criteria('forum_id', '(' . implode(',', array_keys($forums)) . ')', 'IN');
        $forums_obj     =& $forumHandler->getAll($criteria_forum);
        foreach (array_keys($forums_obj) as $id) {
            $forumHandler->synchronization($forums_obj[$id]);
        }
        unset($topics_obj, $forums_obj);
        break;
    case 'approve':
        $forums     = array();
        $topics_obj =& $topicHandler->getAll(new Criteria('topic_id', '(' . implode(',', $topic_id) . ')', 'IN'));
        foreach (array_keys($topics_obj) as $id) {
            $topic_obj =& $topics_obj[$id];
            $topicHandler->approve($topic_obj);
            $topicHandler->synchronization($topic_obj);
            $forums[$topic_obj->getVar('forum_id')] = 1;
        }

        $criteria_forum = new Criteria('forum_id', '(' . implode(',', array_keys($forums)) . ')', 'IN');
        $forums_obj     =& $forumHandler->getAll($criteria_forum);
        foreach (array_keys($forums_obj) as $id) {
            $forumHandler->synchronization($forums_obj[$id]);
        }

        if (empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
            break;
        }

        include_once 'include/notification.inc.php';
        $notificationHandler =& xoops_gethandler('notification');
        foreach (array_keys($topics_obj) as $id) {
            $topic_obj           =& $topics_obj[$id];
            $tags                = array();
            $tags['THREAD_NAME'] = $topic_obj->getVar('topic_title');
            $tags['THREAD_URL']  = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewtopic.php?topic_id=' . $id . '&amp;forum=' . $topic_obj->getVar('forum_id');
            $tags['FORUM_NAME']  = $forums_obj[$topic_obj->getVar('forum_id')]->getVar('forum_name');
            $tags['FORUM_URL']   = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewforum.php?forum=' . $topic_obj->getVar('forum_id');
            $notificationHandler->triggerEvent('global', 0, 'new_thread', $tags);
            $notificationHandler->triggerEvent('forum', $topic_obj->getVar('forum_id'), 'new_thread', $tags);
            $post_obj         =& $topicHandler->getTopPost($id);
            $tags['POST_URL'] = $tags['THREAD_URL'] . '#forumpost' . $post_obj->getVar('post_id');
            $notificationHandler->triggerEvent('thread', $id, 'new_post', $tags);
            $notificationHandler->triggerEvent('forum', $topic_obj->getVar('forum_id'), 'new_post', $tags);
            $notificationHandler->triggerEvent('global', 0, 'new_post', $tags);
            $tags['POST_CONTENT'] = $post_obj->getVar('post_text');
            $tags['POST_NAME']    = $post_obj->getVar('subject');
            $notificationHandler->triggerEvent('global', 0, 'new_fullpost', $tags);
            $notificationHandler->triggerEvent('forum', $topic_obj->getVar('forum_id'), 'new_fullpost', $tags);
            unset($post_obj);
        }
        unset($topics_obj, $forums_obj);
        break;
    case 'delete':
        $forums     = array();
        $topics_obj =& $topicHandler->getAll(new Criteria('topic_id', '(' . implode(',', $topic_id) . ')', 'IN'));
        foreach (array_keys($topics_obj) as $id) {
            $topic_obj =& $topics_obj[$id];
            // irmtfan should be set to false to not delete topic from database
            $topicHandler->delete($topic_obj, false);
            $topicHandler->synchronization($topic_obj);
            $forums[$topic_obj->getVar('forum_id')] = 1;
        }

        $criteria_forum = new Criteria('forum_id', '(' . implode(',', array_keys($forums)) . ')', 'IN');
        $forums_obj     =& $forumHandler->getAll($criteria_forum);
        foreach (array_keys($forums_obj) as $id) {
            $forumHandler->synchronization($forums_obj[$id]);
        }
        unset($topics_obj, $forums_obj);
        break;
    case 'move':
        if (XoopsRequest::getInt('newforum', 0, 'POST') && XoopsRequest::getInt('newforum', 0, 'POST') !== $forum_id && $forumHandler->getPermission(XoopsRequest::getInt('newforum', 0, 'POST'), 'post')) {
            $criteria    = new Criteria('topic_id', '(' . implode(',', $topic_id) . ')', 'IN');
            $postHandler =& xoops_getmodulehandler('post', 'newbb');
            $postHandler->updateAll('forum_id', XoopsRequest::getInt('newforum', 0, 'POST'), $criteria, true);
            $topicHandler->updateAll('forum_id', XoopsRequest::getInt('newforum', 0, 'POST'), $criteria, true);
            $forumHandler->synchronization(XoopsRequest::getInt('newforum', 0, 'POST'));
            $forumHandler->synchronization($forum_id);
        } else {
            include $GLOBALS['xoops']->path('header.php');
            $categoryHandler =& xoops_getmodulehandler('category', 'newbb');
            $categories      = $categoryHandler->getByPermission('access');
            $forums          = $forumHandler->getForumsByCategory(array_keys($categories), 'post', false);

            $box = '<select name="newforum" size="1">';
            if (count($categories) > 0 && count($forums) > 0) {
                foreach (array_keys($forums) as $key) {
                    $box .= "<option value='-1'>[" . $categories[$key]->getVar('cat_title') . ']</option>';
                    foreach ($forums[$key] as $forumid => $_forum) {
                        $box .= "<option value='" . $forumid . "'>-- " . $_forum['title'] . '</option>';
                        if (!isset($_forum['sub'])) {
                            continue;
                        }
                        foreach (array_keys($_forum['sub']) as $fid) {
                            $box .= "<option value='" . $fid . "'>---- " . $_forum['sub'][$fid]['title'] . '</option>';
                        }
                    }
                }
            } else {
                $box .= "<option value='-1'>" . _MD_NOFORUMINDB . '</option>';
            }
            $box .= '</select>';
            unset($forums, $categories);

            echo "<form action='" . XoopsRequest::getString('PHP_SELF', '', 'SERVER') . "' method='post'>";
            echo "<table border='0' cellpadding='1' cellspacing='0' align='center' width='95%'>";
            echo "<tr><td class='bg2'>";
            echo "<table border='0' cellpadding='1' cellspacing='1' width='100%'>";
            echo '<tr><td class="bg3">' . _MD_MOVETOPICTO . '</td><td class="bg1">';
            echo $box;
            echo '</td></tr>';
            echo '<tr class="bg3"><td colspan="2" align="center">';
            echo "<input type='hidden' name='op' value='move' />";
            echo "<input type='hidden' name='forum_id' value='{$forum_id}' />";
            foreach ($topic_id as $id) {
                echo "<input type='hidden' name='topic_id[]' value='" . $id . "' />";
            }
            echo "<input type='submit' name='submit' value='" . _SUBMIT . "' />";
            echo '</td></tr></table></td></tr></table>';
            echo '</form>';
            include $GLOBALS['xoops']->path('footer.php');
            exit();
        }
        break;
}
$statsHandler = &xoops_getmodulehandler('stats', 'newbb');
$statsHandler->reset();
if (empty($forum_id)) {
    redirect_header(XOOPS_URL . '/modules/newbb/list.topic.php', 2, _MD_DBUPDATED);
} else {
    redirect_header(XOOPS_URL . '/modules/newbb/viewforum.php?forum=$forum_id', 2, _MD_DBUPDATED);
}
// irmtfan move to footer.php
include_once __DIR__ . '/footer.php';
include $GLOBALS['xoops']->path('footer.php');

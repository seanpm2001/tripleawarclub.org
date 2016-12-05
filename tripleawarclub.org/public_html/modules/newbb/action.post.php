<?php
/**
 * NewBB 4.3x, the forum module for XOOPS project
 *
 * @copyright    XOOPS Project (http://xoops.org)
 * @license        http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author        Taiwen Jiang (phppp or D.J.) <phppp@users.sourceforge.net>
 * @since        4.00
 * @version        $Id $
 * @package        module::newbb
 */
include_once __DIR__ . '/header.php';

$topic_id = XoopsRequest::getInt('topic_id', 0, 'POST');
$post_id  = XoopsRequest::getArray('post_id', XoopsRequest::getArray('post_id', 0, 'POST'), 'GET');
$uid      = XoopsRequest::getInt('uid', 0, 'GET');

$op   = XoopsRequest::getCmd('op', XoopsRequest::getCmd('op', '', 'POST'), 'GET');
$op   = in_array($op, array('approve', 'delete', 'restore', 'split'), true) ? $op : '';
$mode = XoopsRequest::getInt('mode', 1, 'GET');

if (0 === count($post_id) || 0 === count($op)) {
    // irmtfan - issue with javascript:history.go(-1)
    redirect_header($_SERVER['HTTP_REFERER'], 2, _MD_NORIGHTTOACCESS);
}

$postHandler  =& xoops_getmodulehandler('post', 'newbb');
$topicHandler =& xoops_getmodulehandler('topic', 'newbb');
$forumHandler =& xoops_getmodulehandler('forum', 'newbb');
if (empty($topic_id)) {
    $forum_obj = null;
} else {
    $topic_obj =& $topicHandler->get($topic_id);
    $forum_id  = $topic_obj->getVar('forum_id');
    $forum_obj =& $forumHandler->get($forum_id);
}
$isadmin = newbb_isAdmin($forum_obj);

if (!$isadmin) {
    redirect_header(XOOPS_URL .'/index.php', 2, _MD_NORIGHTTOACCESS);
}

switch ($op) {
    case 'restore':
        $post_id = array_values($post_id);
        sort($post_id);
        $topics = array();
        $forums = array();
        foreach ($post_id as $post) {
            $post_obj =& $postHandler->get($post);
            if ($post_obj->getVar('topic_id') < 1) {
                continue;
            }
            $postHandler->approve($post_obj, true);
            $topics[$post_obj->getVar('topic_id')] = 1;
            $forums[$post_obj->getVar('forum_id')] = 1;
            unset($post_obj);
        }
        foreach (array_keys($topics) as $topic) {
            $topicHandler->synchronization($topic);
        }
        foreach (array_keys($forums) as $forum) {
            $forumHandler->synchronization($forum);
        }
        break;
    case 'approve':
        $post_id = array_values($post_id);
        sort($post_id);
        $topics    = array();
        $forums    = array();
        $criteria  = new Criteria('post_id', '(' . implode(',', $post_id) . ')', 'IN');
        $posts_obj =& $postHandler->getObjects($criteria, true);
        foreach ($post_id as $post) {
            $post_obj =& $posts_obj[$post];
            if (!empty($topic_id) && $topic_id !== $post_obj->getVar('topic_id')) {
                continue;
            }
            $postHandler->approve($post_obj);
            $topics[$post_obj->getVar('topic_id')] = $post;
            $forums[$post_obj->getVar('forum_id')] = 1;
        }
        foreach (array_keys($topics) as $topic) {
            $topicHandler->synchronization($topic);
        }
        foreach (array_keys($forums) as $forum) {
            $forumHandler->synchronization($forum);
        }

        if (empty($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
            break;
        }

        $criteria_topic = new Criteria('topic_id', '(' . implode(',', array_keys($topics)) . ')', 'IN');
        $topic_list     =& $topicHandler->getList($criteria_topic, true);

        $criteria_forum = new Criteria('forum_id', '(' . implode(',', array_keys($forums)) . ')', 'IN');
        $forum_list     =& $forumHandler->getList($criteria_forum);

        include_once 'include/notification.inc.php';
        $notificationHandler =& xoops_gethandler('notification');
        foreach ($post_id as $post) {
            $tags                = array();
            $tags['THREAD_NAME'] = $topic_list[$posts_obj[$post]->getVar('topic_id')];
            $tags['THREAD_URL']  = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewtopic.php?topic_id=' . $posts_obj[$post]->getVar('topic_id') . '&amp;forum=' . $posts_obj[$post]->getVar('forum_id');
            $tags['FORUM_NAME']  = $forum_list[$posts_obj[$post]->getVar('forum_id')];
            $tags['FORUM_URL']   = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/viewforum.php?forum=' . $posts_obj[$post]->getVar('forum_id');
            $tags['POST_URL']    = $tags['THREAD_URL'] . '#forumpost' . $post;
            $notificationHandler->triggerEvent('thread', $posts_obj[$post]->getVar('topic_id'), 'new_post', $tags);
            $notificationHandler->triggerEvent('forum', $posts_obj[$post]->getVar('forum_id'), 'new_post', $tags);
            $notificationHandler->triggerEvent('global', 0, 'new_post', $tags);
            $tags['POST_CONTENT'] = $posts_obj[$post]->getVar('post_text');
            $tags['POST_NAME']    = $posts_obj[$post]->getVar('subject');
            $notificationHandler->triggerEvent('global', 0, 'new_fullpost', $tags);
            $notificationHandler->triggerEvent('forum', $posts_obj[$post]->getVar('forum_id'), 'new_fullpost', $tags);
        }
        break;
    case 'delete':
        $post_id = array_values($post_id);
        rsort($post_id);
        $topics = array();
        $forums = array();
        foreach ($post_id as $post) {
            $post_obj =& $postHandler->get($post);
            if (!empty($topic_id) && $topic_id !== $post_obj->getVar('topic_id')) {
                continue;
            }
            $topics[$post_obj->getVar('topic_id')] = 1;
            $forums[$post_obj->getVar('forum_id')] = 1;
            $postHandler->delete($post_obj, true);
            unset($post_obj);
        }
        foreach (array_keys($topics) as $topic) {
            $topicHandler->synchronization($topic);
        }
        foreach (array_keys($forums) as $forum) {
            $forumHandler->synchronization($forum);
        }
        break;
    case 'split':
        $post_obj =& $postHandler->get($post_id);
        if (0 === count($post_id) || $post_obj->isTopic()) {
            break;
        }
        $topic_id = $post_obj->getVar('topic_id');

        $newtopic =& $topicHandler->create();
        $newtopic->setVar('topic_title', $post_obj->getVar('subject'), true);
        $newtopic->setVar('topic_poster', $post_obj->getVar('uid'), true);
        $newtopic->setVar('forum_id', $post_obj->getVar('forum_id'), true);
        $newtopic->setVar('topic_time', $post_obj->getVar('post_time'), true);
        $newtopic->setVar('poster_name', $post_obj->getVar('poster_name'), true);
        $newtopic->setVar('approved', 1, true);
        $topicHandler->insert($newtopic, true);
        $new_topic_id = $newtopic->getVar('topic_id');

        $pid = $post_obj->getVar('pid');

        $post_obj->setVar('topic_id', $new_topic_id, true);
        $post_obj->setVar('pid', 0, true);
        $postHandler->insert($post_obj);

        /* split a single post */
        if ($mode === 1) {
            $criteria = new CriteriaCompo(new Criteria('topic_id', $topic_id));
            $criteria->add(new Criteria('pid', $post_id));
            $postHandler->updateAll('pid', $pid, $criteria, true);
            /* split a post and its children posts */
        } elseif ($mode === 2) {
            include_once $GLOBALS['xoops']->path('class/xoopstree.php');
            $mytree = new XoopsTree($GLOBALS['xoopsDB']->prefix('bb_posts'), 'post_id', 'pid');
            $posts  = $mytree->getAllChildId($post_id);
            if (count($posts) > 0) {
                $criteria = new Criteria('post_id', '(' . implode(',', $posts) . ')', 'IN');
                $postHandler->updateAll('topic_id', $new_topic_id, $criteria, true);
            }
            /* split a post and all posts coming after */
        } elseif ($mode === 3) {
            $criteria = new CriteriaCompo(new Criteria('topic_id', $topic_id));
            $criteria->add(new Criteria('post_id', $post_id, '>'));
            $postHandler->updateAll('topic_id', $new_topic_id, $criteria, true);

            unset($criteria);
            $criteria = new CriteriaCompo(new Criteria('topic_id', $new_topic_id));
            $criteria->add(new Criteria('post_id', $post_id, '>'));
            $postHandler->identifierName = 'pid';
            $posts                        = $postHandler->getList($criteria);

            unset($criteria);
            $post_update = array();
            foreach ($posts as $postid => $pid) {
                if (!in_array($pid, array_keys($posts))) {
                    $post_update[] = $pid;
                }
                if (!array_key_exists($pid, $posts)) {
                    $post_update2[] = $pid;
                }
            }
            if (count($post_update)) {
                $criteria = new Criteria('post_id', '(' . implode(',', $post_update) . ')', 'IN');
                $postHandler->updateAll('pid', $post_id, $criteria, true);
            }
        }

        $forum_id = $post_obj->getVar('forum_id');
        $topicHandler->synchronization($topic_id);
        $topicHandler->synchronization($new_topic_id);
        $sql    = sprintf('UPDATE %s SET forum_topics = forum_topics+1 WHERE forum_id = %u', $GLOBALS['xoopsDB']->prefix('bb_forums'), $forum_id);
        $result = $GLOBALS['xoopsDB']->queryF($sql);

        break;
}
if (!empty($topic_id)) {
    redirect_header(XOOPS_URL . "/modules/newbb/viewtopic.php?topic_id=$topic_id", 2, _MD_DBUPDATED);
} elseif (!empty($forum_id)) {
    redirect_header(XOOPS_URL . "/modules/newbb/viewforum.php?forum=$forum_id", 2, _MD_DBUPDATED);
} else {
    redirect_header(XOOPS_URL . "/modules/newbb/viewpost.php?uid=$uid", 2, _MD_DBUPDATED);
}
// irmtfan move to footer.php
include_once __DIR__ . '/footer.php';
include $GLOBALS['xoops']->path('footer.php');

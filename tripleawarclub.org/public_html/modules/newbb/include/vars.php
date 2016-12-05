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

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
include_once $GLOBALS['xoops']->path('modules/newbb/include/functions.ini.php');
mod_loadFunctions('session', 'newbb');
// irmtfan include user functions for newbb_getIP
mod_loadFunctions('user', 'newbb');

// NewBB cookie structure
/* NewBB cookie storage
    Long term cookie: (configurable, generally one month)
        LV - Last Visit
        M - Menu mode
        V - View mode
        G - Toggle
    Short term cookie: (same as session life time)
        ST - Stored Topic IDs for mark
        LP - Last Post
        LF - Forum Last view
        LT - Topic Last read
        LVT - Last Visit Temp
*/

/* -- Cookie settings -- */
$forumCookie['domain'] = '';
$forumCookie['path']   = '/';
$forumCookie['secure'] = false;
$forumCookie['expire'] = time() + 3600 * 24 * 30; // one month
$forumCookie['prefix'] = 'newbb_' . ((is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->getVar('uid') : '0IP' . newbb_getIP()); // irmtfan IP for anons - use $GLOBALS["xoopsUser"]

// set LastVisitTemp cookie, which only gets the time from the LastVisit cookie if it does not exist yet
// otherwise, it gets the time from the LastVisitTemp cookie
$last_visit = newbb_getsession('LV');
$last_visit = ($last_visit) ? : newbb_getcookie('LV');
$last_visit = ($last_visit) ? : time();

// update LastVisit cookie.
newbb_setcookie('LV', time(), $forumCookie['expire']); // set cookie life time to one month
newbb_setsession('LV', $last_visit);

// include customized variables
if (is_object($GLOBALS['xoopsModule']) && 'newbb' === $GLOBALS['xoopsModule']->getVar('dirname', 'n')) {
    $GLOBALS['xoopsModuleConfig'] = newbbLoadConfig();
}

newbb_load_object();

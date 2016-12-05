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

defined('NEWBB_FUNCTIONS_INI') || include __DIR__ . '/functions.ini.php';
define('NEWBB_FUNCTIONS_RPC_LOADED', true);

if (!defined('NEWBB_FUNCTIONS_RPC')) {
    define('NEWBB_FUNCTIONS_RPC', 1);

    /**
     * Function to respond to a trackback
     * @param int    $error
     * @param string $error_message
     */
    function newbb_trackback_response($error = 0, $error_message = '')
    {
        $moduleConfig = newbbLoadConfig();

        if (!empty($moduleConfig['rss_utf8'])) {
            $charset       = 'utf-8';
            $error_message = xoops_utf8_encode($error_message);
        } else {
            $charset = _CHARSET;
        }
        header('Content-Type: text/xml; charset="' . $charset . '"');
        if ($error) {
            echo '<?xml version="1.0" encoding="' . $charset . '"?' . ">\n";
            echo '<response>\n';
            echo '<error>1</error>\n';
            echo '<message>$error_message</message>\n';
            echo '</response>';
            exit();
        } else {
            echo '<?xml version="1.0" encoding="' . $charset . '"?' . ">\n";
            echo '<response>\n';
            echo '<error>0</error>\n';
            echo '</response>';
        }
    }
}

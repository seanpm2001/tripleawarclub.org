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
define('NEWBB_FUNCTIONS_RENDER_LOADED', true);

if (!defined('NEWBB_FUNCTIONS_RENDER')) {
    define('NEWBB_FUNCTIONS_RENDER', 1);

    /*
     * Sorry, we have to use the stupid solution unless there is an option in MyTextSanitizer:: htmlspecialchars();
     */
    /**
     * @param $text
     * @return mixed
     */
    function newbb_htmlspecialchars(&$text)
    {
        return preg_replace(array('/&amp;/i', '/&nbsp;/i'), array('&', '&amp;nbsp;'), htmlspecialchars($text));
    }

    /**
     * @param        $text
     * @param  int   $html
     * @param  int   $smiley
     * @param  int   $xcode
     * @param  int   $image
     * @param  int   $br
     * @return mixed
     */
    function &newbb_displayTarea(&$text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        global $myts;

        if ($html !== 1) {
            // html not allowed
            $text = newbb_htmlspecialchars($text);
        }
        $text = $myts->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)
        $text = $myts->makeClickable($text);
        if ($smiley !== 0) {
            // process smiley
            $text = $myts->smiley($text);
        }
        if ($xcode !== 0) {
            // decode xcode
            if ($image !== 0) {
                // image allowed
                $text =& $myts->xoopsCodeDecode($text);
            } else {
                // image not allowed
                $text =& $myts->xoopsCodeDecode($text, 0);
            }
        }
        if ($br !== 0) {
            $text =& $myts->nl2Br($text);
        }
        $text = $myts->codeConv($text, $xcode, $image);    // Ryuji_edit(2003-11-18)

        return $text;
    }

    /**
     * @param $document
     * @return string
     */
    function newbb_html2text($document)
    {
        $text = strip_tags($document);

        return $text;
    }

    /**
     * Display forrum button
     *
     * @param          $link
     * @param          $button
     * @param  string  $alt     alt message
     * @param  boolean $asImage true for image mode; false for text mode
     * @param  string  $extra   extra attribute for the button
     * @return mixed
     * @internal param string $image image/button name, without extension
     */
    function newbb_getButton($link, $button, $alt = '', $asImage = true, $extra = "class='forum_button'")
    {
        $button = "<input type='button' name='{$button}' {$extra} value='{$alt}' onclick='window.location.href={$link}' />";
        if (empty($asImage)) {
            $button = "<a href='{$link}' title='{$alt}' {$extra}>" . newbbDisplayImage($button, $alt, true) . '</a>';
        }

        return $button;
    }

    /**
     * Display forrum images
     *
     * @param  string  $image   image name, without extension
     * @param  string  $alt     alt message
     * @param  boolean $display true for return image anchor; faulse for assign to $xoopsTpl
     * @param  string  $extra   extra attribute for the image
     * @return mixed
     */
    function newbbDisplayImage($image, $alt = '', $display = true, $extra = "class='forum_icon'")
    {
        $iconHandler = newbbGetIconHandler();
        // START hacked by irmtfan
        // to show text links instead of buttons - func_num_args()==2 => only when $image, $alt is set and optional $display not set

        if (func_num_args() == 2) {
            // overall setting
            if (!empty($GLOBALS['xoopsModuleConfig']['display_text_links'])) {
                $display = false;
            }
            // if set for each link => overwrite $display
            if (isset($GLOBALS['xoopsModuleConfig']['display_text_each_link'][$image])) {
                $display = empty($GLOBALS['xoopsModuleConfig']['display_text_each_link'][$image]);
            }
        }
        // END hacked by irmtfan
        if (empty($display)) {
            return $iconHandler->assignImage($image, $alt, $extra);
        } else {
            return $iconHandler->getImage($image, $alt, $extra);
        }
    }

    /**
     * @return NewbbIconHandler
     */
    function newbbGetIconHandler()
    {
        global $xoTheme;
        static $iconHandler;

        if (isset($iconHandler)) {
            return $iconHandler;
        }

        if (!class_exists('NewbbIconHandler')) {
            require_once dirname(__DIR__) . '/class/icon.php';
        }

        $iconHandler           = NewbbIconHandler::instance();
        $iconHandler->template =& $xoTheme->template;
        $iconHandler->init($GLOBALS['xoopsConfig']['language']);

        return $iconHandler;
    }
}

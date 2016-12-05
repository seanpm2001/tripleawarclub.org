<?php
// $Id: admin_permissions.php 69 2012-08-21 19:33:07Z alfred $
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
// Author: XOOPS Foundation                                                  //
// URL: http://xoops.org/                                                //
// Project: XOOPS Project                                                    //
// ------------------------------------------------------------------------- //
include_once __DIR__ . '/admin_header.php';
include_once $GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/class/xoopsformloader.php');
include_once $GLOBALS['xoops']->path('class/xoopsform/grouppermform.php');

/**
 * TODO: synchronize cascade permissions for multi-level
 */

/**
 * Add category navigation to forum casscade structure
 * <ol>Special points:
 *    <li> Use negative values for category IDs to avoid conflict between category and forum
 *    <li> Disabled checkbox for categories to avoid unnecessary permission items for categories in forum permission table
 * </ol>
 *
 * Note: this is a __patchy__ solution. We should have a more extensible and flexible group permission management: not only for data architecture but also for management interface
 */
class NewbbXoopsGroupPermForm extends XoopsGroupPermForm
{
    /**
     * @param        $title
     * @param        $modid
     * @param        $permname
     * @param        $permdesc
     * @param string $url
     */

    public function __construct($title, $modid, $permname, $permdesc, $url = '')
    {
        $this->XoopsGroupPermForm($title, $modid, $permname, $permdesc, $url);
    }

    /**
     * @param        $title
     * @param        $modid
     * @param        $permname
     * @param        $permdesc
     * @param string $url
     */

    /*
    public function newbb_XoopsGroupPermForm($title, $modid, $permname, $permdesc, $url = "")
    {
//        $this->XoopsGroupPermForm($title, $modid, $permname, $permdesc, $url);
        self::__construct($title, $modid, $permname, $permdesc, $url);
    }
*/
    /**
     * @return string
     */
    public function render()
    {
        // load all child ids for javascript codes
        foreach (array_keys($this->_itemTree) as $item_id) {
            $this->_itemTree[$item_id]['allchild'] = array();
            $this->_loadAllChildItemIds($item_id, $this->_itemTree[$item_id]['allchild']);
        }
        $gpermHandler  = &xoops_gethandler('groupperm');
        $memberHandler = &xoops_gethandler('member');
        $glist         =& $memberHandler->getGroupList();
        foreach (array_keys($glist) as $i) {
            // get selected item id(s) for each group
            $selected = $gpermHandler->getItemIds($this->_permName, $i, $this->_modid);
            $ele      = new NewbbXoopsGroupFormCheckBox($glist[$i], 'perms[' . $this->_permName . ']', $i, $selected);
            $ele->setOptionTree($this->_itemTree);
            $this->addElement($ele);
            unset($ele);
        }
        $tray = new XoopsFormElementTray('');
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
        $this->addElement($tray);
        $ret = '<h4>' . $this->getTitle() . '</h4>' . $this->_permDesc . '<br />';
        $ret .= "<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'" . $this->getExtra() . ">\n<table width='100%' class='outer' cellspacing='1' valign='top'>\n";
        $elements = &$this->getElements();
        $hidden   = '';
        foreach (array_keys($elements) as $i) {
            if (!is_object($elements[$i])) {
                $ret .= $elements[$i];
            } elseif (!$elements[$i]->isHidden()) {
                $ret .= "<tr valign='top' align='left'><td class='head'>" . $elements[$i]->getCaption();
                if ($elements[$i]->getDescription() !== '') {
                    $ret .= '<br /><br /><span style="font-weight: normal;">' . $elements[$i]->getDescription() . '</span>';
                }
                $ret .= "</td>\n<td class='even'>\n" . $elements[$i]->render() . "\n</td></tr>\n";
            } else {
                $hidden .= $elements[$i]->render();
            }
        }
        $ret .= "</table>$hidden</form>";
        $ret .= $this->renderValidationJS(true);

        return $ret;
    }
}

/**
 * Class NewbbXoopsGroupFormCheckBox
 */
class NewbbXoopsGroupFormCheckBox extends XoopsGroupFormCheckBox
{
    /**
     * @param      $caption
     * @param      $name
     * @param      $groupId
     * @param null $values
     */
    public function __construct($caption, $name, $groupId, $values = null)
    {
        $this->XoopsGroupFormCheckBox($caption, $name, $groupId, $values);
    }

    /**
     * @param string $tree
     * @param array  $option
     * @param string $prefix
     * @param array  $parentIds
     */
    public function _renderOptionTree(&$tree, $option, $prefix, array $parentIds = array())
    {
        if ($option['id'] > 0) {
            $tree .= $prefix . "<input type=\"checkbox\" name=\"" . $this->getName() . '[groups][' . $this->_groupId . '][' . $option['id'] . "]\" id=\"" . $this->getName() . '[groups][' . $this->_groupId . '][' . $option['id'] . "]\" onclick=\"";
            foreach ($parentIds as $pid) {
                if ($pid <= 0) {
                    continue;
                }
                $parent_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $pid . ']';
                $tree .= "var ele = xoopsGetElementById('" . $parent_ele . "'); if (ele.checked !== true) {ele.checked = this.checked;}";
            }
            foreach ($option['allchild'] as $cid) {
                $child_ele = $this->getName() . '[groups][' . $this->_groupId . '][' . $cid . ']';
                $tree .= "var ele = xoopsGetElementById('" . $child_ele . "'); if (this.checked !== true) {ele.checked = false;}";
            }
            $tree .= '" value="1"';
            if (in_array($option['id'], $this->_value)) {
                $tree .= ' checked="checked"';
            }
            $tree .= ' />' . $option['name'] . "<input type=\"hidden\" name=\"" . $this->getName() . '[parents][' . $option['id'] . "]\" value=\"" . implode(':', $parentIds) . "\" /><input type=\"hidden\" name=\"" . $this->getName() . '[itemname][' . $option['id'] . "]\" value=\"" . htmlspecialchars($option['name']) . "\" /><br />\n";
        } else {
            $tree .= $prefix . $option['name'] . "<input type=\"hidden\" id=\"" . $this->getName() . '[groups][' . $this->_groupId . '][' . $option['id'] . "]\" /><br />\n";
        }
        if (isset($option['children'])) {
            foreach ($option['children'] as $child) {
                if ($option['id'] > 0) {
                    //                  array_push($parentIds, $option['id']);
                    $parentIds[] = $option['id'];
                }
                $this->_renderOptionTree($tree, $this->_optionTree[$child], $prefix . '&nbsp;-', $parentIds);
            }
        }
    }
}

//$action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : "";
$action           = strtolower(XoopsRequest::getCmd('action', ''));
$module_id        = $xoopsModule->getVar('mid');
$newbbpermHandler =& xoops_getmodulehandler('permission', 'newbb');
$perms            = $newbbpermHandler->getValidForumPerms();

switch ($action) {
    case 'template':
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_permissions.php');
        echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_PERM_ACTION . '</legend>';
        $opform    = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', 'get');
        $op_select = new XoopsFormSelect('', 'action');
        $op_select->setExtra('onchange="document.forms.actionform.submit()"');
        $op_select->addOptionArray(array(
                                       'no'       => _SELECT,
                                       'template' => _AM_NEWBB_PERM_TEMPLATE,
                                       'apply'    => _AM_NEWBB_PERM_TEMPLATEAPP,
                                       'default'  => _AM_NEWBB_PERM_SETBYGROUP));
        $opform->addElement($op_select);
        $opform->display();

        $memberHandler =& xoops_gethandler('member');
        $glist         = $memberHandler->getGroupList();
        $elements      = array();
        $perm_template = $newbbpermHandler->getTemplate();
        foreach (array_keys($glist) as $i) {
            $selected = !empty($perm_template[$i]) ? array_keys($perm_template[$i]) : array();
            $ret_ele  = '<tr align="left" valign="top"><td class="head">' . $glist[$i] . '</td>';
            $ret_ele .= '<td class="even">';
            $ret_ele .= '<table class="outer"><tr><td class="odd"><table><tr>';
            $ii         = 0;
            $option_ids = array();
            foreach ($perms as $perm) {
                ++$ii;
                if ($ii % 5 == 0) {
                    $ret_ele .= '</tr><tr>';
                }
                $checked      = in_array('forum_' . $perm, $selected) ? " checked='checked'" : '';
                $option_id    = $perm . '_' . $i;
                $option_ids[] = $option_id;
                $ret_ele .= '<td><input name="perms[' . $i . '][' . 'forum_' . $perm . ']" id="' . $option_id . '" onclick="" value="1" type="checkbox"' . $checked . '>' . constant('_AM_NEWBB_CAN_' . strtoupper($perm)) . '<br></td>';
            }
            $ret_ele .= '</tr></table></td><td class="even">';
            $ret_ele .= _ALL . ' <input id="checkall[' . $i . ']" type="checkbox" value="" onclick="var optionids = new Array(' . implode(', ', $option_ids) . '); xoopsCheckAllElements(optionids, \'checkall[' . $i . ']\')" />';
            $ret_ele .= '</td></tr></table>';
            $ret_ele .= '</td></tr>';
            $elements[] = $ret_ele;
        }
        $tray = new XoopsFormElementTray('');
        $tray->addElement(new XoopsFormHidden('action', 'template_save'));
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
        $ret = '<h4>' . _AM_NEWBB_PERM_TEMPLATE . '</h4>' . _AM_NEWBB_PERM_TEMPLATE_DESC . '<br /><br /><br />';
        $ret .= "<form name='template' id='template' method='post'>\n<table width='100%' class='outer' cellspacing='1'>\n";
        $ret .= implode("\n", $elements);
        $ret .= '<tr align="left" valign="top"><td class="head"></td><td class="even">';
        $ret .= $tray->render();
        $ret .= '</td></tr>';
        $ret .= '</table></form>';
        echo $ret;
        xoops_cp_footer();
        break;

    case 'template_save':
        //        $res = $newbbpermHandler->setTemplate($_POST['perms'], $groupid = 0);
        $res = $newbbpermHandler->setTemplate(XoopsRequest::getArray('perms', '', 'POST'), $groupid = 0);
        if ($res) {
            redirect_header('admin_permissions.php', 2, _AM_NEWBB_PERM_TEMPLATE_CREATED);
        } else {
            redirect_header('admin_permissions.php?action=template', 2, _AM_NEWBB_PERM_TEMPLATE_ERROR);
        }
        break;
    //        exit();

    case 'apply':
        $perm_template = $newbbpermHandler->getTemplate();
        if ($perm_template === null) {
            redirect_header('admin_permissions.php?action=template', 2, _AM_NEWBB_PERM_TEMPLATE);
        }
        xoops_cp_header();
        echo $indexAdmin->addNavigation('admin_permissions.php');
        echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_PERM_ACTION . '</legend>';
        $opform    = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', 'get');
        $op_select = new XoopsFormSelect('', 'action');
        $op_select->setExtra('onchange="document.forms.actionform.submit()"');
        $op_select->addOptionArray(array('no' => _SELECT, 'template' => _AM_NEWBB_PERM_TEMPLATE, 'apply' => _AM_NEWBB_PERM_TEMPLATEAPP));
        $opform->addElement($op_select);
        $opform->display();

        $categoryHandler  =& xoops_getmodulehandler('category', 'newbb');
        $criteriaCategory = new CriteriaCompo(new criteria('1', 1));
        $criteriaCategory->setSort('cat_order');
        $categories = $categoryHandler->getList($criteriaCategory);

        $forumHandler =& xoops_getmodulehandler('forum', 'newbb');
        $forums       = $forumHandler->getTree(array_keys($categories), 0, 'all');
        foreach (array_keys($forums) as $c) {
            $fm_options[-1 * $c - 1000] = ' ';
            $fm_options[-1 * $c]        = '[' . $categories[$c] . ']';
            foreach (array_keys($forums[$c]) as $f) {
                $fm_options[$f] = $forums[$c][$f]['prefix'] . $forums[$c][$f]['forum_name'];
            }
        }
        unset($forums, $categories);

        $fmform    = new XoopsThemeForm(_AM_NEWBB_PERM_TEMPLATEAPP, 'fmform', 'admin_permissions.php', 'post');
        $fm_select = new XoopsFormSelect(_AM_NEWBB_PERM_FORUMS, 'forums', null, 10, true);
        $fm_select->addOptionArray($fm_options);
        $fmform->addElement($fm_select);
        $tray = new XoopsFormElementTray('');
        $tray->addElement(new XoopsFormHidden('action', 'apply_save'));
        $tray->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
        $tray->addElement(new XoopsFormButton('', 'reset', _CANCEL, 'reset'));
        $fmform->addElement($tray);
        $fmform->display();
        xoops_cp_footer();
        break;

    case 'apply_save':
        if (!XoopsRequest::getArray('forums', '', 'POST')) {
            break;
        }
        foreach (XoopsRequest::getArray('forums', '', 'POST') as $forum) {
            if ($forum < 1) {
                continue;
            }
            $newbbpermHandler->applyTemplate($forum, $module_id);
        }
        mod_clearCacheFile('permission', 'newbb');
        redirect_header('admin_permissions.php', 2, _AM_NEWBB_PERM_TEMPLATE_APPLIED);
        break;

    default:
        xoops_cp_header();

        $categoryHandler  =& xoops_getmodulehandler('category', 'newbb');
        $criteriaCategory = new CriteriaCompo(new criteria('1', 1));
        $criteriaCategory->setSort('cat_order');
        $categories = $categoryHandler->getList($criteriaCategory);

        if (0 === count($categories)) {
            redirect_header('admin_cat_manager.php', 2, _AM_NEWBB_CREATENEWCATEGORY);
        }

        $forumHandler =& xoops_getmodulehandler('forum', 'newbb');
        $forums       = $forumHandler->getTree(array_keys($categories), 0, 'all');

        if (0 === count($forums)) {
            redirect_header('admin_forum_manager.php', 2, _AM_NEWBB_CREATENEWFORUM);
        }

        echo $indexAdmin->addNavigation('admin_permissions.php');
        echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_PERM_ACTION . '</legend>';
        $opform    = new XoopsSimpleForm(_AM_NEWBB_PERM_ACTION, 'actionform', 'admin_permissions.php', 'get');
        $op_select = new XoopsFormSelect('', 'action');
        $op_select->setExtra('onchange="document.forms.actionform.submit()"');
        $op_select->addOptionArray(array(
                                       'no'       => _SELECT,
                                       'template' => _AM_NEWBB_PERM_TEMPLATE,
                                       'apply'    => _AM_NEWBB_PERM_TEMPLATEAPP,
                                       'default'  => _AM_NEWBB_PERM_SETBYGROUP));
        $opform->addElement($op_select);
        $opform->display();

        $op_options = array('category' => _AM_NEWBB_CAT_ACCESS);
        $fm_options = array('category' => array('title' => _AM_NEWBB_CAT_ACCESS, 'item' => 'category_access', 'desc' => '', 'anonymous' => true));
        foreach ($perms as $perm) {
            $op_options[$perm] = constant('_AM_NEWBB_CAN_' . strtoupper($perm));
            $fm_options[$perm] = array('title' => constant('_AM_NEWBB_CAN_' . strtoupper($perm)), 'item' => 'forum_' . $perm, 'desc' => '', 'anonymous' => true);
        }

        $op_keys = array_keys($op_options);
        $op      = strtolower(XoopsRequest::getCmd('op', XoopsRequest::getCmd('op', '', 'COOKIE'), 'GET'));
        if (empty($op)) {
            $op = $op_keys[0];
            setcookie('op', isset($op_keys[1]) ? $op_keys[1] : '');
        } elseif (false !== ($key = array_search($op, $op_keys))) {
            setcookie('op', isset($op_keys[$key + 1]) ? $op_keys[$key + 1] : '');
        }

        $opform    = new XoopsSimpleForm('', 'opform', 'admin_permissions.php', 'get');
        $op_select = new XoopsFormSelect('', 'op', $op);
        $op_select->setExtra('onchange="document.forms.opform.submit()"');
        $op_select->addOptionArray($op_options);
        $opform->addElement($op_select);
        $opform->display();

        $perm_desc = '';

        $form = new NewbbXoopsGroupPermForm($fm_options[$op]['title'], $module_id, $fm_options[$op]['item'], $fm_options[$op]['desc'], 'admin/admin_permissions.php', $fm_options[$op]['anonymous']);

        $categoryHandler  = &xoops_getmodulehandler('category', 'newbb');
        $criteriaCategory = new CriteriaCompo(new criteria('1', 1));
        $criteriaCategory->setSort('cat_order');
        $categories = $categoryHandler->getList($criteriaCategory);
        if ($op === 'category') {
            foreach (array_keys($categories) as $key) {
                $form->addItem($key, $categories[$key]);
            }
            unset($categories);
        } else {
            $forumHandler = &xoops_getmodulehandler('forum', 'newbb');
            $forums       = $forumHandler->getTree(array_keys($categories), 0, 'all');
            if (count($forums) > 0) {
                foreach (array_keys($forums) as $c) {
                    $key_c = -1 * $c;
                    $form->addItem($key_c, '<strong>[' . $categories[$c] . ']</strong>');
                    foreach (array_keys($forums[$c]) as $f) {
                        $pid = $forums[$c][$f]['parent_forum'] ?: $key_c;
                        $form->addItem($f, $forums[$c][$f]['prefix'] . $forums[$c][$f]['forum_name'], $pid);
                    }
                }
            }
            unset($forums, $categories);
        }
        $form->display();

        // Since we can not control the permission update, a trick is used here
        $permissionHandler =& xoops_getmodulehandler('permission', 'newbb');
        $permissionHandler->createPermData();
        mod_clearCacheFile('permission', 'newbb');
        xoops_cp_footer();
        break;
}

<?php
// $Id: admin_cat_manager.php 62 2012-08-17 10:15:26Z alfred $
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System                      //
// Copyright (c) 2000 XOOPS.org                           //
// <http://xoops.org/>                             //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// //
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// //
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// //
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://xoops.org/, http://jp.xoops.org/ //
// Project: XOOPS Project                                                    //
// ------------------------------------------------------------------------- //
include_once __DIR__ . '/admin_header.php';
mod_loadFunctions('render', 'newbb');
xoops_cp_header();
echo '<fieldset>';
$op     = XoopsRequest::getCmd('op', XoopsRequest::getCmd('op', '', 'POST'), 'GET'); //!empty($_GET['op'])? $_GET['op'] : (!empty($_POST['op'])?$_POST['op']:"");
$cat_id = XoopsRequest::getInt('cat_id', XoopsRequest::getInt('cat_id', 0, 'POST'), 'GET'); // (int)( !empty($_GET['cat_id']) ? $_GET['cat_id'] : @$_POST['cat_id'] );

$categoryHandler =& xoops_getmodulehandler('category', 'newbb');

/**
 * newCategory()
 *
 */
function newCategory()
{
    editCategory();
}

/**
 * editCategory()
 *
 * @param null|XoopsObject $category_obj
 * @internal param int $catid
 */
function editCategory(XoopsObject $category_obj = null)
{
    global $xoopsModule;
    $categoryHandler = &xoops_getmodulehandler('category', 'newbb');
    if (null === $category_obj) {
        $category_obj =& $categoryHandler->create();
    }
    $groups_cat_access = null;
    include_once $GLOBALS['xoops']->path('modules/' . $xoopsModule->getVar('dirname') . '/class/xoopsformloader.php');

    if (!$category_obj->isNew()) {
        $sform = new XoopsThemeForm(_AM_NEWBB_EDITCATEGORY . ' ' . $category_obj->getVar('cat_title'), 'op', xoops_getenv('PHP_SELF'));
    } else {
        $sform = new XoopsThemeForm(_AM_NEWBB_CREATENEWCATEGORY, 'op', xoops_getenv('PHP_SELF'));
        $category_obj->setVar('cat_title', '');
        $category_obj->setVar('cat_image', '');
        $category_obj->setVar('cat_description', '');
        $category_obj->setVar('cat_order', 0);
        $category_obj->setVar('cat_url', 'http://xoops.org/modules/newbb/ newBB Support');
    }

    $sform->addElement(new XoopsFormText(_AM_NEWBB_SETCATEGORYORDER, 'cat_order', 5, 10, $category_obj->getVar('cat_order')), false);
    $sform->addElement(new XoopsFormText(_AM_NEWBB_CATEGORY, 'title', 50, 80, $category_obj->getVar('cat_title', 'E')), true);
    $sform->addElement(new XoopsFormDhtmlTextArea(_AM_NEWBB_CATEGORYDESC, 'cat_description', $category_obj->getVar('cat_description', 'E'), 10, 60), false);

    $imgdir      = '/modules/' . $xoopsModule->getVar('dirname') . '/assets/images/category';
    $cat_image   = $category_obj->getVar('cat_image');
    $cat_image   = empty($cat_image) ? 'blank.gif' : $cat_image;
    $graph_array =& XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . $imgdir . '/');
    array_unshift($graph_array, _NONE);
    $cat_image_select = new XoopsFormSelect('', 'cat_image', $category_obj->getVar('cat_image'));
    $cat_image_select->addOptionArray($graph_array);
    $cat_image_select->setExtra("onchange=\"showImgSelected('img', 'cat_image', '/" . $imgdir . "/', '', '" . XOOPS_URL . "')\"");
    $cat_image_tray = new XoopsFormElementTray(_AM_NEWBB_IMAGE, '&nbsp;');
    $cat_image_tray->addElement($cat_image_select);
    $cat_image_tray->addElement(new XoopsFormLabel('', "<br /><img src='" . XOOPS_URL . $imgdir . '/' . $cat_image . " 'name='img' id='img' alt='' />"));
    $sform->addElement($cat_image_tray);

    $sform->addElement(new XoopsFormText(_AM_NEWBB_SPONSORLINK, 'cat_url', 50, 80, $category_obj->getVar('cat_url', 'E')), false);
    $sform->addElement(new XoopsFormHidden('cat_id', $category_obj->getVar('cat_id')));

    $button_tray = new XoopsFormElementTray('', '');
    $button_tray->addElement(new XoopsFormHidden('op', 'save'));

    $butt_save = new XoopsFormButton('', '', _SUBMIT, 'submit');
    $butt_save->setExtra('onclick="this.form.elements.op.value=\'save\'"');
    $button_tray->addElement($butt_save);
    if ($category_obj->getVar('cat_id')) {
        $butt_delete = new XoopsFormButton('', '', _CANCEL, 'submit');
        $butt_delete->setExtra('onclick="this.form.elements.op.value=\'default\'"');
        $button_tray->addElement($butt_delete);
    }
    $sform->addElement($button_tray);
    $sform->display();
}

switch ($op) {
    case 'mod':
        $category_obj = ($cat_id > 0) ? $categoryHandler->get($cat_id) : $categoryHandler->create();
        if (!$newXoopsModuleGui) {
            //loadModuleAdminMenu(1, ( $cat_id > 0) ? _AM_NEWBB_EDITCATEGORY . $category_obj->getVar('cat_title') : _AM_NEWBB_CREATENEWCATEGORY);
            echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_EDITCATEGORY . '</legend>';
        } else {
            echo $indexAdmin->addNavigation('admin_cat_manager.php');
        }
        echo '<br />';
        editCategory($category_obj);
        break;

    case 'del':
        if (!(XoopsRequest::getBool('confirm', '', 'POST'))) {
            xoops_confirm(array('op' => 'del', 'cat_id' => XoopsRequest::getInt('cat_id', 0, 'GET'), 'confirm' => 1), 'admin_cat_manager.php', _AM_NEWBB_WAYSYWTDTTAL);
            break;
        } else {
            $category_obj =& $categoryHandler->create(false);
            $category_obj->setVar('cat_id', XoopsRequest::getInt('cat_id', 0, 'POST'));
            $categoryHandler->delete($category_obj);

            redirect_header('admin_cat_manager.php', 2, _AM_NEWBB_CATEGORYDELETED);
        }
        break;

    case 'save':
        mod_clearCacheFile('permission_category', 'newbb');
        if ($cat_id) {
            $category_obj =& $categoryHandler->get($cat_id);
            $message      = _AM_NEWBB_CATEGORYUPDATED;
        } else {
            $category_obj =& $categoryHandler->create();
            $message      = _AM_NEWBB_CATEGORYCREATED;
        }

        $category_obj->setVar('cat_title', XoopsRequest::getString('title', '', 'POST'));
        $category_obj->setVar('cat_image', XoopsRequest::getString('cat_image', '', 'POST'));
        $category_obj->setVar('cat_order', XoopsRequest::getInt('cat_order', 0, 'POST'));
        $category_obj->setVar('cat_description', XoopsRequest::getText('cat_description', '', 'POST'));
        $category_obj->setVar('cat_url', XoopsRequest::getString('cat_url', '', 'POST'));

        $cat_isNew = $category_obj->isNew();
        if (!$categoryHandler->insert($category_obj)) {
            $message = _AM_NEWBB_DATABASEERROR;
        }
        if ($cat_id = $category_obj->getVar('cat_id') && $cat_isNew) {
            $categoryHandler->applyPermissionTemplate($category_obj);
        }
        redirect_header('admin_cat_manager.php', 2, $message);
        break;

    default:
        if (!$categories = $categoryHandler->getByPermission('all')) {
            if (!$newXoopsModuleGui) {
                //loadModuleAdminMenu(1, _AM_NEWBB_CREATENEWCATEGORY);
                echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_CREATENEWCATEGORY . '</legend>';
            } else {
                $indexAdmin->addItemButton(_AM_NEWBB_CREATENEWCATEGORY, 'admin_cat_manager.php?op=mod', $icon = 'add');
                echo $indexAdmin->renderButton();
            }
            echo '<fieldset>';
            echo '<br />';
            newCategory();
            echo '</fieldset>';
            break;
        }

        if (!$newXoopsModuleGui) {
            //loadModuleAdminMenu(1, _AM_NEWBB_CATADMIN);
            echo '<fieldset>';
            echo "<legend style='font-weight: bold; color: #900;'>" . _AM_NEWBB_CATADMIN . '</legend>';
            echo '<br />';
            echo "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='admin_cat_manager.php?op=mod'>" . _AM_NEWBB_CREATENEWCATEGORY . '</a><br /><br />';
        } else {
            echo $indexAdmin->addNavigation('admin_cat_manager.php');
            echo '<fieldset>';
            $indexAdmin->addItemButton(_AM_NEWBB_CREATENEWCATEGORY, 'admin_cat_manager.php?op=mod', $icon = 'add');
            echo $indexAdmin->renderButton();
        }
        echo "<table border='0' cellpadding='4' cellspacing='1' width='100%' class='outer'>";
        echo "<tr align='center'>";
        echo "<th align='left' class='bg3'>" . _AM_NEWBB_CATEGORY1 . '</th>';
        echo "<th class='bg3' width='10%'>" . _AM_NEWBB_EDIT . '</th>';
        echo "<th class='bg3' width='10%'>" . _AM_NEWBB_DELETE . '</th>';
        echo '</tr>';

        foreach ($categories as $key => $onecat) {
            $cat_edit_link  = "<a href=\"admin_cat_manager.php?op=mod&cat_id=" . $onecat->getVar('cat_id') . "\">" . newbbDisplayImage('admin_edit', _EDIT) . '</a>';
            $cat_del_link   = "<a href=\"admin_cat_manager.php?op=del&cat_id=" . $onecat->getVar('cat_id') . "\">" . newbbDisplayImage('admin_delete', _DELETE) . '</a>';
            $cat_title_link = "<a href=\"" . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?cat=' . $onecat->getVar('cat_id') . "\">" . $onecat->getVar('cat_title') . '</a>';

            echo "<tr class='odd' align='left'>";
            echo '<td>' . $cat_title_link . '</td>';
            echo "<td align='center'>" . $cat_edit_link . '</td>';
            echo "<td align='center'>" . $cat_del_link . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</fieldset>';
        break;
}
mod_clearCacheFile('permission_category', 'newbb');
echo '</fieldset>';
xoops_cp_footer();

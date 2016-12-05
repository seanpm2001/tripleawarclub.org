<?php
// $Id: rate.php 62 2012-08-17 10:15:26Z alfred $
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
//  Author: phppp (D.J., infomax@gmail.com)                                  //
//  URL: http://xoops.org                                                    //
//  Project: Article Project                                                 //
//  ------------------------------------------------------------------------ //

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

defined('NEWBB_FUNCTIONS_INI') || include $GLOBALS['xoops']->path('modules/newbb/include/functions.ini.php');
newbb_load_object();

/**
 * Class Nrate
 */
class Nrate extends ArtObject
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('bb_votedata');
        $this->initVar('ratingid', XOBJ_DTYPE_INT);
        $this->initVar('topic_id', XOBJ_DTYPE_INT);
        $this->initVar('ratinguser', XOBJ_DTYPE_INT);
        $this->initVar('rating', XOBJ_DTYPE_INT);
        $this->initVar('ratingtimestamp', XOBJ_DTYPE_INT);
        $this->initVar('ratinghostname', XOBJ_DTYPE_TXTBOX);
    }
}

/**
 * Class NewbbRateHandler
 */
class NewbbRateHandler extends ArtObjectHandler
{
    /**
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'bb_votedata', 'Nrate', 'ratingid', '');
    }

    public function synchronization()
    {
//        return;
    }

    /**
     * clean orphan items from database
     *
     * @return bool true on success
     */
    public function cleanOrphan()
    {
        return parent::cleanOrphan($this->db->prefix('bb_topics'), 'topic_id');
    }
}

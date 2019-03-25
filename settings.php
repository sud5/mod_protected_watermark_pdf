<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Protectedpdf module admin settings and defaults
 *
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");

    $displayoptions = resourcelib_get_displayoptions(array(RESOURCELIB_DISPLAY_AUTO,
                                                           RESOURCELIB_DISPLAY_EMBED,
                                                           RESOURCELIB_DISPLAY_FRAME,
                                                           RESOURCELIB_DISPLAY_DOWNLOAD,
                                                           RESOURCELIB_DISPLAY_OPEN,
                                                           RESOURCELIB_DISPLAY_NEW,
                                                           RESOURCELIB_DISPLAY_POPUP,
                                                          ));
    $defaultdisplayoptions = array(RESOURCELIB_DISPLAY_AUTO,
                                   RESOURCELIB_DISPLAY_EMBED,
                                   RESOURCELIB_DISPLAY_DOWNLOAD,
                                   RESOURCELIB_DISPLAY_OPEN,
                                   RESOURCELIB_DISPLAY_POPUP,
                                  );

    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('protectedpdf/framesize',
        get_string('framesize', 'protectedpdf'), get_string('configframesize', 'protectedpdf'), 130, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('protectedpdf/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));
    $settings->add(new admin_setting_configmultiselect('protectedpdf/displayoptions',
        get_string('displayoptions', 'protectedpdf'), get_string('configdisplayoptions', 'protectedpdf'),
        $defaultdisplayoptions, $displayoptions));

    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('protectedpdfmodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox('protectedpdf/printintro',
        get_string('printintro', 'protectedpdf'), get_string('printintroexplain', 'protectedpdf'), 1));
    $settings->add(new admin_setting_configselect('protectedpdf/display',
        get_string('displayselect', 'protectedpdf'), get_string('displayselectexplain', 'protectedpdf'), RESOURCELIB_DISPLAY_AUTO,
        $displayoptions));
    $settings->add(new admin_setting_configcheckbox('protectedpdf/showsize',
        get_string('showsize', 'protectedpdf'), get_string('showsize_desc', 'protectedpdf'), 0));
    $settings->add(new admin_setting_configcheckbox('protectedpdf/showtype',
        get_string('showtype', 'protectedpdf'), get_string('showtype_desc', 'protectedpdf'), 0));
    $settings->add(new admin_setting_configtext('protectedpdf/popupwidth',
        get_string('popupwidth', 'protectedpdf'), get_string('popupwidthexplain', 'protectedpdf'), 620, PARAM_INT, 7));
    $settings->add(new admin_setting_configtext('protectedpdf/popupheight',
        get_string('popupheight', 'protectedpdf'), get_string('popupheightexplain', 'protectedpdf'), 450, PARAM_INT, 7));
    $options = array('0' => get_string('none'), '1' => get_string('allfiles'), '2' => get_string('htmlfilesonly'));
    $settings->add(new admin_setting_configselect('protectedpdf/filterfiles',
        get_string('filterfiles', 'protectedpdf'), get_string('filterfilesexplain', 'protectedpdf'), 0, $options));
}

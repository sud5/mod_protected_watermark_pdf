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
 * Protectedpdf module version information
 *
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//require('../../config.php');
require('../../config.php');
require_once($CFG->dirroot . '/mod/protectedpdf/lib.php');
require_once($CFG->dirroot . '/mod/protectedpdf/locallib.php');
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID
$r = optional_param('r', 0, PARAM_INT);  // Resource instance ID
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($r) {
    if (!$protectedpdf = $DB->get_record('protectedpdf', array('id' => $r))) {
        protectedpdf_redirect_if_migrated($r, 0);
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('protectedpdf', $protectedpdf->id, $protectedpdf->course, false, MUST_EXIST);
} else {
    if (!$cm = get_coursemodule_from_id('protectedpdf', $id)) {
        protectedpdf_redirect_if_migrated(0, $id);
        print_error('invalidcoursemodule');
    }
    $protectedpdf = $DB->get_record('protectedpdf', array('id' => $cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/protectedpdf:view', $context);

// Completion and trigger events.
protectedpdf_view($protectedpdf, $course, $cm, $context);

$PAGE->set_url('/mod/protectedpdf/view.php', array('id' => $cm->id));

ob_start();
//require_once(__DIR__ . "/../../config.php");
defined('MOODLE_INTERNAL') || die();
ini_set('memory_limit', '512M');
require_once("$CFG->dirroot/lib/filelib.php");
$id = optional_param('id', 0, PARAM_INT);
//$download = optional_param('download', 0, PARAM_INT);
require_once('library/SetaPDF/Autoload.php');

//$filename = 'TPR.pdf';
function password_and_watermarking($id) {
//$text = 'TOP SECRET';
    $opacity = 0.5;
    $fontColor = array(15 / 255, 0 / 255, 0 / 255);
    $outlineColor = array(255 / 255, 0 / 255, 0 / 255);
//$encryption = true;
    $permission = $permission = SetaPDF_Core_SecHandler::PERM_ANNOT;
    global $CFG, $DB, $USER;
    $cm = get_coursemodule_from_id('protectedpdf', $id, 0, true, MUST_EXIST);
    $profile_field_record = $DB->get_record('user_info_field', array('shortname' => 'empid'), 'id,shortname');
    if (!empty($profile_field_record)) {
        $profile_field_id = $profile_field_record->id;
        $employee_record = $DB->get_record('user_info_data', array('userid' => $USER->id, 'fieldid' => $profile_field_id), 'id,data');
    }
    if (!empty($employee_record)) {
        $employee_id = $employee_record->data;
    } else {
        $employee_id = '';
    }

    $user = $DB->get_record('user', array('id' => $USER->id), 'email', MUST_EXIST);
    $watermarkingcontenttable = $DB->get_record('protectedpdf', array('id' => $cm->instance), 'contentwatermarking,applywatermark');
    $watermarkingcontents = $watermarkingcontenttable->contentwatermarking;

//adding the email address on watermarking content
        if($watermarkingcontenttable->applywatermark == 1 || is_null($watermarkingcontenttable->applywatermark)){
        if (empty($watermarkingcontents)) { //for default string
            $watermarkingcontent = 'This individual at ' . $user->email . ' is granted a single-user, non-exclusive license to use this book';
        } else if (strpos($watermarkingcontents, '%email%') > -1) {
            $strpos = strpos($watermarkingcontents, '%email%');
            $watermarkingcontent = substr($watermarkingcontents, 0, $strpos) . $user->email . substr($watermarkingcontents, $strpos + 7);
        } else {
            $watermarkingcontent = $watermarkingcontents;
        }

//adding the employee id on watermarking content
        if (strpos($watermarkingcontent, '%empid%') > -1) {
            $strpos = strpos($watermarkingcontent, '%empid%');
            $watermarkingcontentfinal = substr($watermarkingcontent, 0, $strpos) . $employee_id . substr($watermarkingcontent, $strpos + 7);
        } else {
            $watermarkingcontentfinal = $watermarkingcontent;
        }
    } else {
        $watermarkingcontentfinal = "";
    }
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    require_course_login($course, true, $cm);
    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $file) {
        $file->copy_content_to($CFG->dataroot . '/temp/' . $file->get_filename());
        chmod($CFG->dataroot . '/temp/' . $file->get_filename(), 0777);
        $filename = $CFG->dataroot . '/temp/' . $file->get_filename();
    }

    $user = $DB->get_record('user', array('id' => $USER->id), 'email', MUST_EXIST);
    $setpassword = $DB->get_record('protectedpdf', array('id' => $cm->instance), 'setpassword,password,allowprinting,allowcomments', MUST_EXIST);

//To allow the user to print the document
    if ($setpassword->allowprinting == 1) {
        $permission = 36;
    }
    if ($setpassword->allowcomments == 1) {
        $permission = 2100;
    }
    if (!empty($setpassword->password)) {
        $userpassword = "$setpassword->password";
    } else {
        $userpassword = "$user->email";
    }
    $ownerpassword = "manju";
    $finalfile = basename($filename);

    $writer = new SetaPDF_Core_Writer_Http("$finalfile", true);
// get a document instance
    $document = SetaPDF_Core_Document::loadByFilename(
                    "$filename", $writer
    );

    if ($setpassword->setpassword == 1) {
        // create a security handler and attach it to the document instance
        $secHandler = SetaPDF_Core_SecHandler_Standard_Aes128::factory(
                        $document, $ownerpassword, $userpassword, $permission
        );
        $document->setSecHandler($secHandler);
    } else {
        $secHandler = SetaPDF_Core_SecHandler_Standard_Aes128::factory(
                        $document, null, null, $permission
        );
        $document->setSecHandler($secHandler);
    }

// create a stamper instance
    $stamper = new SetaPDF_Stamper($document);

// create a font object
    $font = SetaPDF_Core_Font_Standard_HelveticaBold::create($document);

// create simple text stamp
    $stamp = new SetaPDF_Stamper_Stamp_Text($font, 10);
    $stamp->setText($watermarkingcontentfinal);
    $stamp->setTextColor(new SetaPDF_Core_DataStructure_Color_Rgb(255 / 255, 0, 0));
    $stamp->setAlign(SetaPDF_Core_Text::ALIGN_CENTER);
    $stamp->setOpacity(0.5);

// right bottom and callback
    $stamper->addStamp($stamp, array(
        'position' => SetaPDF_Stamper::POSITION_CENTER_TOP,
        'rotation' => 0
    ));

// stamp the document
    $stamper->stamp();

// Show the the while page at a time
    $document->getCatalog()->setPageLayout(SetaPDF_Core_Document_PageLayout::SINGLE_PAGE);

// save and send it to the client
    $document->save()->finish();

    ob_end_flush();
}

//if ($download) {
password_and_watermarking($id);
//}
?>

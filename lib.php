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
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in Protectedpdf module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function protectedpdf_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS: return false;
        case FEATURE_GROUPINGS: return false;
        case FEATURE_MOD_INTRO: return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_GRADE_OUTCOMES: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;

        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function protectedpdf_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function protectedpdf_reset_userdata($data) {
    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function protectedpdf_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function protectedpdf_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add protectedpdf instance.
 * @param object $data
 * @param object $mform
 * @return int new protectedpdf instance id
 */
function protectedpdf_add_instance($data, $mform) {
    $section = optional_param('section', 0, PARAM_INT);
    global $CFG, $DB, $COURSE;
    //var_dump($COURSE->id);
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/mod/protectedpdf/locallib.php");
    $cmid = $data->coursemodule;
//print_object($cmid);die;
    if (empty($data->contentwatermarking)) {
        $data->contentwatermarking = "";
    }
    $data->timemodified = time();

    //protectedpdf_set_display_options($data);

    $data->id = $DB->insert_record('protectedpdf', $data);
    //  print_object($data);die;
    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id' => $cmid));
    protectedpdf_set_mainfile($data);
    // print_object($section);die;
//    $moodlegroups = $DB->get_records('course_sections', array('course' => $COURSE->id, 'section' => $section), null, 'id,availability');
//    $course = $DB->get_record('course', array('id' => $COURSE->id), 'id,fullname');
    // $moodlegroups = $DB->get_records('course_modules',array('id' => $cmid),null,'id,availability');
//    $groupid = array();
//    $time = array();
//    foreach ($moodlegroups as $moodlegroupkey => $moodlegroupvalue) {
//        if (!empty($moodlegroupvalue->availability)) {
////            print_object(json_decode($moodlegroupvalue->availability));die;
//            foreach (json_decode($moodlegroupvalue->availability)->c as $key1 => $value) {
//                if (($value->type == 'group' ) && !empty($value->id)) {
//                    $groupid[] = $value->id;
//                } else if (($value->type == 'date' ) && !empty($value->t) && !empty($value->d)) {
//                    if ($value->d == ">=") {
//                        $time['starttime'] = userdate($value->t,'%d-%m-%y/%H:%M %p');
//                    } elseif ($value->d == "<") {
//                        $time['endtime'] = date("d-m-Y/H:i:s", $value->t);
//                    }
//                }
//            }
//        }
//    }
////    print_object($time);die;
//    $emailidallgroup = array();
//    for ($i = 0; $i < count($groupid); $i++) {
//        $emailidallgroup[] = groups_get_members($groupid[$i], $fields = 'u.id,email', $sort = 'lastname ASC');
//    }
//    $emailid = array();
//    foreach ($emailidallgroup as $emailidallgroupkey => $emailidallgroupvalue) {
//        foreach ($emailidallgroupvalue as $emailidkey => $emailidvalue) {
//            $emailid[] = $emailidvalue->id;
//        }
//    }
//
//    if ($data->notifyusers) {
//        $emailid = array_unique($emailid);
//        $support = \core_user::get_support_user();
//        $sectionparam = array('id' => $COURSE->id);
//        $sectionurl = new moodle_url('/course/view.php', $sectionparam).'#section-'. $section;
//        $subject = get_string('mailsubject', 'protectedpdf') . $course->fullname . ' ' . $time['starttime'];
//        for ($i = 0; $i < count($emailid); $i++) {
//            $user = $DB->get_record('user', array('id' => $emailid[$i]));
//            $body = get_string('hello', 'protectedpdf') . $user->firstname . ",\n\n" . substr(get_string('text', 'protectedpdf'), 0, 39) . $course->fullname . '. ' . substr(get_string('text', 'protectedpdf'), 38,17) . $sectionurl .substr(get_string('text', 'protectedpdf'), 54) . ' ' .$time['starttime'] . "\n\n" . get_string('thanksmessage', 'protectedpdf') . "\n\n" . get_string('remark', 'protectedpdf') . "\n" . $support->firstname . "\n" . $CFG->supportpage;
//            $flag = email_to_user($user, $support, $subject, $body);
//        }
//    }
    return $data->id;
}

/**
 * Update protectedpdf instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function protectedpdf_update_instance($data, $mform) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");
    $data->timemodified = time();
    $data->id = $data->instance;
    $data->revision++;

    // protectedpdf_set_display_options($data);

    $DB->update_record('protectedpdf', $data);
    protectedpdf_set_mainfile($data);
    return true;
}

/**
 * Updates display options based on form input.
 *
 * Shared code used by protectedpdf_add_instance and protectedpdf_update_instance.
 *
 * @param object $data Data object
 */
//function protectedpdf_set_display_options($data) {
//    $displayoptions = array();
////    if ($data->display == PROTECTEDPDFLIB_DISPLAY_POPUP) {
////        $displayoptions['popupwidth']  = $data->popupwidth;
////        $displayoptions['popupheight'] = $data->popupheight;
////    }
////    if (in_array($data->display, array(PROTECTEDPDFLIB_DISPLAY_AUTO, PROTECTEDPDFLIB_DISPLAY_EMBED, PROTECTEDPDFLIB_DISPLAY_FRAME))) {
////        $displayoptions['printintro']   = (int)!empty($data->printintro);
////    }
//    if (!empty($data->showsize)) {
//        $displayoptions['showsize'] = 1;
//    }
//    if (!empty($data->showtype)) {
//        $displayoptions['showtype'] = 1;
//    }
//    $data->displayoptions = serialize($displayoptions);
//}

/**
 * Delete protectedpdf instance.
 * @param int $id
 * @return bool true
 */
function protectedpdf_delete_instance($id) {
    global $DB;

    if (!$protectedpdf = $DB->get_record('protectedpdf', array('id' => $id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('protectedpdf', array('id' => $protectedpdf->id));

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info info
 */
function protectedpdf_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->dirroot/mod/protectedpdf/locallib.php");
    require_once($CFG->libdir . '/completionlib.php');

    $context = context_module::instance($coursemodule->id);

    if (!$protectedpdf = $DB->get_record('protectedpdf', array('id' => $coursemodule->instance), 'id, name, display, tobemigrated, revision, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $protectedpdf->name;
    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('protectedpdf', $protectedpdf, $coursemodule->id, false);
    }

    if ($protectedpdf->tobemigrated) {
        $info->icon = 'i/invalid';
        return $info;
    }
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', 0, 'sortorder DESC, id ASC', false); // TODO: this is not very efficient!!
    if (count($files) >= 1) {
        $mainfile = reset($files);
        $info->icon = file_file_icon($mainfile, 24);
        $protectedpdf->mainfile = $mainfile->get_filename();
    }

//    $display = protectedpdf_get_final_display_type($protectedpdf);
//
//    if ($display == PROTECTEDPDFLIB_DISPLAY_POPUP) {
//        $fullurl = "$CFG->wwwroot/mod/protectedpdf/view.php?id=$coursemodule->id&amp;redirect=1";
//        $options = empty($protectedpdf->displayoptions) ? array() : unserialize($protectedpdf->displayoptions);
//        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
//        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
//        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
//        $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";
//
//    } else if ($display == PROTECTEDPDFLIB_DISPLAY_NEW) {
//        $fullurl = "$CFG->wwwroot/mod/protectedpdf/view.php?id=$coursemodule->id&amp;redirect=1";
//        $info->onclick = "window.open('$fullurl'); return false;";
//
//    }
    // If any optional extra details are turned on, store in custom data
    $info->customdata = protectedpdf_get_optional_details($protectedpdf, $coursemodule);

    return $info;
}

/**
 * Called when viewing course page. Shows extra details after the link if
 * enabled.
 *
 * @param cm_info $cm Course module information
 */
function protectedpdf_cm_info_view(cm_info $cm) {
    $details = $cm->customdata;
    if ($details) {
        $cm->set_after_link(' ' . html_writer::tag('span', $details, array('class' => 'protectedpdflinkdetails')));
    }
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_protectedpdf
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function protectedpdf_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('protectedpdfcontent', 'protectedpdf');
    return $areas;
}

/**
 * File browsing support for protectedpdf module content area.
 *
 * @package  mod_protectedpdf
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function protectedpdf_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot . '/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_protectedpdf', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_protectedpdf', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/protectedpdf/locallib.php");
        return new protectedpdf_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: protectedpdf_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the protectedpdf files.
 *
 * @package  mod_protectedpdf
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function protectedpdf_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/protectedpdf:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    array_shift($args); // ignore revision - designed to prevent caching problems only

    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = rtrim("/$context->id/mod_protectedpdf/$filearea/0/$relativepath", '/');
    do {
        if (!$file = $fs->get_file_by_hash(sha1($fullpath))) {
            if ($fs->get_file_by_hash(sha1("$fullpath/."))) {
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.htm"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/index.html"))) {
                    break;
                }
                if ($file = $fs->get_file_by_hash(sha1("$fullpath/Default.htm"))) {
                    break;
                }
            }
            $protectedpdf = $DB->get_record('protectedpdf', array('id' => $cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($protectedpdf->legacyfiles != PROTECTEDPDFLIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/' . $relativepath, $cm->id, $cm->course, 'mod_protectedpdf', 'content', 0)) {
                return false;
            }
            // file migrate - update flag
            $protectedpdf->legacyfileslast = time();
            $DB->update_record('protectedpdf', $protectedpdf);
        }
    } while (false);

    // should we apply filters?
    $mimetype = $file->get_mimetype();
    if ($mimetype === 'text/html' or $mimetype === 'text/plain') {
        $filter = $DB->get_field('protectedpdf', 'filterfiles', array('id' => $cm->instance));
        $CFG->embeddedsoforcelinktarget = true;
    } else {
        $filter = 0;
    }

    // finally send the file
    send_stored_file($file, null, $filter, $forcedownload, $options);
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function protectedpdf_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-protectedpdf-*' => get_string('page-mod-protectedpdf-x', 'protectedpdf'));
    return $module_pagetype;
}

/**
 * Export file protectedpdf contents
 *
 * @return array of file content
 */
function protectedpdf_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);
    $protectedpdf = $DB->get_record('protectedpdf', array('id' => $cm->instance), '*', MUST_EXIST);

    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', 0, 'sortorder DESC, id ASC', false);

    foreach ($files as $fileinfo) {
        $file = array();
        $file['type'] = 'file';
        $file['filename'] = $fileinfo->get_filename();
        $file['filepath'] = $fileinfo->get_filepath();
        $file['filesize'] = $fileinfo->get_filesize();
        $file['fileurl'] = file_encode_url("$CFG->wwwroot/" . $baseurl, '/' . $context->id . '/mod_protectedpdf/content/' . $protectedpdf->revision . $fileinfo->get_filepath() . $fileinfo->get_filename(), true);
        $file['timecreated'] = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder'] = $fileinfo->get_sortorder();
        $file['userid'] = $fileinfo->get_userid();
        $file['author'] = $fileinfo->get_author();
        $file['license'] = $fileinfo->get_license();
        $contents[] = $file;
    }

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function protectedpdf_dndupload_register() {
    return array('files' => array(
            array('extension' => '*', 'message' => get_string('dnduploadprotectedpdf', 'mod_protectedpdf'))
    ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function protectedpdf_dndupload_handle($uploadinfo) {
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '';
    $data->introformat = FORMAT_HTML;
    $data->coursemodule = $uploadinfo->coursemodule;
    $data->files = $uploadinfo->draftitemid;

    // Set the display options to the site defaults.
    $config = get_config('protectedpdf');
    $data->display = $config->display;
    $data->popupheight = $config->popupheight;
    $data->popupwidth = $config->popupwidth;
    $data->printintro = $config->printintro;
    $data->showsize = (isset($config->showsize)) ? $config->showsize : 0;
    $data->showtype = (isset($config->showtype)) ? $config->showtype : 0;
    $data->filterfiles = $config->filterfiles;

    return protectedpdf_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $protectedpdf   protectedpdf object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function protectedpdf_view($protectedpdf, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $protectedpdf->id
    );

    $event = \mod_protectedpdf\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('protectedpdf', $protectedpdf);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}
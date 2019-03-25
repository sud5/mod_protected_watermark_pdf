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
 * Private protectedpdf module utility functions
 *
 * @package    mod_protectedpdf
 * @copyright  2017 Sudhanshu Gupta
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('PROTECTEDPDFLIB_DISPLAY_AUTO', 0);
/** Display using object tag */
define('PROTECTEDPDFLIB_DISPLAY_EMBED', 1);
/** Display inside frame */
define('PROTECTEDPDFLIB_DISPLAY_FRAME', 2);
/** Display normal link in new window */
define('PROTECTEDPDFLIB_DISPLAY_NEW', 3);
/** Force download of file instead of display */
define('PROTECTEDPDFLIB_DISPLAY_DOWNLOAD', 4);
/** Open directly */
define('PROTECTEDPDFLIB_DISPLAY_OPEN', 5);
/** Open in "emulated" pop-up without navigation */
define('PROTECTEDPDFLIB_DISPLAY_POPUP', 6);

/** Legacy files not needed or new resource */
define('PROTECTEDPDFLIB_LEGACYFILES_NO', 0);
/** Legacy files conversion marked as completed */
define('PROTECTEDPDFLIB_LEGACYFILES_DONE', 1);
/** Legacy files conversion in progress*/
define('PROTECTEDPDFLIB_LEGACYFILES_ACTIVE', 2);

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/protectedpdf/lib.php");

/**
 * Redirected to migrated protectedpdf if needed,
 * return if incorrect parameters specified
 * @param int $oldid
 * @param int $cmid
 * @return void
 */
function protectedpdf_redirect_if_migrated($oldid, $cmid) {
    global $DB, $CFG;

    if ($oldid) {
        $old = $DB->get_record('protectedpdf_old', array('oldid'=>$oldid));
    } else {
        $old = $DB->get_record('protectedpdf_old', array('cmid'=>$cmid));
    }

    if (!$old) {
        return;
    }

    redirect("$CFG->wwwroot/mod/$old->newmodule/view.php?id=".$old->cmid);
}

/**
 * Display embedded protectedpdf file.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function protectedpdf_display_embed($protectedpdf, $cm, $course, $file) {
    global $CFG, $PAGE, $OUTPUT;

    $clicktoopen = protectedpdf_get_clicktoopen($file, $protectedpdf->revision);

    $context = context_module::instance($cm->id);
    $path = '/'.$context->id.'/mod_protectedpdf/content/'.$protectedpdf->revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
    $moodleurl = new moodle_url('/pluginfile.php' . $path);

    $mimetype = $file->get_mimetype();
    $title    = $protectedpdf->name;

    $extension = protectedpdflib_get_extension($file->get_filename());

    $mediarenderer = $PAGE->get_renderer('core', 'media');
    $embedoptions = array(
        core_media::OPTION_TRUSTED => true,
        core_media::OPTION_BLOCK => true,
    );

    if (file_mimetype_in_typegroup($mimetype, 'web_image')) {  // It's an image
        $code = protectedpdflib_embed_image($fullurl, $title);

    } else if ($mimetype === 'application/pdf') {
        // PDF document
        $code = protectedpdflib_embed_pdf($fullurl, $title, $clicktoopen);

    } else if ($mediarenderer->can_embed_url($moodleurl, $embedoptions)) {
        // Media (audio/video) file.
        $code = $mediarenderer->embed_url($moodleurl, $title, 0, 0, $embedoptions);

    } else {
        // anything else - just try object tag enlarged as much as possible
        $code = protectedpdflib_embed_general($fullurl, $title, $clicktoopen, $mimetype);
    }

    protectedpdf_print_header($protectedpdf, $cm, $course);
    protectedpdf_print_heading($protectedpdf, $cm, $course);

    echo $code;

    protectedpdf_print_intro($protectedpdf, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

/**
 * Display protectedpdf frames.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function protectedpdf_display_frame($protectedpdf, $cm, $course, $file) {
    global $PAGE, $OUTPUT, $CFG;

    $frame = optional_param('frameset', 'main', PARAM_ALPHA);

    if ($frame === 'top') {
        $PAGE->set_pagelayout('frametop');
        protectedpdf_print_header($protectedpdf, $cm, $course);
        protectedpdf_print_heading($protectedpdf, $cm, $course);
        protectedpdf_print_intro($protectedpdf, $cm, $course);
        echo $OUTPUT->footer();
        die;

    } else {
        $config = get_config('protectedpdf');
        $context = context_module::instance($cm->id);
        $path = '/'.$context->id.'/mod_protectedpdf/content/'.$protectedpdf->revision.$file->get_filepath().$file->get_filename();
        $fileurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
        $navurl = "$CFG->wwwroot/mod/protectedpdf/view.php?id=$cm->id&amp;frameset=top";
        $title = strip_tags(format_string($course->shortname.': '.$protectedpdf->name));
        $framesize = $config->framesize;
        $contentframetitle = format_string($protectedpdf->name);
        $modulename = s(get_string('modulename','protectedpdf'));
        $dir = get_string('thisdirection', 'langconfig');

        $file = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html dir="$dir">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>$title</title>
  </head>
  <frameset rows="$framesize,*">
    <frame src="$navurl" title="$modulename" />
    <frame src="$fileurl" title="$contentframetitle" />
  </frameset>
</html>
EOF;

        @header('Content-Type: text/html; charset=utf-8');
        echo $file;
        die;
    }
}

/**
 * Internal function - create click to open text with link.
 */
function protectedpdf_get_clicktoopen($file, $revision, $extra='') {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/'.$file->get_contextid().'/mod_protectedpdf/content/'.$revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);

    $string = get_string('clicktoopen2', 'protectedpdf', "<a href=\"$fullurl\" $extra>$filename</a>");

    return $string;
}

/**
 * Internal function - create click to open text with link.
 */
function protectedpdf_get_clicktodownload($file, $revision) {
    global $CFG;

    $filename = $file->get_filename();
    $path = '/'.$file->get_contextid().'/mod_protectedpdf/content/'.$revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, true);

    $string = get_string('clicktodownload', 'protectedpdf', "<a href=\"$fullurl\">$filename</a>");

    return $string;
}

/**
 * Print protectedpdf info and workaround link when JS not available.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function protectedpdf_print_workaround($protectedpdf, $cm, $course, $file) {
    global $CFG, $OUTPUT;

    protectedpdf_print_header($protectedpdf, $cm, $course);
    protectedpdf_print_heading($protectedpdf, $cm, $course, true);
    protectedpdf_print_intro($protectedpdf, $cm, $course, true);

    $protectedpdf->mainfile = $file->get_filename();
    echo '<div class="protectedpdfworkaround">';
    switch (protectedpdf_get_final_display_type($protectedpdf)) {
        case PROTECTEDPDFLIB_DISPLAY_POPUP:
            $path = '/'.$file->get_contextid().'/mod_protectedpdf/content/'.$protectedpdf->revision.$file->get_filepath().$file->get_filename();
            $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
            $options = empty($protectedpdf->displayoptions) ? array() : unserialize($protectedpdf->displayoptions);
            $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
            $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
            $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
            $extra = "onclick=\"window.open('$fullurl', '', '$wh'); return false;\"";
            echo protectedpdf_get_clicktoopen($file, $protectedpdf->revision, $extra);
            break;

        case PROTECTEDPDFLIB_DISPLAY_NEW:
            $extra = 'onclick="this.target=\'_blank\'"';
            echo protectedpdf_get_clicktoopen($file, $protectedpdf->revision, $extra);
            break;

        case PROTECTEDPDFLIB_DISPLAY_DOWNLOAD:
            echo protectedpdf_get_clicktodownload($file, $protectedpdf->revision);
            break;

        case PROTECTEDPDFLIB_DISPLAY_OPEN:
        default:
            echo protectedpdf_get_clicktoopen($file, $protectedpdf->revision);
            break;
    }
    echo '</div>';

    echo $OUTPUT->footer();
    die;
}

/**
 * Print protectedpdf header.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @return void
 */
function protectedpdf_print_header($protectedpdf, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$protectedpdf->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($protectedpdf);
    $PAGE->set_button(update_module_button($cm->id, '', get_string('modulename', 'protectedpdf')));
    echo $OUTPUT->header();
}

/**
 * Print protectedpdf heading.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @param bool $notused This variable is no longer used
 * @return void
 */
function protectedpdf_print_heading($protectedpdf, $cm, $course, $notused = false) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($protectedpdf->name), 2);
}

/**
 * Gets optional details for a protectedpdf, depending on protectedpdf settings.
 *
 * Result may include the file size and type if those settings are chosen,
 * or blank if none.
 *
 * @param object $protectedpdf Protectedpdf table row
 * @param object $cm Course-module table row
 * @return string Size and type or empty string if show options are not enabled
 */
function protectedpdf_get_optional_details($protectedpdf, $cm) {
    global $DB;

    $details = '';

    $options = empty($protectedpdf->displayoptions) ? array() : unserialize($protectedpdf->displayoptions);
    if (!empty($options['showsize']) || !empty($options['showtype'])) {
        $context = context_module::instance($cm->id);
        $size = '';
        $type = '';
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', 0, 'sortorder DESC, id ASC', false);
        if (!empty($options['showsize']) && count($files)) {
            $sizebytes = 0;
            foreach ($files as $file) {
                // this will also synchronize the file size for external files if needed
                $sizebytes += $file->get_filesize();
            }
            if ($sizebytes) {
                $size = display_size($sizebytes);
            }
        }
        if (!empty($options['showtype']) && count($files)) {
            // For a typical file protectedpdf, the sortorder is 1 for the main file
            // and 0 for all other files. This sort approach is used just in case
            // there are situations where the file has a different sort order
            $mainfile = reset($files);
            $type = get_mimetype_description($mainfile);
            // Only show type if it is not unknown
            if ($type === get_mimetype_description('document/unknown')) {
                $type = '';
            }
        }

        if ($size && $type) {
            // Depending on language it may be necessary to show both options in
            // different order, so use a lang string
            $details = get_string('protectedpdfdetails_sizetype', 'protectedpdf',
                    (object)array('size'=>$size, 'type'=>$type));
        } else {
            // Either size or type is set, but not both, so just append
            $details = $size . $type;
        }
    }

    return $details;
}

/**
 * Print protectedpdf introduction.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function protectedpdf_print_intro($protectedpdf, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($protectedpdf->displayoptions) ? array() : unserialize($protectedpdf->displayoptions);

    $extraintro = protectedpdf_get_optional_details($protectedpdf, $cm);
    if ($extraintro) {
        // Put a paragaph tag around the details
        $extraintro = html_writer::tag('p', $extraintro, array('class' => 'protectedpdfdetails'));
    }

    if ($ignoresettings || !empty($options['printintro']) || $extraintro) {
        $gotintro = trim(strip_tags($protectedpdf->intro));
        if ($gotintro || $extraintro) {
            echo $OUTPUT->box_start('mod_introbox', 'protectedpdfintro');
            if ($gotintro) {
                echo format_module_intro('protectedpdf', $protectedpdf, $cm->id);
            }
            echo $extraintro;
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Print warning that instance not migrated yet.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function protectedpdf_print_tobemigrated($protectedpdf, $cm, $course) {
    global $DB, $OUTPUT;

    $protectedpdf_old = $DB->get_record('protectedpdf_old', array('oldid'=>$protectedpdf->id));
    protectedpdf_print_header($protectedpdf, $cm, $course);
    protectedpdf_print_heading($protectedpdf, $cm, $course);
    protectedpdf_print_intro($protectedpdf, $cm, $course);
    echo $OUTPUT->notification(get_string('notmigrated', 'protectedpdf', $protectedpdf_old->type));
    echo $OUTPUT->footer();
    die;
}

/**
 * Print warning that file can not be found.
 * @param object $protectedpdf
 * @param object $cm
 * @param object $course
 * @return void, does not return
 */
function protectedpdf_print_filenotfound($protectedpdf, $cm, $course) {
    global $DB, $OUTPUT;

    $protectedpdf_old = $DB->get_record('protectedpdf_old', array('oldid'=>$protectedpdf->id));
    protectedpdf_print_header($protectedpdf, $cm, $course);
    protectedpdf_print_heading($protectedpdf, $cm, $course);
    protectedpdf_print_intro($protectedpdf, $cm, $course);
    if ($protectedpdf_old) {
        echo $OUTPUT->notification(get_string('notmigrated', 'protectedpdf', $protectedpdf_old->type));
    } else {
        echo $OUTPUT->notification(get_string('filenotfound', 'protectedpdf'));
    }
    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best display format.
 * @param object $protectedpdf
 * @return int display type constant
 */
function protectedpdf_get_final_display_type($protectedpdf) {
    global $CFG, $PAGE;

    if ($protectedpdf->display != PROTECTEDPDFLIB_DISPLAY_AUTO) {
        return $protectedpdf->display;
    }

    if (empty($protectedpdf->mainfile)) {
        return PROTECTEDPDFLIB_DISPLAY_DOWNLOAD;
    } else {
        $mimetype = mimeinfo('type', $protectedpdf->mainfile);
    }

    if (file_mimetype_in_typegroup($mimetype, 'archive')) {
        return PROTECTEDPDFLIB_DISPLAY_DOWNLOAD;
    }
    if (file_mimetype_in_typegroup($mimetype, array('web_image', '.htm', 'web_video', 'web_audio'))) {
        return PROTECTEDPDFLIB_DISPLAY_EMBED;
    }

    // let the browser deal with it somehow
    return PROTECTEDPDFLIB_DISPLAY_OPEN;
}

/**
 * File browsing support class
 */
class protectedpdf_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

function protectedpdf_set_mainfile($data) {
    global $DB;
    $fs = get_file_storage();
    $cmid = $data->coursemodule;
    $draftitemid = $data->files;

    $context = context_module::instance($cmid);
    if ($draftitemid) {
        file_save_draft_area_files($draftitemid, $context->id, 'mod_protectedpdf', 'content', 0, array('subdirs'=>true));
    }
    $files = $fs->get_area_files($context->id, 'mod_protectedpdf', 'content', 0, 'sortorder', false);
    if (count($files) == 1) {
        // only one file attached, set it as main file automatically
        $file = reset($files);
        file_set_sortorder($context->id, 'mod_protectedpdf', 'content', 0, $file->get_filepath(), $file->get_filename(), 1);
    }
}

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Library of interface functions and constants.
 *
 * @package     mod_qbank
 * @copyright   2021 Catalyst IT Australia Pty Ltd
 * @author      Nicholas Hoobin <nicholashoobin@catalyst-au.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function qbank_supports(string $feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
        case FEATURE_USES_QUESTIONS:
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_qbank into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_qbank_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function qbank_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('qbank', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_qbank in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_qbank_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function qbank_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('qbank', $moduleinstance);
}

/**
 * Removes an instance of the mod_qbank from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function qbank_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('qbank', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('qbank', array('id' => $id));

    return true;
}

/**
 * Question data fragment to get the question html via ajax call.
 *
 * @param $args
 * @return array|string
 */
function mod_qbank_output_fragment_question_data($args) {
    if (empty($args)) {
        return '';
    }
    $param = json_decode($args);
    $filtercondition = json_decode($param->filtercondition);
    if (!$filtercondition) {
        return ['', ''];
    }
    $extraparams = json_decode($param->extraparams);
    $params = \core_question\local\bank\helper::convert_object_array($filtercondition);
    $extraparamsclean = [];
    if (!empty($extraparams)) {
        $extraparamsclean = $extraparams;
    }
    $thispageurl = new \moodle_url('/mod/qbank/view.php');
    $thiscontext = \context::instance_by_id($param->contextid);
    $thispageurl->param('cmid', $thiscontext->instanceid);
    $contexts = new \core_question\local\bank\question_edit_contexts($thiscontext);
    $contexts->require_one_edit_tab_cap($params['tabname']);
    $course = get_course($params['courseid']);
    $questionbank = new \core_question\local\bank\view($contexts, $thispageurl, $course, null, $params, $extraparamsclean);
    list($questionhtml, $jsfooter) = $questionbank->display_questions_table();
    return [$questionhtml, $jsfooter];
}

/**
 * This function extends the settings navigation block for the site.
 *
 * It is safe to rely on PAGE here as we will only ever be within the module
 * context when this is called
 *
 * @param settings_navigation $settings
 * @param navigation_node $qbanknode
 * @return void
 */
function qbank_extend_settings_navigation(settings_navigation $settings, navigation_node $qbanknode) {
    question_extend_settings_navigation($qbanknode, $settings->get_page()->cm->context)->trim_if_empty();
}


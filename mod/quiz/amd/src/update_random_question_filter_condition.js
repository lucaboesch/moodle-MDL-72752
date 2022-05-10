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
 * Question bank filter managemnet.
 *
 * @module     mod_quiz/update_random_question_filter_condition
 * @author      2022 <nathannguyen@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import ajax from 'core/ajax';
import Notification from 'core/notification';

/**
 * Filter condition update form.
 *
 * @param {String} returnUrl return URL for redirection
 */
export const init = (returnUrl) => {
    const SELECTORS = {
        FORM_CONTAINER: '#update_filter_condition_form-container',
        UPDATE_BUTTON: '[name="update"]',
        CANCEL_BUTTON: '[name="cancel"]',
        FILTER_CONDITION_ELEMENT: '[data-filtercondition]',
    };

    const form = document.querySelector(SELECTORS.FORM_CONTAINER);
    const updateButton = form.querySelector(SELECTORS.UPDATE_BUTTON);
    const cancelButton = form.querySelector(SELECTORS.CANCEL_BUTTON);

    updateButton.addEventListener("click", () => {
        const request = {
            methodname: 'mod_quiz_update_filter_condition',
            args: {
                cmid: form.dataset?.cmid,
                id: form.dataset?.id,
                filtercondition: form.querySelector(SELECTORS.FILTER_CONDITION_ELEMENT).dataset?.filtercondition,
            }
        };
       ajax.call([request])[0]
           .then(() => {
               window.location.href = returnUrl;
            })
           .catch(Notification.exception);
    });

    cancelButton.addEventListener("click", () => {
        window.location.href = returnUrl;
    });

};

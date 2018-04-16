'use strict';

import 'blockui';
import _ from 'lodash';

class Helpers {
    /**
     * Block given element with loading overlay.
     *
     * @param selector
     */
    static blockElement(selector) {
        $(selector).block({
            message: '<i class="fa fa-spin fa-spinner"></i> Please wait',
            css: {
                border: '1px solid #000',
                padding: '15px',
                background: 'rgba(0, 0, 0, 0.3)',
                color: '#fff'
            }
        });
    }

    /**
     * Unblock given element.
     *
     * @param selector
     */
    static unblockElement(selector) {
        $(selector).unblock();
    }

    /**
     * Generate random string with given length.
     *
     * @param length
     * @returns {string}
     */
    static randomString(length = 6) {
        let text = '';
        let possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        for (let i = 0; i < length; i++) {
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        }

        return text;
    }

    /**
     * Display and log server side error (for axios catch).
     *
     * @param err
     */
    static showServerError(err) {
        if (err.response.status !== 422) {
            $.growl.error({
                message: err.message
            });

            console.error(err.response || err);

            return;
        }

        // Validation error.
        let errors = err.response.data.errors;

        $.growl.warning({
            title: err.response.data.message,
            message: errors[_.keys(errors)[0]][0]
        });
    }

    /**
     * Build form data from object.
     *
     * @param FormData
     * @param data
     * @param name
     */
    static buildFormData(FormData, data, name) {
        name = name || '';

        if (data instanceof File) {
            FormData.append(name, data);
        } else if (typeof data === 'object') {
            _.each(data, (value, index) => {
                if (name === '') {
                    this.buildFormData(FormData, value, `${index}`);
                } else {
                    this.buildFormData(FormData, value, `${name}[${index}]`);
                }
            });
        } else {
            FormData.append(name, data);
        }
    }
}

export default Helpers;
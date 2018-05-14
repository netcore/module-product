'use strict';

import _ from 'lodash';
import store from './store';
import Errors from './errors';

class Helpers {
	/**
	 * Generate random string with given length.
	 *
	 * @param length
	 * @returns {string}
	 */
	static randomString (length = 6) {
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
	 * @param error
	 */
	static showServerError (error) {
		if(!error.response || (error.response.status && error.response.status !== 422)) {
			$.growl.error({
				message: error.message
			});

			console.error(error);

			return;
		}

		// Validation error.
		let errors = error.response.data.errors;

		$.growl.warning({
			title: error.response.data.message,
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
	static buildFormData (FormData, data, name) {
		name = name || '';

		if(data === 'true' || data === 'false') {
			data = data === 'true' ? 1 : 0;
		}

		if(typeof data === 'boolean') {
			data = data ? 1 : 0;
		}

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

	/**
	 * Turn object into form data.
	 *
	 * @param data
	 * @return {FormData}
	 */
	static getFormDataFromObject (data) {
		let form = new FormData;
		Helpers.buildFormData(form, data);
		return form;
	}

	/**
	 * Debug FormData object.
	 *
	 * @param FormData
	 */
	static debugFormData (FormData) {
		for (var pair of FormData.entries()) {
			console.log(pair[0] + ': ' + pair[1]);
		}
	}

	/**
	 * Get new Errors bag instance.
	 *
	 * @return {Errors}
	 */
	static getErrorsBag () {
		return new Errors;
	}

	/**
	 * Build translations object.
	 *
	 * @param fields
	 * @return {Array}
	 */
	static mockTranslations (fields) {
		let translations = {};

		_.each(store.state.languages, language => {
			translations[language.iso_code] = _.cloneDeep(fields);
		});

		return translations;
	}
}

export default Helpers;
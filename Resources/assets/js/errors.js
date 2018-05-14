'use strict';

import _ from 'lodash';

/**
 * Helper.
 *
 * @param needle
 * @param haystack
 * @return {boolean}
 */
const beginsWith = (needle, haystack) => {
	return (haystack.substr(0, needle.length) === needle);
};

class Errors {
	/**
	 * Constructor.
	 */
	constructor () {
		this.errors = {};
	}

	/**
	 * Set errors.
	 *
	 * @param errors
	 */
	set (errors) {
		this.errors = errors;
	}

	/**
	 * Check if bag has error/-s for given key.
	 *
	 * @param key
	 * @return {boolean}
	 */
	has (key) {
		let hasError = false;

		_.each(this.errors, (errors, validationKey) => {
			if (beginsWith(key, validationKey)) {
				hasError = true;
			}
		});

		return hasError;
	}

	/**
	 * Get error for given key.
	 *
	 * @param key
	 * @return {*}
	 */
	get(key) {
		let error = this.errors[key];

		if(!error) {
			return '';
		}

		if(typeof error === 'object') {
			return error[0];
		}

		return error;
	}

	/**
	 * Get first error of given key.
	 * Can be used to get first error of some group.
	 *
	 * @param key
	 * @return {*}
	 */
	getFirstOf(key) {
		let errors = _.filter(this.errors, (o, k) => beginsWith(key, k));
		return errors[Object.keys(errors)[0]][0];
	}

	/**
	 * Clear errors of specific key.
	 *
	 * @param key
	 */
	clear (key) {
		this.errors = _.pickBy(this.errors, (error, validationKey) => {
			return !beginsWith(key, validationKey);
		});
	}

	/**
	 * Clear all errors.
	 */
	clearAll () {
		this.errors = {};
	}
}

export default Errors;
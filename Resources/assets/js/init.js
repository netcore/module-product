'use strict';

import _ from 'lodash';
import axios from 'axios';
import Helpers from './helpers';

export const Init = {
	install(Vue) {
		Vue.prototype._ = _;
		Vue.prototype.$http = axios;
		Vue.prototype.$helpers = Helpers;
	}
};
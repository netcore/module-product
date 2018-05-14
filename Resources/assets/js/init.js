'use strict';

import axios from 'axios';
import Helpers from './helpers';

export const Init = {
	install(Vue) {
		Vue.prototype.$http = axios;
		Vue.prototype.$helpers = Helpers;
	}
};
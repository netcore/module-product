'use strict';

// Imports.
import { Init } from './init';
import router from './router';
import store from './store';
import Breadcrumb from './components/Breadcrumb';

// Use's.
Vue.use(Init);

// Global components.
Vue.component('breadcrumb', Breadcrumb);

// App.
new Vue({
	el: '#product-app',
	router,
	store
});
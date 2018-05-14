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
	store,

	methods: {
		setActiveMenuItem (newRoute) {
			let parts = newRoute.name ? newRoute.name.split('.') : [];
			let menu = $('#left-admin-menu');

			$.each(menu.find('li.px-nav-item > a'), (i, item) => {
				let $item = $(item);
				let url = $item.attr('href');

				$item.parent('li').removeClass('active');

				if (url.match(`/#/${parts[0]}`)) {
					$item.parent('li').addClass('active');
					$item.closest('li.px-nav-dropdown').addClass('px-open');
				}
			});
		}
	},

	watch: {
		$route (newRoute) {
			this.setActiveMenuItem(newRoute);
		}
	}
});
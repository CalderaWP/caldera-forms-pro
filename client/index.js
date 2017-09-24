import Vue from 'vue'
import store from './store'
import SettingsView from './views/Settings.vue';
import {Tabs, Tab} from 'vue-tabs-component';


Vue.component('tabs', Tabs);
Vue.component('tab', Tab);

const app = new Vue({
	el: '#app',
	store,
	components: {
		'settings': SettingsView
	},
	render(h) {
		return h(
			'div',
			{
				attrs: {
					id: 'cf-pro-settings'
				}
			},
			[
				h( 'settings')
			]
		)
	}

});

export { app, store }

import Vue from 'vue'
import Settings from './components/Settings.vue'

//import store from './store/index.js';

import Vuex from 'vuex'
import Store from './store/index';

new Vue({
	el: '#cf-pro-message-settings',
	Store,
  	render: h => h(Settings)
});


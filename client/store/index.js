import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const STATE = {
	loading: false,
	connected: false,
	forms: [
	],
	settings : {
		enhancedDelivery: false,
		generatePDFs: false
	},
	layouts : [
		{name:'FOFOF'}
	],
	account: {
		plan: String,
		id: Number,
		apiKeys: {
			public: String,
			secret: String,
			token: String
		}
	}

};


import { MUTATIONS } from './mutations';

import { ACTIONS } from './actions';

import  { GETTERS } from './getters';


const accountSaver = store => {
  // called when the store is initialized
	store.subscribe((mutation, state) => {
		const type = mutation.type;
		switch (type) {
			case 'apiKeys' :
			case 'secretKey':
			case 'publicKey':
				if (!state.connected && state.account.apiKeys.token && state.account.apiKeys.public) {
					store.dispatch('testConnection');
					store.dispatch('getLayouts');
				} else if (!state.account.apiKeys.public || !state.account.apiKeys.secret) {
					store.commit('connected', 0);
				} else {
					store.commit('connected', 0);
				}
				break;
			case  'connected' :
				if (state.connected) {
					store.dispatch('getLayouts')
				}
				break;
		}

  })
};

const store =  new Vuex.Store({
  strict: false,
  plugins: [
  	accountSaver,
  ],
  modules: {},
  state: STATE,
  getters: GETTERS,
  mutations: MUTATIONS,
  actions: ACTIONS
});


export default store;

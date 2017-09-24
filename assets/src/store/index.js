import Vue from 'vue'
import Vuex from 'vuex'
import { mapMutations } from 'vuex'

Vue.use(Vuex);



const STATE = {

		forms: [
			{ form_id : 1, name: 'One', layout: 2, pdf_layout:1 },
			{ form_id : 2, name: 'Two', layout: 1, pdf_layout:2 },
		],
		settings : {
			enhancedDelivery: true,
			generatePDFs: false
		},
		layouts : [
			{ id: 1, name: 'One' },
			{ id: 2, name: 'Two' }
		],
		account: {
			plan: 'apex',
			id: 42,
			apiKeys: {
				public: 'pub',
				secret: 'secret'
			}
		}

};

const isObject = function (value) {
	/**@TODO npm in just this function from lodash, beacuse the fact that I copypasted 3 lines of code is THE WORST THING EVER! https://github.com/lodash/lodash/blob/master/isObject.js **/

	const type = typeof value;
	return value != null && (type == 'object' || type == 'function');
	//Look - I added semicolons
};

const hasProp = function (maybeObj, prop) {
	return isObject(maybeObj) && objHasProp(maybeObj,prop);
};

const objHasProp = function(obj,pro) {
	return Object.prototype.hasOwnProperty.call(obj, prop);
};

const findForm = function(state,formId){
	return state.forms.find(form => form.form_id === formId);
};

const findFormOffset = function(state,formId){
	return state.forms.findIndex(form => form.form_id === formId);
};

const GETTERS = {
	getFormById: state => (formId) => {
		return findForm(state,formId);
	},
	getFormSetting: state => (formId, setting) => {

	},
	getSetting: state => (setting,_default) => {
		if( objHasProp(state.settings, setting )){
			return state.settings[setting];
		}
		return _default;
	},
	forms: state => state.forms,
	settings: state => state.settings,
	layouts: state => state.layouts,
	account: state => state.account
};


const MUTATIONS = {
	formSetting:  state => (state,obj => {
		const formId = obj.formId;
		const setting = obj.setting;
		const value = obj.value;
		const form = findForm(state,formId);
		if (undefined != typeof  form ) {
			state.forms[findFormOffset(state,formId)][setting]=value;
		}
	}),
	setting: state => (state,obj=> {
		const setting = obj.setting;
		const value = obj.value;
		state.settings[setting]=value;
	}),
	account(state,account){
		state.account=account;
	}

};


const CFProConfig = {
	/**
	 * The URL for local site API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	localApiURL: CF_PRO_ADMIN.api.cf.url,

	/**
	 * The nonce for local site API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	localApiNonce: CF_PRO_ADMIN.api.cf.nonce,

	/**
	 * The URL for remote app API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	appURL: CF_PRO_ADMIN.api.cfPro.url
};

const ACTIONS = {
	getAccount ({ commit }) {
		return jQuery({
			method: "GET",
			url: CFProConfig.localApiURL
		} ).then( r => {
			console.log(r);
		});

	}
};


module.exports =  new Vuex.Store({
	strict: true,
	plugins: [],
	modules: {},
	state: STATE,
	getters: {},
	mutations: {},
	actions: {}
});
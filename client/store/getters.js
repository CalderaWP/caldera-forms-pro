import  { objHasProp, findForm } from './util/utils';

export const GETTERS = {
	publicKey: state => {
		return state.account.apiKey.public;
	},
	secretKey: state => {
		return state.account.apiKey.secret;
	},
	apiKeys: state => {
		return state.account.apiKeys;
	},
	nom: state => {
		return 'nom'
	},
	getSetting: state => (setting,_default) => {
		if( objHasProp(state.settings, setting )){
			return state.settings[setting];
		}
		return _default;
	},
	getFormsById: (state, getters) => (id) => {
		return state.forms.find(form => form.form_id === id)
	}

};

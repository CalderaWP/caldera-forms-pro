export const MUTATIONS = {
	publicKey (state,value) {
		state.account.apiKeys.public = value;
	},
	secretKey (state,value) {
		state.account.apiKeys.secret = value;
	},
	apiKeys (state,obj) {
		state.account.apiKeys.public = obj.public;
		state.account.apiKeys.secret = obj.secret;
		state.account.apiKeys.token = obj.token;
	},
	accountId(state,value){
		state.account.id = value;
	},
	plan(state,value){
		state.account.plan = value;
	},
	loading(state){
		state.loading = ! state.loading;
	},
	forms(state, value){
		state.forms = value;
	},
	connected(state,value ){
		state.connected = value;
	},
	layouts(state,value){
		state.layouts = value;
	}
};



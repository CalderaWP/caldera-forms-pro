export default {
	apiKeys: {
		public: '',
		private: ''
	},
	getApiKeys (){
		return this.apiKeys;
	},
	hasAuth(){
		return true;
	},
	testConnection(){
		return this.testConnection() && true;
	},
	getLayouts() {
		return {};
	},
	getSettings(){
		return {};
	}
}
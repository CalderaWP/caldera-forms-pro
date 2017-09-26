import debounce from 'lodash.debounce';
export const accountSaver = store => {
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

export const formSaver = store => {
	store.subscribe((mutation, state) => {
		if( 'form' === mutation.type ){
			debounce(
				() => {
					store.dispatch('saveAccount')
				}, 350
			);
		}


	})
};





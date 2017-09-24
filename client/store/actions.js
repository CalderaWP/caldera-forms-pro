import { localAPI, appAPI, appToken } from './util/API';
import { urlString } from './util/urlString';

export const ACTIONS = {
	getAccount(context){
		return new Promise((resolve, reject) => {
			localAPI.get().then(response => {
				var r;
				if ('string' == typeof response.data) {
					const maybe = JSON.parse(response.data);
					if ('object' === typeof  maybe) {
						r = maybe;
					}else{
						throw new Exception;
					}
				} else {
					r = response.data;
				}
				context.commit('forms', r.forms);
				context.commit('apiKeys', r.apiKeys);
				context.commit('accountId', r.account_id);
				context.commit('plan', r.plan);
				context.commit('enhancedDelivery', r.enhancedDelivery);
				resolve(response);
			}, error => {
				reject(error);
			});
		})
	},
	saveAccount({commit, state}) {
		return localAPI.post('', {
			accountId: state.account.id,
			apiKey: state.account.apiKeys.public,
			apiSecret: state.account.apiKeys.secret,
			enhancedDelivery: state.settings.enhancedDelivery,
			plan: state.account.plan,
			forms: state.forms
		}).then(r => {
			console.log(r);
		});
	},
	testConnection({commit, state}) {
		return new Promise((resolve, reject) => {
			if (state.account.apiKeys.token) {
				return appAPI.get(
					urlString(
						{
							public: state.account.apiKeys.public,
							token: appToken( state.account.apiKeys ),
						},
						'/account/verify'
					)
				).then(r => {
						state.account.plan = r.plan;
						state.account.id = r.id;
						state.connected = true;
						commit( 'connected', true );
						resolve(r);
					},
					error => {
						reject(error);
					});

			}else{
				reject( 'Not Connected' );
			}
		});

	},
	getLayouts({commit, state}) {
		if( state.connected ){
			return appAPI.get(
				urlString(
					{
						simple: true,
						public: state.account.apiKeys.public,
						token: state.account.apiKeys.token,
					},
					'/layouts/list'
				)
			).then(
				r => {
					commit( 'layouts', r.data );
				}, e => {
					console.log(e);
				}
			);
		}
	}
};

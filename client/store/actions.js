import { localAPI, appAPI, appToken } from './util/API';
import { urlString } from './util/urlString';
import CFProConfig from  './util/wpConfig';

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
				context.commit('formScreen', r.hasOwnProperty( 'formScreen' ) ? r.formScreen : CFProConfig.formScreen );
				resolve(response);
			}, error => {
				reject(error);
			});
		})
	},
	saveAccount(context) {
		return localAPI.post('', {
			accountId: context.state.account.id,
			apiKey: context.state.account.apiKeys.public,
			apiSecret: context.state.account.apiKeys.secret,
			enhancedDelivery: context.state.settings.enhancedDelivery,
			plan: context.state.account.plan,
			forms: context.state.forms
		}).then(r => {
			if( r.data.hasOwnProperty( '_cfAlertMessage' ) ){
				context.dispatch( 'updateMainAlert', _cfAlertMessage );
			}else{
				context.dispatch( 'updateMainAlert', {
					message: context.state.strings.saved,
					show: true,
					success: true,
					fade: 1500
				});
			}
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
	},
	/**
	 * Set the main alert -- status.
	 *
	 * Using this over mutation mainAlert, which this uses, is you can send a number of milliseconds in alert.fade and it will removed in that number of milliseconds
	 *
	 * @since 1.0.0
	 *
	 * @param {*} context
	 * @param {Object} alert Commit payload
	 */
	updateMainAlert(context, alert){
		const fade = ( alert.hasOwnProperty( 'fade' ) && ! isNaN( alert.fade ) ) ? alert.fade : 0;
		if( fade ){
			//OMG(s) window scope.
			window.setTimeout( () =>{
				context.dispatch( 'closeMainAlert' );
			}, fade );
		}
		context.commit('mainAlert',alert)
	},
	/**
	 * Make mainAlert clode
	 *
	 * @since 1.0.0
	 *
	 * @param context
	 */
	closeMainAlert(context){
		context.dispatch( 'updateMainAlert', {
			show:false,
		} );
	}
};

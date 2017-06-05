/** globals jQuery, CF_PRO_ADMIN, Vue **/
jQuery(function($) {

	/**
	 * The template for the layout chooser component
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	const layoutChooserTemplate = document.getElementById('cf-pro-layout-chooser' ).innerHTML;

	const checkboxSettingTemplate = document.getElementById( 'cf-pro-checkbox' ).innerHTML;

	/**
	 * The URL for local site API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	const localApiURL = CF_PRO_ADMIN.api.cf.url;

	/**
	 * The nonce for local site API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	const localApiNonce = CF_PRO_ADMIN.api.cf.nonce;

	/**
	 * The URL for remote app API
	 *
	 * @since 0.0.1
	 *
	 * @type {string}
	 */
	const appURL = CF_PRO_ADMIN.api.cfPro.url;

	/**
	 * Creates a reusable layout chooser select
	 *
	 * @since 0.0.1
	 */
	Vue.component('layout-chooser', {
		template: layoutChooserTemplate,
		props: [
			'form',
			'layouts',
			'setting',
			'disabled'
		],
		methods: {
			layoutChanged: function(e) {
				let selected = $( e.target ).val();
				this.$parent.$emit( 'layoutChosen', {
					form: this.form.form_id,
					selected: selected,
					setting: this.setting
				});

			},
			idAttr: function (formId) {
				return 'cf-pro-choose-template-' + formId;
			}

		},
		computed: {
			selected(){
				return this.form[this.setting];
			}
		}

	});

	Vue.component( 'checkbox-setting', {
		template: checkboxSettingTemplate,
		props: [
			'form',
			'setting',
			'label'
		],
		methods: {
			idAttr: function (formId) {
				return 'cf-pro-' + this.setting + '-' + formId;
			},
			changed: function (e) {
				let selected = $( e.target ).prop( 'checked' );
				this.selected = selected;
				this.$parent.$emit( 'checkboxChanged', {
					form: this.form.form_id,
					setting: this.setting,
					value: selected
				});
			}
		},
		computed: {
			selected(){
				let selected = this.form[this.setting];
				return this.form[this.setting];
			}
		}


	});

	/**
	 * The admin app for the message UI
	 *
	 * @since 0.0.1
	 *
	 * @type {Vue}
	 */
	const MessageAdmin = new Vue({
		el: '#cf-pro-message-settings',
		data() {
			return {
				apiKey: CF_PRO_ADMIN.settings.apiKeys.public,
				apiSecret: CF_PRO_ADMIN.settings.apiKeys.secret,
				apiConnected: false,
				loaded: false,
				enhancedDeliveryAllowed: false,
				forms: CF_PRO_ADMIN.settings.forms,
				layouts: [],
				generatePDFs: false,
				enhancedDelivery: false,
				accountId: CF_PRO_ADMIN.settings.account_id,
				plan: 'basic',
				loading: false,
				accountActive: true,
				alert:{
					show: false,
					message: '',
					success: true
				}
			}

		},
		created() {
			this.getLayouts();
			this.testConnection();
			this.listen();
		},
		methods: {
			save() {
				return this.update({
					forms: this.forms,
					apiKey: this.apiKey,
					apiSecret: this.apiSecret,
					generatePDFs: this.generatePDFs,
					enhancedDelivery: this.enhancedDelivery,
					accountId: this.accountId
				}, true );

			},
			update( data, report ){
				this.loading = true;
				$.ajax(localApiURL, {
					method: 'POST',
					data:data ,
					beforeSend: function beforeSend(xhr) {
						xhr.setRequestHeader('X-WP-Nonce', localApiNonce);
					}
				}).then(
					r => {
						this.loading = false;
						if ( report ) {
							this.alert.show = false;
							window.setTimeout(() => {
								this.alert.message = CF_PRO_ADMIN.strings.saved;
								this.alert.success = true;
								this.alert.show = true;
							}, 250 );





						}
					}, e => {
						this.loading = false;
						if ( report ) {
							this.alert.show = false;
							window.setTimeout(() => {
								this.alert.message = CF_PRO_ADMIN.strings.notSaved;
								this.alert.success = false;
								this.alert.show = true;
							}, 250 );
						}
					}
				);

			},
			apiKeyChange() {
				if( ! this.apiKey || ! this.apiSecret ){
					return false;
				}

				this.testConnection();

			},
			getLayouts() {
				if( ! this.hasAuth() ){
					return false;
				}
				this.loading = true;
				let url = appURL + '/layouts/list?public=' + this.apiKey + '&token=' + sha1(this.apiKey + this.apiSecret) + '&simple=true';


				$.ajax(url,  {
					method: 'GET',
				}).then(
					r => {
						this.layouts = r;
						this.loading = false;
					}, e => {
						this.loading = false;
					}
				);

			},
			testConnection() {
				if( ! this.hasAuth() ){
					this.loaded = true;
					return false;
				}

				this.loading = true;
				$.ajax({
					method: "GET",
					data: {
						public: this.apiKey,
						token: sha1(this.apiKey + this.apiSecret),
					},
					url: appURL + '/account/verify'

				}).then( r => {
					this.accountId = r.id;
					if( ! r.active ){
						this.apiConnected = false;
						this.loading = false;
						this.loaded = true;
						this.accountActive = false;
						return;
					}
					this.accountActive = true;


					this.apiConnected = true;
					this.loaded = true;
					this.loading = false;
					this.plan = r.plan;
					if( 'apex' == this.plan ){
						this.enhancedDeliveryAllowed = true;
					}else{
						this.enhancedDeliveryAllowed = false;
					}
					this.getLayouts();
					return this.update({
						apiKey: this.apiKey,
						apiSecret: this.apiSecret,
						accountId: this.accountId
					}, false );
				}, e => {
					console.log(e);
					console.log('not connected...');
					this.apiConnected = false;
					this.loaded = true;
				});
			},
			listen() {
				this.$on('layoutChosen', function (data) {
					let index = this.forms.findIndex( e => {
						return e.form_id == data.form;
					});

					if ( -1 < index ) {
						this.forms[index][data.setting] = data.selected;
					}
				});

				this.$on('checkboxChanged', function (data) {
					console.log(data);
					let index = this.forms.findIndex( e => {
						return e.form_id == data.form;
					});

					if ( -1 < index ) {
						this.forms[index][data.setting] = data.value;
					}
				});
			},
			hasAuth() {
				if( ! this.apiKey || ! this.apiSecret ){
					this.apiConnected = false;

					return false;
				}
				return true;
			}
		}
	});

});
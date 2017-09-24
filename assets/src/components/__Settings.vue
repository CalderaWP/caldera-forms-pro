<template>
	<div>
		<div id="cf-pro-message-setting-inner">
			<div class="caldera-editor-header">
				<ul class="caldera-editor-header-nav">
					<li class="caldera-editor-logo">
				<span class="caldera-forms-name">
					Caldera Forms Pro: Message Settings
				</span>
					</li>
					<li class="cf-pro-notice-wrap">
						<status-indicator
								:success="alert.success"
								:message="alert.message"
								:show="alert.show"
						>
						</status-indicator>
					</li>
					<li v-if="!loaded || loading" class="cf-pro-spinner">
						<span class="spinner is-active "></span>
					</li>


				</ul>
			</div>
			<div id="cf-pro-admin-page-wrap" v-cloak>

				<form id="cf-pro-message-form" v-if="loaded" v-on:submit.prevent="save()">
					<div id="cf-pro-message-top-left">
						<h2>
							API Keys
						</h2>
						<div v-if="! apiConnected">
							<div class="cf-alert cf-alert-error ">
								<p>
									Not Connected To Caldera Forms Pro Server
								</p>
							</div>
						</div>

						<div v-if="apiConnected" class="cf-alert cf-alert-success ">
							<p>
								Connected To The Caldera Forms Pro Server
							</p>
						</div>

						<div class="caldera-config-field">
							<label for="cf-pro-api-key">
								API Key
							</label>
							<input
									id="cf-pro-api-key"
									type="text"
									class="field-config"
									v-model="apiKey"
									aria-describedby="cf-pro-api-key-description"
									v-on:change="apiKeyChange()"
							/>
							<p id="cf-pro-api-key-description" class="description">
								Your Caldera Forms Pro API Public Key
							</p>
						</div>

						<div class="caldera-config-field">
							<label for="cf-pro-api-secret">
								API Secret
							</label>
							<input
									id="cf-pro-api-secret"
									type="text"
									class="field-config"
									v-model="apiSecret"
									aria-describedby="cf-pro-api-secret-description"
									v-on:change="apiKeyChange()"
							/>
							<p id="cf-pro-api-secret-description" class="description">
								Your Caldera Forms Pro API Secret Key
							</p>
						</div>
					</div>
					<div id="cf-pro-message-top-right">
						<div class="postbox">
							<h2>Get Account Details</h2>
							<div class="inside">
								<div class="main">
									<h3>Have an account?</h3>
									<p>
										Get your API keys <a
											href="https://app.calderaformspro.com/app#/account">here</a>.
									</p>

									<h3>Need an account?</h3>

									<p>
										Start Your Free Trial Here <a href="https://calderaformspro.com/">here</a>.
										?>
									</p>
									<div v-if="! accountActive" class="cf-alert cf-alert-error">
										<p>Account exists but is not currently active.</p>
									</div>

								</div>
							</div>
						</div>
					</div>


					<div v-if="apiConnected">

						<div class="caldera-config-field">
							<label for="cf-pro-enhanced-delivery">
								Enable Enhanced Delivery
							</label>

							<input
									id="cf-pro-enhanced-delivery"
									type="checkbox"
									class="field-config"
									v-model="enhancedDelivery"
									value="1"
							/>

						</div>


						<h2>
							Form Settings
						</h2>

						<table class="table">
							<caption>Settings For Individual Forms
								<p class="description" v-if="'basic' !== plan">
									<a href="https://app.CalderaFormsPro.com" target="_blank">
										Create or edit layouts in the app.
									</a>
								</p>
							</caption>
							<thead>
							<tr>
								<th scope="col">Form</th>
								<th scope="col">Email Layout</th>
								<th scope="col">PDF Layout</th>
								<th scope="col">Attach PDF</th>
								<th scope="col">Add PDF Link</th>
							</tr>
							</thead>
							<tbody>
							<tr v-for="form in forms" v-if="layouts">
								<td scope="row">{{formSettings.vue}}</td>
								<td>
									<layout-chooser
											:layouts="layouts"
											:form="form"
											:setting="'layout'"
									>
									</layout-chooser>
								</td>
								<td>
									<layout-chooser
											:layouts="layouts"
											:form="form"
											:setting="'pdf_layout'"
									>
									</layout-chooser>
								</td>
								<td>
									<checkbox-setting
											:form="form"
											:setting="'attach_pdf'"
											:label="'Attach PDF '"
											:selected="form.attach_pdf"
									></checkbox-setting>
								</td>

								<td>
									<checkbox-setting
											:form="form"
											:setting="'pdf_link'"
											:label="'Add PDF Link '"
											:selected="form.pdf_link"
									></checkbox-setting>
								</td>

							</tr>
							</tbody>
						</table>
						<input type="submit" value="Save" class="button button-primary button-large" />
					</div>
				</form>
			</div>
		</div>
	</div>

</template>
<script>
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

	import sha1 from '../sha1.js';
	import Checkbox from './Checkbox.vue';
	import LayoutChooser from './LayoutChooser.vue';

	export default{
		el: '#cf-pro-message-settings',
		components : {
			'layout-chooser' : LayoutChooser,
			'checkbox-setting' : Checkbox
		},
		data() {
			return {
				apiKey: CF_PRO_ADMIN.settings.apiKeys.public,
				apiSecret: CF_PRO_ADMIN.settings.apiKeys.secret,
				apiConnected: false,
				loaded: false,
				enhancedDeliveryAllowed: true,
				forms: CF_PRO_ADMIN.settings.forms,
				layouts: [],
				generatePDFs: false,
				enhancedDelivery: CF_PRO_ADMIN.settings.enhancedDelivery,
				accountId: CF_PRO_ADMIN.settings.account_id,
				plan: 'basic',
				loading: false,
				accountActive: true,
				alert: {
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
					accountId: this.accountId,
					plan: this.plan,
				}, true);

			},
			update(data, report){
				this.loading = true;
				if (this.accountActive && this.apiConnected) {
					data.activate = true;
				} else {
					data.activate = false;
				}
				jQuery.ajax(CFProConfig.localApiURL, {
					method: 'POST',
					data: data,
					beforeSend: function beforeSend(xhr) {
						xhr.setRequestHeader('X-WP-Nonce', CFProConfig.localApiNonce);
					}
				}).then(
						r => {
							this.loading = false;
							if (report) {
								this.alert.show = false;
								window.setTimeout(() => {
									this.alert.message = CF_PRO_ADMIN.strings.saved;
									this.alert.success = true;
									this.alert.show = true;
								}, 250);


							}
						}, e => {
							this.loading = false;
							if (report) {
								this.alert.show = false;
								window.setTimeout(() => {
									this.alert.message = CF_PRO_ADMIN.strings.notSaved;
									this.alert.success = false;
									this.alert.show = true;
								}, 250);
							}
						}
				);

			},
			apiKeyChange() {
				if (!this.apiKey || !this.apiSecret) {
					return false;
				}

				this.testConnection();

			},
			getLayouts() {
				if (!this.hasAuth()) {
					return false;
				}
				this.loading = true;
				let url = CFProConfig.appURL + '/layouts/list?public=' + this.apiKey + '&token=' + sha1(this.apiKey + this.apiSecret) + '&simple=true';


				jQuery.ajax(url, {
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
				if (!this.hasAuth()) {
					this.loaded = true;
					return false;
				}

				this.loading = true;
				jQuery.ajax({
					method: "GET",
					data: {
						plan: this.plan,
						public: this.apiKey,
						token: sha1(this.apiKey + this.apiSecret),
					},
					url: CFProConfig.appURL + '/account/verify'

				}).then(r => {
					this.accountId = r.id;
					if (!r.active) {
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


					this.getLayouts();
					return this.update({
						apiKey: this.apiKey,
						apiSecret: this.apiSecret,
						accountId: this.accountId
					}, false);
				}, e => {
					console.log(e);
					console.log('not connected...');
					this.apiConnected = false;
					this.loaded = true;
				});
			},
			listen() {
				this.$on('layoutChosen', function (data) {
					let index = this.forms.findIndex(e => {
						return e.form_id == data.form;
					});

					if (-1 < index) {
						this.forms[index][data.setting] = data.selected;
					}
				});

				this.$on('checkboxChanged', function (data) {
					let index = this.forms.findIndex(e => {
						return e.form_id == data.form;
					});

					if (-1 < index) {
						this.forms[index][data.setting] = data.value;
					}
				});
			},
			hasAuth() {
				if (!this.apiKey || !this.apiSecret) {
					this.apiConnected = false;

					return false;
				}
				return true;
			}
		}
	}
</script>
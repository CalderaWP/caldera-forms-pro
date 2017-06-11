<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;

}
?>
<div id="cf-pro-message-settings">
	<div id="cf-pro-message-setting-inner">
		<div class="caldera-editor-header">
			<ul class="caldera-editor-header-nav">
				<li class="caldera-editor-logo">
				<span class="caldera-forms-name">
					<?php esc_html_e( 'Caldera Forms Pro: Message Settings', 'caldera-forms-pro' ); ?>
				</span>
				</li>
				<li>
					<button class="caldera-header-save-button button button-primary button-large" id="cf-pro-save-header" v-on:click.prevent="save()">
						<?php esc_html_e( 'Save', 'caldera-forms-pro' ); ?>
					</button>
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
						<?php esc_html__( 'API Keys', 'caldera-forms-pro' ); ?>
					</h2>
					<div v-if="! apiConnected">
						<div class="cf-alert cf-alert-error ">
							<p>
								<?php esc_html_e( 'Not Connected To Caldera Forms Pro Server', 'caldera-forms-pro' ); ?>
							</p>
						</div>
					</div>

					<div v-if="apiConnected" class="cf-alert cf-alert-success ">
						<p>
							<?php esc_html_e( 'Connected To The Caldera Forms Pro Server', 'caldera-forms-pro' ); ?>
						</p>
					</div>

					<div class="caldera-config-field">
						<label for="cf-pro-api-key">
							<?php esc_html_e( 'API Key', 'caldera-forms-pro' ); ?>
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
							<?php esc_html_e( 'Your Caldera Forms Pro API Public Key', 'caldera-forms-pro' ); ?>
						</p>
					</div>

					<div class="caldera-config-field">
						<label for="cf-pro-api-secret">
							<?php esc_html_e( 'API Secret', 'caldera-forms-pro' ); ?>
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
							<?php esc_html_e( 'Your Caldera Forms Pro API Secret Key', 'caldera-forms-pro' ); ?>
						</p>
					</div>
				</div>
				<div id="cf-pro-message-top-right">
					<div class="postbox">
						<h2><?php esc_html_e( 'Get Account Details', 'caldera-forms-pro' ); ?></h2>
						<div class="inside">
							<div class="main">
								<h3><?php esc_html_e( 'Have an account?', 'caldera-forms-pro' ); ?></h3>

								<p>
									<?php
									printf( '%s %s.', esc_html__( 'Get your API keys', 'caldera-forms-pro' ), sprintf( '<a href="https://app.calderaformspro.com/app#/account">%s</a>', esc_html__( 'here', 'caldera-forms-pro' ) ) );
									?>
								</p>

								<h3><?php esc_html_e( 'Need an account?', 'caldera-forms-pro' ); ?></h3>

								<p>
									<?php
									printf( '%s %s.', esc_html__( 'Start Your Free Trial Here', 'caldera-forms-pro' ), sprintf( '<a href="https://calderaformspro.com/">%s</a>', esc_html__( 'here', 'caldera-forms-pro' ) ) );
									?>
								</p>
								<div v-if="! accountActive" class="cf-alert cf-alert-error">
									<p><?php esc_html_e( 'Account exists but is not currently active.', 'caldera-forms-pro' ); ?></p>
								</div>

							</div>
						</div>
					</div>
				</div>


				<div v-if="apiConnected">

					<div class="caldera-config-field">
						<label for="cf-pro-enhanced-delivery">
							<?php esc_html_e( 'Enable Enhanced Delivery', 'caldera-forms-pro' ); ?>
						</label>
						<div v-if="! enhancedDeliveryAllowed" class="cf-alert cf-alert-error">
							<p>
								<?php esc_html_e( 'Your Caldera Forms Pro Plan Does Not Support Enhanced Delivery.', 'caldera-forms-pro' ); ?>
							</p>
						</div>

							<input
								id="cf-pro-enhanced-delivery"
								type="checkbox"
								class="field-config"
								v-model="enhancedDelivery"
								v-if="enhancedDeliveryAllowed"
								value="1"
							/>

					</div>


					<h2>
						<?php esc_html_e( 'Form Settings', 'caldera-forms-pro' ); ?>
					</h2>

					<table class="table">
						<caption>Settings For Individual Forms
							<p class="description" v-if="'basic' == plan">
								<?php esc_html_e( 'Your plan does not support the layout builder', 'caldera-forms-pro' ); ?>
							</p>
							<p class="description" v-if="'basic' !== plan">
								<a href="https://app.CalderaFormsPro.com" target="_blank">
									<?php esc_html_e( 'Create or edit layouts in the app.', 'caldera-forms-pro' ); ?>
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
								<td scope="row">{{form.name}}</td>
								<td>
									<layout-chooser
										form="form.ID"
										:layouts="layouts"
										:form="form"
										:setting="'layout'"
										:disabled="'basic' == plan"
									>
									</layout-chooser>
								</td>

								<td>
									<layout-chooser
										form="form.ID"
										:layouts="layouts"
										:form="form"
										:setting="'pdf_layout'"
									    :disabled="'basic' == plan"
									>
									</layout-chooser>
								</td>
								<td>
									<checkbox-setting
										:form="form" :setting="'attach_pdf'"
										:label="'<?php esc_attr_e( 'Attach PDF ', 'caldera-forms-pro' ); ?>'"
									></checkbox-setting>
								</td>
								<td>
								<td>
									<checkbox-setting
										:form="form" :setting="'pdf_link'"
										:label="'<?php esc_attr_e( 'Add PDF Link ', 'caldera-forms-pro' ); ?>'"
									></checkbox-setting>
								</td>

							</tr>
						</tbody>
					</table>
					<?php echo get_submit_button( 'Save','primary large', 'cf-pro-save' ); ?>
				</div>
			</form>
		</div>
	</div>
</div>


<script id="cf-pro-layout-chooser" type="text/html">
	<div class="caldera-config-field">
		<label v-bind:for="idAttr(form.form_id)" class="screen-reader-text">
			<?php esc_html_e( 'Choose Layout For ', 'caldera-forms-pro' ); ?> {{setting}}
		</label>
		<select
			v-bind:id="idAttr(form.form_id)"
			v-on:change="layoutChanged"
			v-model="selected"
			v-bind:disabled="disabled"
		>
			<option v-for="option in layouts" v-bind:value="option.id">
				{{ option.name }}
			</option>
		</select>
	</div>
</script>

<script id="cf-pro-checkbox" type="text/html">
	<span>
		<label v-bind:for="idAttr(form.form_id)" class="screen-reader-text">
			{{label}}
		</label>
		<input
			type="checkbox"
			v-bind:id="idAttr(form.form_id)"
			v-model="selected"
			v-on:change="changed"
			value="1"
		/>
	</span>

</script>
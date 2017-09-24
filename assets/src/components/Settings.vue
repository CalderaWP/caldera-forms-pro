<template>
	<div>
		<div id="cf-pro-message-setting-inner">
			<ul>
				<li>{{apiPublic}}</li>
				<li>{{apiSecret}}</li>
			</ul>
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
	import { mapState } from 'vuex'


	export default{
		el: '#cf-pro-message-settings',
		components: {
			'layout-chooser': LayoutChooser,
			'checkbox-setting': Checkbox
		},
		computed : mapState({
			apiPublic: state => state.account.apiKeys.public,
			apiSecret: state => state.account.apiKeys.secret

		}),
		beforeMount(){
			this.$state.dispatch('xxx')
		}

	}
</script>
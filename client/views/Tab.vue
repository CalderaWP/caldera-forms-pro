<template>
	<div id="cf-pro-form-settings">
		<div v-if="connected">
			<form-setting :form="form" :layouts="layouts"></form-setting>
		</div>
		<div v-if="!connected">
			Not Connected
		</div>
		<div id="cf-pro-alert-wrap">
			<status
					:message="mainAlert.message"
					:success="mainAlert.success"
					:show="mainAlert.show"
			>
			</status>
		</div>
	</div>
</template>
<script>
	import { mapState } from 'vuex'
	import { mapActions } from 'vuex'

	import debounce from 'lodash.debounce';
	import Status from '../components/Elements/Status/Status';

	import  formSetting from '../components/FormSettings/Form.vue';
	export default{
		components: {
			'form-setting' : formSetting
		},
		methods: {
			...mapActions([
				'getLayouts',
				'getAccount',
				'saveAccount'

			]),
		},
		beforeMount(){
			this.getAccount().then( () => {
				this.getLayouts();
				this.form = this.$store.getters.getFormsById( this.formScreen );
			});

		},
		computed: mapState({
			layouts: state => state.layouts,
			connected: state => state.connected,
			formScreen: state => state.formScreen,
			mainAlert: state => state.mainAlert
		}),
		data(){
			return{
				form: {}
			}
		}


	}
</script>
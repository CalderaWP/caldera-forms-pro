<template>
	<div id="cf-pro-message-settings">
		<div id="cf-pro-message-setting-inner">
			<div class="caldera-editor-header">
				<ul class="caldera-editor-header-nav">
					<li class="caldera-editor-logo">
						<span class="caldera-forms-name">
							Caldera Forms Pro
						</span>
					</li>
					<li class="status good" v-if="connected">
						Connected
					</li>
					<li class="status bad" v-if="!connected">
						Not Connected
					</li>
					<li class="cf-pro-save">
						<input type="submit" class="button button-primary" value="Save" @click="save"/>
					</li>
					<li id="cf-pro-alert-wrap">
						<status
							:message="mainAlert.message"
							:success="mainAlert.success"
							:show="mainAlert.show"
							>
						</status>
					</li>
				</ul>
			</div>
			<div class="cf-pro-settings" v-cloak>
				<div>
					<tabs :options="{ useUrlFragment: false }">
						<tab name="Account">
							<account-edit></account-edit>
						</tab>
						<tab name="Form Settings">
							<div v-if="connected">
								<forms-settings></forms-settings>
							</div>
							<div v-else>
								You must connected to Caldera Forms Pro First
							</div>
						</tab>
						<tab name="Settings">
							<delivery></delivery>
						</tab>
					</tabs>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';
	import AccountDisplay from '../components/Account/display';
	import AccountEdit from '../components/Account/Edit';
	import FormsSettings from '../components/FormSettings/Forms';
	import enhancedDelivery from '../components/GeneralSettings/enhancedDelivery';
	import Status from '../components/Elements/Status/Status.vue'
	export default{
		components :{
			'account-display': AccountDisplay,
			'account-edit' : AccountEdit,
			'forms-settings' : FormsSettings,
			'delivery' : enhancedDelivery,
			'status' : Status
		},
		computed: mapState({
			loading: state => state.loading,
			connected: state => state.connected,
			publicKey: state => state.account.apiKeys.public,
			enhancedDelivery: state => state.settings.enhancedDelivery,
			mainAlert: state => state.mainAlert
		}),
		beforeMount(){
			[].forEach.call(document.querySelectorAll('.update-nag'),function(e){
				e.parentNode.removeChild(e);
			});

			this.$store.dispatch( 'getAccount' );
		},
		methods:{
			save(){
				this.$store.dispatch( 'saveAccount' );
			}
		}
	}
</script>
<style>
	[v-cloak] {
		display: none;
		visibility: hidden;
	}

	.cf-pro-settings {
		margin-top: 50px;
	}

	.cf-pro-save input.button {
		margin: 10px;
	}

	li.status {
		padding: 14px !important;
		color: white;
	}

	li.status.good {
		background-color: #0b7a6f;
	}

	li.status.bad {
		background-color: red;
	}


	.tabs-component-panels,ul.tabs-component-tabs {
		float: left;
		display:inline-block
	}

	.tabs-component-panels{
		padding:16px;
	}
	ul.tabs-component-tabs{
		background: #0b7a6f;

	}
	li.tabs-component-tab {
		padding:8px 4px;
		text-align:center;

	}
	li.tabs-component-tab a{
		color: white;
	}
	li.tabs-component-tab.is-active{
		background-color: #ff7e30;
	}



</style>
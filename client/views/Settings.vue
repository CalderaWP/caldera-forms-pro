<template>
	<div class="cf-pro-settings">
		<div style="border:1px solid red;min-height: 10px">
			{{connected}}
		</div>
		<div v-if="loading">Spinner</div>
		<div v-if="connected">Connected</div>
		<account-display></account-display>
		<account-edit></account-edit>
		<div v-if="connected">
			<h3>Forms</h3>
			<form-settings></form-settings>
		</div>
	</div>

</template>
<script>
	import { mapState, mapGetters, mapActions, mapMutations } from 'vuex';
	import AccountDisplay from '../components/Account/display';
	import AccountEdit from '../components/Account/Edit';
	import FormSettings from '../components/FormSettings/Forms';
	export default{
		components :{
			'account-display': AccountDisplay,
			'account-edit' : AccountEdit,
			'form-settings' : FormSettings
		},
		computed: mapState({
			loading: state => state.loading,
			connected: state => state.connected,
		}),
		beforeMount(){
			this.$store.dispatch( 'getAccount' );
		},
		methods:{
			save(){
				this.$store.dispatch( 'saveAccount' );
			}
		}
	}
</script>
<template>
	<div id="cf-pro-account-edit">
		<text-input
				:value="publicKey"
				:setting="'publicKey'"
				:label="'Public Key'"
				:idAttr="'cf-pro-api-public'"
		/>
		<text-input
				:value="secretKey"
				:setting="'secretKey'"
				:label="'Secret Key'"
				:idAttr="'cf-pro-api-secret'"
		/>
	</div>
</template>
<script>
	import { mapState, mapGetters, mapActions, mapMutations } from 'vuex'
	import  TextInput from '../Elements/Field/Text.vue';
	export default{
		components: {
			'text-input': TextInput
		},
		computed: mapState({
			account: state => state.account,
			publicKey: state => state.account.apiKeys.public,
			secretKey: state => state.account.apiKeys.secret,
		}),
		watch: {
			publicKey(){
				this.save();
			},
			secretKey(){
				this.save();
			}
		},
		methods:{
			save(){
				this.$store.dispatch( 'saveAccount' );
			}
		}
	}
</script>
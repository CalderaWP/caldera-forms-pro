<template>
	<div class="caldera-config-group">
		<label v-bind:for="cf-pro-enhanced-delivery">
			Enable Enhanced Delivery
		</label>
		<div class="caldera-config-field">
			<input
					type="checkbox"
					v-model="enhancedDelivery"
					id="cf-pro-enhanced-delivery"
					@change="changed"
					aria-describedby="cf-pro-enhanced-delivery-desc"
			/>
			<div class="description" id="cf-pro-enhanced-delivery-desc">
				<p>If enabled, messages are sent via Caldera Forms Pro Servers</p>
				<ul v-if="enhancedDelivery">
					<li>Forms must use database entry tracking.</li>
					<li>From Email setting will be used as the reply-to.</li>
				</ul>
			</div>
		</div>
	</div>
</template>
<script>
	import { mapGetters } from 'vuex';
	export default{
		computed: mapGetters({
			enhancedDelivery: state => state.settings.enhancedDelivery
		}),
		methods: {
			changed(v){
				this.$store.commit( 'enhancedDelivery', v );
			}
		}

	}
</script>
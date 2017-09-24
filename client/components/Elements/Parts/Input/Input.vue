<template>
	<input
			v-bind:type="type"
			v-bind:id="idAttr"
			v-model="value"
			@keyup="handle()"
			@change="handle()"
	/>
</template>
<script>
	export default{
		props: {
			idAttr: {
				type: String,
				require: true
			},
			value:{
				type:String,
				required: true
			},
			setting:{
				type:String,
				required: true
			},
			type: {
				type: String,
				default: 'text'
			}
		},
		methods: {
			handle(){
				this.$store.commit(this.setting,this.value);
			}
		},

	}
</script>
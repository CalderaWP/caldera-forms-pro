<template>
	<div class="caldera-config-field">
		<cf-label
				:idBase="idBase"
				:setting="setting"
				:label="label"
				:screenReader="true"
		>
		</cf-label>
		<input
				type="checkbox"
				v-bind:id="idAttr()"
				v-model="selected"
				v-on:change="changed"
				value="1"
		/>
	</div>
</template>
<script>
	import Label from './Label.vue';
	import element from './element';
	export default {
		props : element.generateProps( [
				'form',
				'setting',
				'label',

		]),
		components: {
			'cf-label': Label
		},
		methods: {
			idAttr: function (something) {
				return something;
			},
			changed: function (v) {
				this.$parent.$emit( 'checkboxChanged', {
					form: this.form.form_id,
					setting: this.setting,
					value: v
				});
			}
		},
		watch: {
			selected(v){
				this.changed(v);
				return v;
			}
		},
		data() {
			return{
				//selected: this.$store.state.getSetting( this.form.form_id ),
				idBase: this.form.form_id
			}
		},

	}
</script>
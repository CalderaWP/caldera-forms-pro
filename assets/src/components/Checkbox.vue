<template>
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
</template>
<script>
	export default {
		props: [
			'form',
			'setting',
			'label'
		],
		methods: {
			idAttr: function (formId) {
				return 'cf-pro-' + this.setting + '-' + formId;
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
				selected: this.form[this.setting]
			}
		},

	}
</script>
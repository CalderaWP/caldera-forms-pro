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
			changed: function (e) {
				let selected = $( e.target ).prop( 'checked' );
				this.selected = selected;
				this.$parent.$emit( 'checkboxChanged', {
					form: this.form.form_id,
					setting: this.setting,
					value: selected
				});
			}
		},
		computed: {
			selected(){
				let selected = this.form[this.setting];
				return this.form[this.setting];
			}
		}

	}
</script>
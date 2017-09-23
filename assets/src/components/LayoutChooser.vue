<template>
	<div class="caldera-config-field">
		<label v-bind:for="idAttr(form.form_id)" class="screen-reader-text">
			Choose Layout For {{setting}}
		</label>
		<select
				v-bind:id="idAttr(form.form_id)"
				v-model="selected"
				v-bind:disabled="disabled"
		>
			<option v-for="option in layouts" v-bind:value="option.id">
				{{ option.name }}
			</option>
		</select>
	</div>
</template>
<script>
	export default{
		props: [
			'form',
			'layouts',
			'setting',
			'disabled'
		],
		methods: {
			idAttr: function (formId) {
				return 'cf-pro-choose-template-' + formId;
			},
			changed(v){
				this.$parent.$emit( 'layoutChosen', {
					form: this.form.form_id,
					selected: v,
					setting: this.setting
				});
			}
		},
		data() {
			return{
				selected: this.form[this.setting]
			}
		},
		watch: {
			selected(v){
				this.changed(v);
				return v;
			}
		}

	}
</script>
<template>
	<div class="caldera-config-field">
		<label v-bind:for="idAttr(form.form_id)" class="screen-reader-text">
			Choose Layout For {{setting}}
		</label>
		<select
				v-bind:id="idAttr(form.form_id)"
				v-on:change="layoutChanged"
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
			layoutChanged: function(e) {
				let selected = jQuery( e.target ).val();
				this.$parent.$emit( 'layoutChosen', {
					form: this.form.form_id,
					selected: selected,
					setting: this.setting
				});

			},
			idAttr: function (formId) {
				return 'cf-pro-choose-template-' + formId;
			}
		},
		computed: {
			selected(){
				return this.form[this.setting];
			}
		}
	}
</script>
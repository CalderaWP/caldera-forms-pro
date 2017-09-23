<template>
	<div class="caldera-config-field">
		<cf-label
				:idBase="form.form_id"
				:setting="setting"
				:label="label"
		>
		</cf-label>
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
	import Label from './Label.vue';
	import  ucwords from 'locutus/php/strings/ucwords';
	import  element from './element';
	export default{
		props: element.generateProps(
				[
					'form',
					'layouts',
					'setting',
				],
				{

					disabled: {
						required: false,
						default:false
					}
				}
		),
		components: {
			'cf-label': Label
		},
		methods: {
			idAttr: function (something) {
				return something;
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
			//	settingUC: ucwords(setting),
				selected: this.form[this.setting],
				//@TODO Translations.
				label: `Choose Layout For ${this.setting}`
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
<template>
	<span>
		<cf-label
				:idBase="form.form_id"
				:setting="setting"
				:label="label"
				:screenReader="true"
		>
		</cf-label>
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
	import Label from './Label.vue';
	import element from './element';
	export default {
		props : element.generateProps( [
				'forms',
				'setting',
				'label'
		]),
		render: function (h) {
			return createElement('h1', this.blogTitle)
		},
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
				selected: this.form[this.setting]
			}
		},

	}
</script>
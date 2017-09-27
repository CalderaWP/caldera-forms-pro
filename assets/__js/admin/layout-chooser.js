/**
 * The template for the layout chooser component
 *
 * @since 0.0.1
 *
 * @type {string}
 */
const layoutChooserTemplate = document.getElementById('cf-pro-layout-chooser').innerHTML;

/**
 * Creates a reusable layout chooser select
 *
 * @since 0.0.1
 */
const layoutChooser = {
	template: layoutChooserTemplate,
	props: [
		'form',
		'layouts',
		'setting',
		'disabled'
	],
	methods: {
		layoutChanged: function(e) {
			let selected = $( e.target ).val();
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
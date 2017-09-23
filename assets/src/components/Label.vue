<script>
	import element from './element';
	export default{
		render(h) {
			return h(
					'label', {
						'class': {
							'screen-reader-text': this.screenReader,
							'cf-pro-label' : true
						},
						attrs: {
							id: this.idAttr( this.idBase )
						}
					},
					this.label
			);
		},
		props: element.generateProps( [
				'label',
				'idBase',
				'setting',
				'screenReader'
		]),
		methods: {
			idAttr: function (something) {
				return something;
			},
		}
	}
</script>
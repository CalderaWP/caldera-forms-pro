import Props from './Props/factory';
import idAttr from  './Methods/idAttr';

export  default {
	generateProps: (propsArray, defaultsObject) => {
		return Props.generate(propsArray, defaultsObject);
	},
	props: Props,
	methods: {
		idAttr: idAttr
	}
};
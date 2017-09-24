const nonReqObj = {
	type: Object,
	default: {}
};

module.exports =  {
	formId: {
		type: String,
		required: true,
	},
	label : {
		type: String,
		required: true
	},
	idBase : {
		type: String,
		required: true
	},
	setting : {
		type: String,
		required: true
	},
	screenReader :{
		type: Boolean,
		default: false
	},
	form : {
		type: Object,
		default: {
			form_id: 'pants'
		}
	},
	layouts: nonReqObj
};
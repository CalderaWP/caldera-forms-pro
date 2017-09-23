const nonReqObj = {
	type: Object,
	default: {}
};

module.exports =  {
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
	form : nonReqObj,
	layouts: nonReqObj
};
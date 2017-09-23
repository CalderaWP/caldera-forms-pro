'use strict';
import Props from './props';
/**
 *
 * @param {Array}propsArray
 * @param {Object} defaultsObject
 * @returns {*}
 */
export  default {
	generate : function (propsArray, defaultsObject ) {
		let props = propsArray.filter( prop => {
			return Props.hasOwnProperty( prop );
		}).map(
			prop => {
				return Props[prop]
			}
		);
		if( ! defaultsObject ){
			return props;
		}
		return Object.assign(defaultsObject,props );
	}
};

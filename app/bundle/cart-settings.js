/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue.extend({

	    data: function () {
	        return window.$data;
	    },

	    fields: __webpack_require__(26),

	    methods: {

	        save: function () {
	            this.$http.post('admin/system/settings/config', { name: 'bixie/cart', config: this.config }, function () {
	                this.$notify('Settings saved.');
	            }).error(function (data) {
	                this.$notify(data, 'danger');
	            });
	        }
	    },

	    computed: {
	        vatOptions: function () {
	            var options = [];
	            _.forIn(this.config.vatclasses, function (vatclass, value) {
	                options.push({value: value, text: vatclass.name});
	            });
	            return options;
	        }
	    }

	});

	Vue.field.templates.formrow = __webpack_require__(28);
	Vue.field.templates.raw = __webpack_require__(29);
	Vue.field.types.checkbox = '<p class="uk-form-controls-condensed"><label><input type="checkbox" v-attr="attrs" v-model="value"> {{ optionlabel | trans }}</label></p>';
	Vue.field.types.number = '<input type="number" v-attr="attrs" v-model="value" number>';
	Vue.field.types.title = '<h3 v-attr="attrs">{{ title | trans }}</h3>';

	$(function () {

	    (new module.exports()).$mount('#cart-settings');

	});


/***/ },

/***/ 26:
/***/ function(module, exports, __webpack_require__) {

	
	var options = __webpack_require__(27);

	module.exports = {
	    cart: {
	        'currency': {
	            type: 'select',
	            label: 'Default currency',
	            options: {
	                'Euro': 'EUR',
	                'Dollar': 'USD'
	            },
	            attrs: {'class': 'uk-form-width-medium'}
	        },
	        'vat_view': {
	            type: 'select',
	            label: 'VAT display',
	            options: {
	                'Show prices including VAT': 'incl',
	                'Show prices excluding VAT': 'excl'
	            },
	            attrs: {'class': 'uk-form-width-medium'}
	        },
	        'title1': {
	            raw: true,
	            type: 'title',
	            label: '',
	            title: 'Add to cart buttons',
	            attrs: {'class': 'uk-margin-top'}
	        },
	        'show_vat': {
	            type: 'checkbox',
	            label: 'VAT',
	            optionlabel: 'Show VAT amount'
	        }
	    },
	    general: {
	        'orders_per_page': {
	            type: 'number',
	            label: 'Orders per page',
	            attrs: {'class': 'uk-form-width-small'}
	        },
	        'server_tz': {
	            type: 'select',
	            label: 'Server timezone',
	            options: options.timezoneOptions(),
	            attrs: {'class': 'uk-form-width-medium'}
	        },
	        'validation_key': {
	            label: 'Validation key',
	            attrs: {'class': 'uk-form-width-large'}
	        },
	        'USDtoEUR': {
	            type: 'number',
	            label: 'USD to EUR conversion rate',
	            attrs: {'class': 'uk-form-width-small uk-text-right'}
	        },
	        'EURtoUSD': {
	            type: 'number',
	            label: 'EUR to USD conversion rate',
	            attrs: {'class': 'uk-form-width-small uk-text-right'}
	        }

	    }


	};

/***/ },

/***/ 27:
/***/ function(module, exports) {

	module.exports = {


	    timezoneOptions: function () {
	        var options = {};
	        _.forIn(window.$data.timezones, function (zones, continent) {
	            options[continent] = (function (zones) {
	                var zoneOptions = {};
	                _.forIn(zones, function (zone, code) {
	                    zoneOptions[zone] = code;
	                });
	                return zoneOptions;
	            }(zones));
	        });
	        return options;
	    }

	};

/***/ },

/***/ 28:
/***/ function(module, exports) {

	module.exports = "<div v-repeat=\"field in fields\" v-class=\"uk-form-row: !field.raw\">\r\n    <label v-if=\"field.label\" class=\"uk-form-label\">{{ field.label | trans }}</label>\r\n    <div v-if=\"!field.raw\" class=\"uk-form-controls\" v-class=\"uk-form-controls-text: ['checkbox', 'radio'].indexOf(field.type)>-1\">\r\n        <field config=\"{{ field }}\" values=\"{{@ values }}\"></field>\r\n    </div>\r\n    <field v-if=\"field.raw\" config=\"{{ field }}\" values=\"{{@ values }}\"></field>\r\n</div>\r\n";

/***/ },

/***/ 29:
/***/ function(module, exports) {

	module.exports = "<template v-repeat=\"field in fields\">\r\n    <field config=\"{{ field }}\" values=\"{{@ values }}\"></field>\r\n</template>\r\n";

/***/ }

/******/ });
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
/******/ ([
/* 0 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = Vue.extend({

	    data: function () {
	        return _.merge({
	            'config': {},
	            'products': []
	        }, window.$cart);
	    },

	    methods: {
	    },

	    components: {

	        'addtocart': __webpack_require__(4)

	    }

	});

	__webpack_require__(7)(Vue);

	$(function () {

	    (new module.exports()).$mount('.bixie-addtocart');

	});


/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(5)
	module.exports.template = __webpack_require__(6)


/***/ },
/* 5 */
/***/ function(module, exports) {

	module.exports = {

	        props: ['product', 'item_id'],

	        data: function () {
	            return {
	                config: window.$cart.config
	            }
	        },

	        created: function () {
	        },

	        computed: {
	            fileName: function () {
	                return this.file.split('/').pop();
	            }
	        },

	        methods: {
	            addToCart: function (product) {
	                window.$bixieCart.$emit('add.bixie.cart', product);
	            }
	        }

	    };

	    Vue.component('addtocart', function (resolve, reject) {
	        Vue.asset({
	            js: [
	                'app/assets/uikit/js/components/upload.min.js',
	                'app/system/modules/finder/app/bundle/panel-finder.js'
	            ]
	        }, function () {
	            resolve(module.exports);
	        })
	    });

/***/ },
/* 6 */
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-grid uk-grid-small uk-flex-middle uk-margin\" data-uk-margin=\"\">\r\n        <div class=\"uk-width-medium-1-3 uk-text-right\">{{{ product.price | currency }}}</div>\r\n        <div class=\"uk-width-medium-2-3\">\r\n            <button class=\"uk-button uk-button-success\" v-on=\"click: addToCart(product)\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'Add to cart' | trans }}\r\n            </button>\r\n        </div>\r\n\r\n\r\n    </div>";

/***/ },
/* 7 */
/***/ function(module, exports) {

	module.exports = function (Vue) {
	    var icons = {
	        'EUR': 'uk-icon-euro',
	        'USD': 'uk-icon-dollar'
	    };

	    function number_format(number, decimals, dec_point, thousands_sep) {
	        //  discuss at: http://phpjs.org/functions/number_format/
	        number = (number + '')
	            .replace(/[^0-9+\-Ee.]/g, '');
	        var n = !isFinite(+number) ? 0 : +number,
	            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
	            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
	            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
	            s = '',
	            toFixedFix = function(n, prec) {
	                var k = Math.pow(10, prec);
	                return '' + (Math.round(n * k) / k)
	                        .toFixed(prec);
	            };
	        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
	            .split('.');
	        if (s[0].length > 3) {
	            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	        }
	        if ((s[1] || '')
	                .length < prec) {
	            s[1] = s[1] || '';
	            s[1] += new Array(prec - s[1].length + 1)
	                .join('0');
	        }
	        return s.join(dec);
	    }

	    Vue.filter('datetime', function (date) {
	        if (typeof date === 'string') {
	            date = new Date(date);
	        }
	        return date ? this.$date(date, 'mediumDate') + ', ' + this.$date(date, 'HH:mm:ss') : '';
	    });

	    Vue.filter('currency', function (price, currency) {
	        var icon = '<i class="' + (icons[(currency || window.$cart.config.currency)] || icons.EUR) + ' uk-margin-small-right"></i>', numberString;
	        try {
	            numberString = Number(price).toLocaleString(window.$locale.locale.replace('_', '-'), {minimumFractionDigits: 2});
	        } catch (ignore) {
	            numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, $locale.NUMBER_FORMATS.GROUP_SEP);
	        }
	        return icon + numberString;
	    });
	};

/***/ }
/******/ ]);
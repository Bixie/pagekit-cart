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

	var Cart = Vue.extend(__webpack_require__(8));

	__webpack_require__(7)(Vue);

	$(function () {

	    window.$bixieCart = (new Cart()).$appendTo('body');

	});

	module.exports = Cart;

/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
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

	    Vue.filter('currency', function (price, product) {

	        var siteCurrency = window.$bixieCart.currency || 'EUR',
	            icon = '<i class="' + icons[siteCurrency] + ' uk-margin-small-right"></i>',
	            numberString;

	        if (product && product.currency !== siteCurrency) {
	            price = window.$bixieCart.convertPrice(price, product);
	        }

	        try {
	            numberString = Number(price).toLocaleString(window.$locale.locale.replace('_', '-'), {minimumFractionDigits: 2});
	        } catch (ignore) {
	            numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
	        }
	        return icon + numberString;
	    });
	};

/***/ },
/* 8 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(9)
	module.exports.template = __webpack_require__(10)


/***/ },
/* 9 */
/***/ function(module, exports) {

	module.exports = {

	        el: function () {
	            return document.createElement('div');
	        },

	        data: function () {
	            return {
	                'currency': window.$cart.config.currency,
	                'config': window.$cart.config,
	                'cartItems': _.merge([], window.$cartItems)
	            };
	        },

	        created: function () {
	            this.resource = this.$resource('api/cart/cart/:id');
	            this.$on('add.bixie.cart', function (product) {
	                this.addToCart(product);
	            });
	            this.$on('show.bixie.cart', function () {
	                this.$.cartmodal.open();
	            });
	        },

	        computed: {
	            totalNetto: function () {
	                var total = 0;
	                this.cartItems.forEach(function (cartItem) {
	                    total += this.convertPrice(cartItem.price, cartItem);
	                }.bind(this));
	                return total;
	            },
	            totalBruto: function () {
	                var total = 0;
	                this.cartItems.forEach(function (cartItem) {
	                    var vat = this.calcVat(cartItem);
	                    total += vat.bruto;
	                }.bind(this));
	                return total;
	            },
	            totalTaxes: function () {
	                return this.totalBruto - this.totalNetto
	            }

	        },

	        methods: {
	            addToCart: function (product) {
	                var cartItem = _.merge({
	                    item_title: product.item.title,
	                    item_url: product.item.url,
	                    product_id: product.id
	                }, product);
	                delete cartItem['item'];
	                delete cartItem['id'];
	                this.cartItems.push(cartItem);
	                this.saveCart();
	                this.$.cartmodal.open();
	            },

	            saveCart: function () {
	                this.resource.save({}, { cartItems: this.cartItems }, function (data) {
	                    console.log(data);
	                    this.$notify('Cart updated.');
	                });
	            },

	            calcVat: function (product) {
	                var netto = this.convertPrice(product.price, product),
	                    vatclass = this.config.vatclasses[product.vat];
	                return {
	                    netto: netto,
	                    bruto: (Math.round((netto * 100) * ((vatclass.rate / 100) + 1))) / 100,
	                    vatclass: vatclass
	                }
	            },

	            convertPrice: function (price, product) {
	                var siteCurrency = this.currency || 'EUR';
	                price = Number(price);
	                if (product && product.currency !== siteCurrency) {
	                    price = (Math.round((price * 100) * (window.$cart.config[product.currency + 'to' + siteCurrency] || 1))) / 100;
	                }
	                return price;
	            }
	        }

	    };

/***/ },
/* 10 */
/***/ function(module, exports) {

	module.exports = "<v-modal v-ref=\"cartmodal\" large>\r\n        <div class=\"uk-modal-header\">\r\n            <h3>{{ 'Items in cart' | trans }}</h3>\r\n        </div>\r\n        <div class=\"uk-margin\">\r\n            <div v-show=\"!cartItems.length\" class=\"uk-alert\">{{ 'No items in cart yet' | trans }}</div>\r\n            <ul v-show=\"cartItems.length\" class=\"uk-list uk-list-line\">\r\n                <li v-repeat=\"cartItem: cartItems\">\r\n                    <div class=\"uk-grid uk-grid-small\">\r\n                        <div class=\"uk-width-medium-3-4\">\r\n                            <a href=\"{{ cartItem.item_url}}\">{{ cartItem.item_title  }}</a>\r\n                        </div>\r\n                        <div class=\"uk-width-medium-1-4 uk-text-right\">\r\n                            {{{ cartItem.price | currency cartItem}}}\r\n                        </div>\r\n                    </div>\r\n                </li>\r\n                <li>\r\n                    <div class=\"uk-grid uk-grid-small\">\r\n                        <div class=\"uk-width-medium-3-4 uk-text-right uk-text-small\">\r\n                            <div><span>{{ 'Total taxes' | trans }}</span> {{{ totalTaxes | currency }}}</div>\r\n                            <div><span>{{ 'Total including taxes' | trans }}</span> {{{ totalBruto | currency }}}</div>\r\n                        </div>\r\n                        <div class=\"uk-width-medium-1-4 uk-text-right\">\r\n                            <h3>{{{ totalNetto | currency }}}</h3>\r\n                        </div>\r\n                    </div>\r\n                </li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"uk-modal-footer uk-text-right\">\r\n            <button type=\"button\" class=\"uk-button uk-modal-close\">{{ 'Close' | trans }}</button>\r\n            <button type=\"button\" class=\"uk-button uk-button-success uk-margin-left\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'To checkout' | trans }}</button>\r\n        </div>\r\n\r\n    </v-modal>\r\n    <pre>{{ totalNetto | json}}</pre>\r\n    <pre>{{$data.cartItems | json}}</pre>";

/***/ }
/******/ ]);
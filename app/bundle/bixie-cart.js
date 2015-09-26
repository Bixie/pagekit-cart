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

	var Cart = Vue.extend({

	    data: function () {
	         return _.merge({
	            'filters': {
	                currency: window.$cart.config.currency || 'EUR'
	            },
	            'checkout_url': '',
	            'checkout': {},
	            'config': {},
	            'products': {},
	            'cartItems': window.$cartItems || []
	        }, window.$cart);
	    },

	    created: function () {
	        this.resource = this.$resource('api/cart/cart/:id');
	        this.filters = _.assign(this.filters, JSON.parse((this.$localstorage('cart.filters') || '{}')));
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
	            this.showCart();
	        },

	        showCart: function () {
	            this.$broadcast('show.bixie.cart');
	        },

	        removeFromCart: function (idx) {
	            this.cartItems.$remove(idx);
	            this.saveCart();

	        },

	        saveCart: _.debounce(function () {
	            console.log('save' + this.cartItems.length);
	            this.resource.save({}, { cartItems: this.cartItems }, function (data) {
	                console.log(data, this.cartItems.length);
	                if (data.length == this.cartItems.length) { //todo this is bodgy
	                    this.$set('cartItems', data);
	                }
	                //this.$notify('Cart updated.');
	            });
	        }, 700),

	        checkoutSubmit: function (e) {
	            e.preventDefault();
	            this.$.checkout.doCheckout();
	        }
	    },

	    watch: {
	        'filters': {
	            handler: function (value) {
	                this.$localstorage('cart.filters', JSON.stringify(value));
	            },
	            deep: true
	        }
	    },
	    components: {
	        cartmodal: __webpack_require__(6),
	        checkout: __webpack_require__(25),
	        addtocart: __webpack_require__(10)
	    },

	    mixins: [
	        __webpack_require__(13),
	        __webpack_require__(14)
	    ]
	});

	Vue.use(__webpack_require__(19));

	$(function () {

	    $('body').append('<div><cartmodal></cartmodal></div>');

	    window.$bixieCart = (new Cart()).$mount('body');

	});

	module.exports = Cart;

/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(7)
	module.exports.template = __webpack_require__(8)


/***/ },
/* 7 */
/***/ function(module, exports) {

	module.exports = {


	        inherit: true,

	        ready: function () {
	            this.$on('show.bixie.cart', function () {
	                this.$.cartmodal.open();
	            });
	        }

	    };

/***/ },
/* 8 */
/***/ function(module, exports) {

	module.exports = "<v-modal v-ref=\"cartmodal\" large>\r\n        <div class=\"uk-modal-header\">\r\n            <h3>{{ 'Items in cart' | trans }}</h3>\r\n        </div>\r\n        <div class=\"uk-margin\">\r\n            <cartlist></cartlist>\r\n        </div>\r\n        <div class=\"uk-modal-footer uk-text-right\">\r\n            <button type=\"button\" class=\"uk-button uk-modal-close\">{{ 'Close' | trans }}</button>\r\n            <a v-attr=\"href: checkout_url\" class=\"uk-button uk-button-success uk-margin-left\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'To checkout' | trans }}</a>\r\n        </div>\r\n\r\n    </v-modal>\r\n    <pre>{{ filters.currency | json}}</pre>\r\n    <pre>{{ totalNetto | json}}</pre>";

/***/ },
/* 9 */,
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(11)
	module.exports.template = __webpack_require__(12)


/***/ },
/* 11 */
/***/ function(module, exports) {

	module.exports = {

	        props: ['product', 'item_id'],

	        inherit: true,

	        computed: {
	            includingVat: function () {
	                var vatString = this.formatprice(this.getVat(this.product)),
	                        text = this.config.vat_view == 'excl' ? '+ %vat% VAT' : 'incl. %vat% VAT';
	                return this.$trans(text, {vat: vatString});
	            }
	        }

	    };

/***/ },
/* 12 */
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-flex uk-flex-middle uk-flex-center uk-flex-space-around uk-flex-wrap uk-margin\" data-uk-margin=\"\">\r\n        <div class=\"uk-text-right\">\r\n            <strong>{{{ product | productprice }}}</strong>\r\n            <div v-if=\"config.addtocart.show_vat\"><small>{{{ includingVat }}}</small></div>\r\n        </div>\r\n        <div class=\"\">\r\n            <button type=\"button\" class=\"uk-button uk-button-success\" v-on=\"click: addToCart(product)\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'Add to cart' | trans }}\r\n            </button>\r\n        </div>\r\n\r\n    </div>";

/***/ },
/* 13 */
/***/ function(module, exports) {

	
	module.exports = {

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
	            return this.totalBruto - this.totalNetto;
	        }
	    }

	};

/***/ },
/* 14 */
/***/ function(module, exports, __webpack_require__) {

	
	var icons = {
	    'EUR': 'uk-icon-euro',
	    'USD': 'uk-icon-dollar'
	}, number_format = __webpack_require__(15);

	module.exports = {

	    methods: {

	        calcVat: function (product) {
	            var netto = this.convertPrice(product.price, product),
	                vatclass = window.$cart.config.vatclasses[product.vat],
	                bruto = (Math.round((netto * 100) * ((vatclass.rate / 100) + 1))) / 100;
	            return {
	                netto: netto,
	                bruto: bruto,
	                vat: bruto - netto,
	                vatclass: vatclass
	            };
	        },

	        convertPrice: function (price, product) {
	            price = Number(price);

	            if (product && product.currency !== this.filters.currency) {
	                price = (Math.round((price * 100) * (window.$cart.config[product.currency + 'to' + this.filters.currency] || 1))) / 100;
	            }
	            return price;
	        },

	        productprice: function (product) {
	            var price = Number(product.price);

	            if (this.config.vat_view === 'incl') {
	                price = this.inclVat(product);
	            }

	            if (product && product.currency !== this.filters.currency) {
	                price = this.convertPrice(price, product);
	            }

	            return this.formatprice(price);
	        },

	        inclVat: function (product) {
	            var vat = this.calcVat(product);
	            return vat.bruto;
	        },

	        getVat: function (product) {
	            var vat = this.calcVat(product);
	            return vat.vat;
	        },

	        formatprice: function (price) {
	            var icon = '<i class="' + icons[this.filters.currency || 'EUR'] + ' uk-margin-small-right"></i>',
	                numberString;
	            try {
	                numberString = price.toLocaleString(window.$trans.locale, {minimumFractionDigits: 2});
	            } catch (ignore) {
	                numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
	            }
	            return icon + numberString;
	        }

	    },

	    filters: {
	        productprice: function (product) {
	            return this.productprice(product);
	        },

	        inclVat: function (product) {
	            return this.inclVat(product);
	        },

	        getVat: function (product) {
	            return this.getVat(product);
	        },

	        formatprice: function (price) {
	            return this.formatprice(price);
	        }

	    },

	    components: {
	        cartlist: __webpack_require__(16)
	    }

	};

/***/ },
/* 15 */
/***/ function(module, exports) {

	
	module.exports = function number_format(number, decimals, dec_point, thousands_sep) {
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
	};

/***/ },
/* 16 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(17)
	module.exports.template = __webpack_require__(18)


/***/ },
/* 17 */
/***/ function(module, exports) {

	module.exports = {

	        inherit: true,

	        methods: {
	            removeFromCart: function (idx) {
	                window.$bixieCart.removeFromCart(idx);
	            }
	        }


	    };

/***/ },
/* 18 */
/***/ function(module, exports) {

	module.exports = "<div v-show=\"!cartItems.length\" class=\"uk-alert\">{{ 'No items in cart yet' | trans }}</div>\r\n\r\n    <ul v-show=\"cartItems.length\" class=\"uk-list uk-list-line\">\r\n        <li v-repeat=\"cartItem: cartItems\">\r\n            <div class=\"uk-grid uk-grid-small\">\r\n                <div class=\"uk-width-medium-1-2\">\r\n                    <a href=\"{{ cartItem.item_url}}\">{{ cartItem.item_title  }}</a>\r\n                </div>\r\n                <div class=\"uk-width-medium-1-2 uk-text-right\">\r\n                    <div class=\"uk-grid uk-grid-small\">\r\n                        <div class=\"uk-width-1-2\">\r\n                            <a v-on=\"click: removeFromCart($index)\" class=\"uk-icon-trash-o uk-icon-justify uk-icon-hover\"></a>\r\n                        </div>\r\n                        <div class=\"uk-width-1-2\">\r\n                            {{{ cartItem | productprice }}}\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        <li>\r\n            <div class=\"uk-grid uk-grid-small\">\r\n                <div class=\"uk-width-medium-3-4 uk-text-right uk-text-small\">\r\n                    <div v-if=\"config.vat_view == 'incl'\"><span>{{ 'Total excluding taxes' | trans }}</span> {{{ totalNetto | formatprice }}}</div>\r\n                    <div><span>{{ 'Total taxes' | trans }}</span> {{{ totalTaxes | formatprice }}}</div>\r\n                    <div v-if=\"config.vat_view == 'excl'\"><span>{{ 'Total including taxes' | trans }}</span> {{{ totalBruto | formatprice }}}</div>\r\n                </div>\r\n                <div class=\"uk-width-medium-1-4 uk-text-right\">\r\n                    <h3 v-if=\"config.vat_view == 'incl'\">{{{ totalBruto | formatprice }}}</h3>\r\n                    <h3 v-if=\"config.vat_view == 'excl'\">{{{ totalNetto | formatprice }}}</h3>\r\n                </div>\r\n            </div>\r\n        </li>\r\n    </ul>";

/***/ },
/* 19 */
/***/ function(module, exports) {

	/**
	 * Local Storage Plugin
	 * based on https://github.com/hosokawat/jquery-localstorage
	 */
	exports.install = function (Vue) {

	    var localStorage = window.localStorage,
	        supports = localStorage ? true : false;

	    var remove = function (key) {
	        if (localStorage) {
	            localStorage.removeItem(key);
	        }
	        return;
	    };

	    function allStorage() {
	        return supports ? localStorage : undefined;
	    }

	    var config = function (key, value) {
	        // All Read
	        if (key === undefined && value === undefined) {
	            return allStorage();
	        }

	        // Write
	        if (value !== undefined) {
	            if (localStorage) {
	                localStorage.setItem(key, value);
	            }
	        }

	        // Read
	        var result;
	        if (localStorage) {
	            if (localStorage[key]) {
	                result = localStorage.getItem(key);
	            }
	        }
	        return result;
	    };

	    var io = function (key) {
	        return {
	            read: function () {
	                return config(key);
	            }, write: function (value) {
	                return config(key, value);
	            }, remove: function () {
	                return remove(key);
	            }, key: key
	        };
	    };
	    Vue.prototype.$localstorage = function (key, value) {
	        return config(key, value);
	    };
	    Vue.prototype.$localstorage.remove = remove;
	    Vue.prototype.$localstorage.io = io;
	};


/***/ },
/* 20 */,
/* 21 */,
/* 22 */,
/* 23 */,
/* 24 */,
/* 25 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(26)
	module.exports.template = __webpack_require__(27)


/***/ },
/* 26 */
/***/ function(module, exports) {

	module.exports = {

	    data: function () {
	        return {
	            spin: false,
	            show_address_2: false,
	            checkout: {
	                agreed: false,
	                invoice_address: {
	                    name: 'name',
	                    address: 'address',
	                    address_2: '',
	                    zipcode: '3456',
	                    city: 'City',
	                    country: 'NL'
	                },
	                payment: {
	                    method: window.$cart.config.default_payment,
	                    price: 0
	                }
	            },
	            invalid: {}
	        };
	    },

	    inherit: true,

	    created: function () {
	        this.config.required_checkout.forEach(function (name) {
	            this.$set('invalid.' + name, false);
	        }.bind(this));
	    },

	    methods: {
	        doCheckout: function () {
	            if (this.spin) {
	                return;
	            }
	            console.log('checkout');
	            var vm = this;
	            if(this.validateCheckout()) {
	                console.log(this.checkout.payment.method);
	                this.spin = true;
	                this.resource.save({id: 'checkout'}, { cartItems: this.cartItems, checkout: this.checkout }, function (data) {

	                    console.log(data);
	                    vm.$set('spin', false);

	                });
	            }
	        },

	        validateCheckout: function () {
	            var invalid = false;
	            this.config.required_checkout.forEach(function (name) {
	                invalid = !this.validateField(name) || invalid;
	            }.bind(this));

	            return !invalid;
	        },

	        validateField: function (name) {
	            var valid = !!this.$get('checkout.' + name);
	            this.$set('invalid.' + name, !valid);
	            return valid;
	        }
	    },

	    computed: {
	        countryList: function () {
	            var options = [{value: '', text: this.$trans('Country')}];
	            options.push({value: 'NL', text: this.$trans('Netherlands')});
	            return options;
	        }
	    }

	};

/***/ },
/* 27 */
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-grid uk-grid-width-medium-1-2\">\r\n        <div>\r\n\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'Invoice Address' | trans }}</h3>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-user\"></i>\r\n                            <input v-model=\"checkout.invoice_address.name\" name=\"name\" type=\"text\"\r\n                                   v-on=\"blur: validateField('invoice_address.name')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Name' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.invoice_address.name\">\r\n                            {{ 'Please enter your name' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-building\"></i>\r\n                            <input v-model=\"checkout.invoice_address.address\" name=\"address\" type=\"text\"\r\n                                   v-on=\"blur: validateField('invoice_address.address')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Address' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.invoice_address.address\">\r\n                            {{ 'Please enter an address' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div v-show=\"show_address_2\" class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-building\"></i>\r\n                            <input v-model=\"checkout.invoice_address.address_2\" name=\"address_2\" type=\"text\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Address line 2' | trans }}\">\r\n                        </div>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"checkbox\" value=\"hide-show_address_2\"\r\n                                      v-model=\"show_address_2\"> {{ 'Show address line 2' | trans }}</label>\r\n                    </div>\r\n                </div>\r\n\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-map-pin\"></i>\r\n                            <input v-model=\"checkout.invoice_address.zipcode\" name=\"name\" type=\"text\"\r\n                                   v-on=\"blur: validateField('invoice_address.zipcode')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Zipcode' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.invoice_address.zipcode\">\r\n                            {{ 'Please enter a zipcode' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-map-o\"></i>\r\n                            <input v-model=\"checkout.invoice_address.city\" name=\"name\" type=\"text\"\r\n                                   v-on=\"blur: validateField('invoice_address.city')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'City' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.invoice_address.city\">\r\n                            {{ 'Please enter a city' | trans }}</p>\r\n                    </div>\r\n                </div>\r\n\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <select v-model=\"checkout.invoice_address.country\" name=\"country\" options=\"countryList\"\r\n                                v-on=\"blur: validateField('invoice_address.country')\"\r\n                                class=\"uk-width-1-1\"></select>\r\n                    </div>\r\n                    <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.invoice_address.country\">\r\n                        {{ 'Please select a country' | trans }}</p>\r\n                </div>\r\n            </div>\r\n\r\n\r\n        </div>\r\n        <div>\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'Payment method' | trans }}</h3>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"radio\" value=\"PAYPAL\"\r\n                                      v-model=\"checkout.payment.method\"> {{ 'Paypal' | trans }}</label>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"radio\" value=\"STRIPE\"\r\n                                      v-model=\"checkout.payment.method\"> {{ 'Stripe' | trans }}</label>\r\n                    </div>\r\n                </div>\r\n\r\n            </div>\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'To payment' | trans }}</h3>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"checkbox\" name=\"agreed\" value=\"agreed\"\r\n                                      v-model=\"checkout.agreed\"> {{ 'I agree with the terms and conditions' | trans }}</label>\r\n                    </div>\r\n                    <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.agreed\">\r\n                        {{ 'Please agree with the terms and conditions' | trans }}</p>\r\n                </div>\r\n\r\n                <div class=\"uk-margin uk-text-right\">\r\n                    <button class=\"uk-button uk-button-large uk-button-success\">\r\n                        <i v-show=\"!spin\" class=\"uk-icon-check uk-margin-small-right\"></i>\r\n                        <i v-show=\"spin\" class=\"uk-icon-circle-o-notch uk-icon-spin uk-margin-small-right\"></i>\r\n                        {{ 'To payment' | trans }}\r\n                    </button>\r\n                </div>\r\n\r\n              </div>\r\n        </div>\r\n    </div>\r\n<pre>{{checkout|json}}</pre>";

/***/ }
/******/ ]);
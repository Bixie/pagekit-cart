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
	            'gateways': [],
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
	        cartmodal: __webpack_require__(7),
	        checkout: __webpack_require__(10),
	        addtocart: __webpack_require__(13)
	    },

	    mixins: [
	        __webpack_require__(16),
	        __webpack_require__(17)
	    ]
	});

	Vue.use(__webpack_require__(22));

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
/* 6 */,
/* 7 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(8)
	module.exports.template = __webpack_require__(9)


/***/ },
/* 8 */
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
/* 9 */
/***/ function(module, exports) {

	module.exports = "<v-modal v-ref=\"cartmodal\" large>\r\n        <div class=\"uk-modal-header\">\r\n            <h3>{{ 'Items in cart' | trans }}</h3>\r\n        </div>\r\n        <div class=\"uk-margin\">\r\n            <cartlist></cartlist>\r\n        </div>\r\n        <div class=\"uk-modal-footer uk-text-right\">\r\n            <button type=\"button\" class=\"uk-button uk-modal-close\">{{ 'Close' | trans }}</button>\r\n            <a v-attr=\"href: checkout_url\" class=\"uk-button uk-button-success uk-margin-left\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'To checkout' | trans }}</a>\r\n        </div>\r\n\r\n    </v-modal>\r\n    <pre>{{ filters.currency | json}}</pre>\r\n    <pre>{{ totalNetto | json}}</pre>";

/***/ },
/* 10 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(11)
	module.exports.template = __webpack_require__(12)


/***/ },
/* 11 */
/***/ function(module, exports) {

	module.exports = {

	    data: function () {
	        return {
	            spin: false,
	            paymenterror: '',
	            show_address2: false,
	            checkout: {
	                agreed: false,
	                billing_address: {
	                    firstName: 'Piet',
	                    lastName: 'Jansen',
	                    email: 'info@bixie.nl',
	                    phone: '467748',
	                    address1: 'Straat 34',
	                    address2: '',
	                    postcode: '3456 BE',
	                    city: 'Ede',
	                    state: '',
	                    country: 'NL'
	                },
	                payment: {
	                    method: '',
	                    price: 0
	                }
	            },
	            card: {
	                number: '4242424242424242',
	                expiryMonth: '6',
	                expiryYear: '2016',
	                cvv: '123'
	            },
	            invalid: {}
	        };
	    },

	    inherit: true,

	    created: function () {
	        this.config.required_checkout.forEach(function (name) {
	            this.$set('invalid.' + name, false);
	        }.bind(this));
	        this.$set('invalid.payment.method', false);
	        if (this.gateways.length === 1) {
	            this.$set('checkout.payment.method', this.gateways[0].shortName);
	        }
	    },

	    methods: {
	        doCheckout: function () {
	            if (this.spin) {
	                return;
	            }
	            var vm = this;

	            if(this.validateCheckout()) {

	                this.spin = true;
	                this.$set('paymenterror', '');

	                this.resource.save({id: 'checkout'}, {
	                    cartItems: this.cartItems,
	                    cardData: this.card,
	                    checkout: _.merge({currency: this.filters.currency}, this.checkout)
	                }, function (data) {

	                    vm.$set('spin', false);
	                    if (data.error) {
	                        vm.$set('paymenterror', data.error);
	                    } else {
	                        console.log(data);
	                        if (data.succesurl) {
	                            //reset cart oon orig vm
	                            this.$set('cartItems', data.cartItems);
	                            vm.$.redirectmodal.open();
	                            setTimeout(function () {
	                                window.location.href = data.succesurl;
	                            }, 500)
	                        }

	                    }

	                });
	            }
	        },

	        validateCheckout: function () {
	            var invalid = false;

	            this.config.required_checkout.forEach(function (name) {
	                invalid = !this.validateField(name, 'checkout') || invalid;
	            }.bind(this));

	            ['number', 'expiryMonth', 'expiryYear', 'cvv'].forEach(function (name) {
	                invalid = !this.validateField(name, 'card') || invalid;
	            }.bind(this));

	            if (!this.checkout.payment.method) {
	                invalid = true;
	            }
	            this.$set('invalid.payment.method', !this.checkout.payment.method);

	            return !invalid;
	        },

	        validateField: function (name, type) {
	            var valid = !!this.$get(type + '.' + name);
	            this.$set('invalid.' + name, !valid);
	            return valid;
	        }
	    },

	    computed: {
	        countryList: function () {
	            var options = [{value: '', text: this.$trans('Country')}];
	            _.forIn(this.countries, function (text, value) {
	                options.push({value: value, text: text});
	            });
	            return options;
	        },
	        months: function () {
	            var options = [{value: '', text: this.$trans('Month')}];
	            for (var m = 1; m <= 12; m++) {
	                options.push({value: m.toString(), text: m.toString()});
	            }
	            return options;
	        },
	        years: function () {
	            var nowYear = (new Date()).getFullYear(),
	               options = [{value: '', text: this.$trans('Year')}];
	            for (var y = nowYear; y < (nowYear + 10); y++) {
	                options.push({value: y.toString(), text: y.toString()});
	            }
	            return options;
	        }
	    }

	};

/***/ },
/* 12 */
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-grid uk-grid-width-medium-1-2\">\r\n        <div>\r\n\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'Billing Address' | trans }}</h3>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-user\"></i>\r\n                            <input v-model=\"checkout.billing_address.firstName\" name=\"firstName\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.firstName')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'First name' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.firstName\">\r\n                            {{ 'Please enter your first name' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-user\"></i>\r\n                            <input v-model=\"checkout.billing_address.lastName\" name=\"lastName\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.lastName')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Last name' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.lastName\">\r\n                            {{ 'Please enter your last name' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-envelope-o\"></i>\r\n                            <input v-model=\"checkout.billing_address.email\" name=\"email\" type=\"email\"\r\n                                   v-on=\"blur: validateField('billing_address.email')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Email address' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.email\">\r\n                            {{ 'Please enter your email address' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-phone\"></i>\r\n                            <input v-model=\"checkout.billing_address.phone\" name=\"phone\" type=\"text\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Phone number' | trans }}\">\r\n                        </div>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-building-o\"></i>\r\n                            <input v-model=\"checkout.billing_address.address1\" name=\"address1\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.address1')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Address' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.address1\">\r\n                            {{ 'Please enter an address' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div v-show=\"show_address2\" class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-building-o\"></i>\r\n                            <input v-model=\"checkout.billing_address.address2\" name=\"address2\" type=\"text\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Address line 2' | trans }}\">\r\n                        </div>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"checkbox\" value=\"hide-show_address2\"\r\n                                      v-model=\"show_address2\"> {{ 'Show address line 2' | trans }}</label>\r\n                    </div>\r\n                </div>\r\n\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-map-pin\"></i>\r\n                            <input v-model=\"checkout.billing_address.postcode\" name=\"postcode\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.postcode')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Zipcode' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.postcode\">\r\n                            {{ 'Please enter a zipcode' | trans }}</p>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-map-o\"></i>\r\n                            <input v-model=\"checkout.billing_address.city\" name=\"city\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.city')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'City' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.city\">\r\n                            {{ 'Please enter a city' | trans }}</p>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-map-o\"></i>\r\n                            <input v-model=\"checkout.billing_address.state\" name=\"state\" type=\"text\"\r\n                                   v-on=\"blur: validateField('billing_address.state')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'State' | trans }}\">\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <select v-model=\"checkout.billing_address.country\" name=\"country\" options=\"countryList\"\r\n                                v-on=\"change: validateField('billing_address.country')\"\r\n                                class=\"uk-width-1-1\"></select>\r\n                    </div>\r\n                    <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.billing_address.country\">\r\n                        {{ 'Please select a country' | trans }}</p>\r\n                </div>\r\n            </div>\r\n\r\n\r\n        </div>\r\n        <div>\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'Payment method' | trans }}</h3>\r\n\r\n                <div v-repeat=\"gateway: gateways\" class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"radio\" value=\"{{ gateway.shortName | trans }}\"\r\n                                      v-model=\"checkout.payment.method\"> {{ gateway.name | trans }}</label>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls\">\r\n                        <div class=\"uk-form-icon uk-width-1-1\">\r\n                            <i class=\"uk-icon-credit-card\"></i>\r\n                            <input v-model=\"card.number\" name=\"number\" type=\"text\"\r\n                                   v-on=\"blur: validateField('card.number')\"\r\n                                   class=\"uk-width-1-1\" placeholder=\"{{ 'Credit card number' | trans }}\">\r\n                        </div>\r\n                        <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.number\">\r\n                            {{ 'Please enter card number' | trans }}</p>\r\n                    </div>\r\n                </div>\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <div class=\"uk-grid uk-grid-small\">\r\n                            <div class=\"uk-width-1-3\">\r\n                                <select v-model=\"card.expiryMonth\" name=\"expiryMonth\" options=\"months\"\r\n                                        v-on=\"blur: validateField('card.expiryMonth')\"\r\n                                        class=\"uk-width-1-1\"></select>\r\n                                <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.expiryMonth\">\r\n                                    {{ 'Please enter expiry month' | trans }}</p>\r\n                            </div>\r\n                            <div class=\"uk-width-1-3\">\r\n                                <select v-model=\"card.expiryYear\" name=\"expiryYear\" options=\"years\"\r\n                                        v-on=\"blur: validateField('card.expiryYear')\"\r\n                                        class=\"uk-width-1-1\"></select>\r\n                                <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.expiryYear\">\r\n                                    {{ 'Please enter expiry year' | trans }}</p>\r\n                            </div>\r\n                            <div class=\"uk-width-1-3\">\r\n                                <input v-model=\"card.cvv\" name=\"vvc\" type=\"text\"\r\n                                       v-on=\"blur: validateField('card.cvv')\"\r\n                                       class=\"uk-width-1-1\" placeholder=\"{{ 'CVV' | trans }}\">\r\n                                <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.cvv\">\r\n                                    {{ 'Please enter card number' | trans }}</p>\r\n                            </div>\r\n                        </div>\r\n\r\n                    </div>\r\n                </div>\r\n\r\n                <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.payment.method\">\r\n                    {{ 'Please select a payment method' | trans }}</p>\r\n            </div>\r\n            <div class=\"uk-panel uk-panel-box uk-form\">\r\n\r\n                <h3 class=\"uk-panel-title\">{{ 'To payment' | trans }}</h3>\r\n\r\n                <div class=\"uk-form-row\">\r\n                    <div class=\"uk-form-controls uk-form-controls-text\">\r\n                        <label><input type=\"checkbox\" name=\"agreed\" value=\"agreed\"\r\n                                      v-model=\"checkout.agreed\"> {{ 'I agree with the terms and conditions' | trans }}</label>\r\n                    </div>\r\n                    <p class=\"uk-form-help-block uk-text-danger\" v-show=\"invalid.agreed\">\r\n                        {{ 'Please agree with the terms and conditions' | trans }}</p>\r\n                </div>\r\n\r\n                <div class=\"uk-margin uk-text-right\">\r\n                    <button class=\"uk-button uk-button-large uk-button-success\">\r\n                        <i v-show=\"!spin\" class=\"uk-icon-check uk-margin-small-right\"></i>\r\n                        <i v-show=\"spin\" class=\"uk-icon-circle-o-notch uk-icon-spin uk-margin-small-right\"></i>\r\n                        {{ 'To payment' | trans }}\r\n                    </button>\r\n                </div>\r\n\r\n                <div v-if=\"paymenterror\" class=\"uk-alert uk-alert-danger\">{{ paymenterror | trans }}</div>\r\n\r\n              </div>\r\n        </div>\r\n    </div>\r\n\r\n    <v-modal v-ref=\"redirectmodal\" lightbox options=\"{{ {center: true} }}\">\r\n        <div class=\"uk-panel uk-panel-space uk-text-center\">\r\n            <h1 class=\"uk-heading-large\"><i class=\"uk-icon-check uk-text-success uk-margin-small-right\"></i>\r\n                {{ 'Payment successful' | trans }}</h1>\r\n            <p>{{ 'You are redirected....' | trans }}</p>\r\n            <p><i class=\"uk-icon-refresh uk-icon-spin\"></i></p>\r\n        </div>\r\n\r\n    </v-modal>";

/***/ },
/* 13 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(14)
	module.exports.template = __webpack_require__(15)


/***/ },
/* 14 */
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
/* 15 */
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-flex uk-flex-middle uk-flex-center uk-flex-space-around uk-flex-wrap uk-margin\" data-uk-margin=\"\">\r\n        <div class=\"uk-text-right\">\r\n            <strong>{{{ product | productprice }}}</strong>\r\n            <div v-if=\"config.addtocart.show_vat\"><small>{{{ includingVat }}}</small></div>\r\n        </div>\r\n        <div class=\"\">\r\n            <button type=\"button\" class=\"uk-button uk-button-success\" v-on=\"click: addToCart(product)\">\r\n                <i class=\"uk-icon-shopping-cart uk-margin-small-right\"></i>{{ 'Add to cart' | trans }}\r\n            </button>\r\n        </div>\r\n\r\n    </div>";

/***/ },
/* 16 */
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
/* 17 */
/***/ function(module, exports, __webpack_require__) {

	
	var icons = {
	    'EUR': 'uk-icon-euro',
	    'USD': 'uk-icon-dollar'
	}, number_format = __webpack_require__(18);

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
	        cartlist: __webpack_require__(19)
	    }

	};

/***/ },
/* 18 */
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
/* 19 */
/***/ function(module, exports, __webpack_require__) {

	module.exports = __webpack_require__(20)
	module.exports.template = __webpack_require__(21)


/***/ },
/* 20 */
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
/* 21 */
/***/ function(module, exports) {

	module.exports = "<div v-show=\"!cartItems.length\" class=\"uk-alert\">{{ 'No items in cart yet' | trans }}</div>\r\n\r\n    <ul v-show=\"cartItems.length\" class=\"uk-list uk-list-line\">\r\n        <li v-repeat=\"cartItem: cartItems\">\r\n            <div class=\"uk-grid uk-grid-small\">\r\n                <div class=\"uk-width-medium-1-2\">\r\n                    <a href=\"{{ cartItem.item_url}}\">{{ cartItem.item_title  }}</a>\r\n                </div>\r\n                <div class=\"uk-width-medium-1-2 uk-text-right\">\r\n                    <div class=\"uk-grid uk-grid-small\">\r\n                        <div class=\"uk-width-1-2\">\r\n                            <a v-on=\"click: removeFromCart($index)\" class=\"uk-icon-trash-o uk-icon-justify uk-icon-hover\"></a>\r\n                        </div>\r\n                        <div class=\"uk-width-1-2\">\r\n                            {{{ cartItem | productprice }}}\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n        </li>\r\n        <li>\r\n            <div class=\"uk-grid uk-grid-small\">\r\n                <div class=\"uk-width-medium-3-4 uk-text-right uk-text-small\">\r\n                    <div v-if=\"config.vat_view == 'incl'\"><span>{{ 'Total excluding taxes' | trans }}</span> {{{ totalNetto | formatprice }}}</div>\r\n                    <div><span>{{ 'Total taxes' | trans }}</span> {{{ totalTaxes | formatprice }}}</div>\r\n                    <div v-if=\"config.vat_view == 'excl'\"><span>{{ 'Total including taxes' | trans }}</span> {{{ totalBruto | formatprice }}}</div>\r\n                </div>\r\n                <div class=\"uk-width-medium-1-4 uk-text-right\">\r\n                    <h3 v-if=\"config.vat_view == 'incl'\">{{{ totalBruto | formatprice }}}</h3>\r\n                    <h3 v-if=\"config.vat_view == 'excl'\">{{{ totalNetto | formatprice }}}</h3>\r\n                </div>\r\n            </div>\r\n        </li>\r\n    </ul>";

/***/ },
/* 22 */
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


/***/ }
/******/ ]);
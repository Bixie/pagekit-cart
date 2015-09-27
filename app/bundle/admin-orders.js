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

	module.exports = {

	    data: function () {
	        return _.merge({
	            orders: false,
	            pages: 0,
	            count: '',
	            selected: [],
	            filters: {
	                currency: window.$data.config.currency || 'EUR'
	            }
	        }, window.$data);
	    },

	    created: function () {
	        this.resource = this.$resource('api/cart/order/:id');
	        this.config.filter = _.extend({
	            status: '',
	            search: '',
	            order: 'created desc',
	            limit: this.config.orders_per_page
	        }, this.config.filter);
	    },

	    computed: {

	        statusOptions: function () {

	            var options = _.map(this.statuses, function (status, id) {
	                return { text: status, value: id };
	            });

	            return [{ label: this.$trans('Filter by'), options: options }];
	        }
	    },

	    methods: {

	        cartItems: function (order) {
	            var cartItems = order.cartItems.map(function (cartItem) {
	                return cartItem.item_title;
	            });
	            return cartItems.join(', ');
	        },

	        active: function (portfolio) {
	            return this.selected.indexOf(portfolio.id) != -1;
	        },

	        load: function (page) {

	            page = page !== undefined ? page : this.config.page;

	            return this.resource.query({ filter: this.config.filter, page: page }, function (data) {
	                this.$set('orders', data.orders);
	                this.$set('pages', data.pages);
	                this.$set('count', data.count);
	                this.$set('config.page', page);
	                this.$set('selected', []);
	            });
	        },

	        save: function (order) {
	            this.resource.save({ id: order.id }, { order: order }, function (data) {
	                this.load();
	                this.$notify('Cart order saved.');
	            });
	        },

	        status: function (status) {

	            var orders = this.getSelected();

	            orders.forEach(function (file) {
	                file.status = status;
	            });

	            this.resource.save({ id: 'bulk' }, { orders: orders }, function (data) {
	                this.load();
	                this.$notify('Orders saved.');
	            });
	        },

	        toggleStatus: function (order) {
	            order.status = order.status === 0 ? 1 : 0;
	            this.save(order);
	        },

	        getSelected: function () {
	            return this.orders.filter(function (order) {
	                return this.selected.indexOf(order.id) !== -1;
	            }, this);
	        },

	        removeOrders: function () {

	            this.resource.delete({id: 'bulk'}, {ids: this.selected}, function () {
	                this.load();
	                this.$notify('Order(s) deleted.');
	            });
	        },

	        getStatusText: function (order) {
	            return this.statuses[order.status];
	        },

	        getName: function (order) {
	            return order.data.billing_address.firstName + ' ' + order.data.billing_address.lastName;
	        }

	    },

	    watch: {
	        'config.page': 'load',

	        'config.filter': {
	            handler: function () { this.load(0); },
	            deep: true
	        }
	    },

	    mixins: [__webpack_require__(17)]


	};

	$(function () {

	    new Vue(module.exports).$mount('#cart-orders');

	});



/***/ },
/* 1 */,
/* 2 */,
/* 3 */,
/* 4 */,
/* 5 */,
/* 6 */,
/* 7 */,
/* 8 */,
/* 9 */,
/* 10 */,
/* 11 */,
/* 12 */,
/* 13 */,
/* 14 */,
/* 15 */,
/* 16 */,
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

	        formatprice: function (price, currency) {
	            var icon = '<i class="' + icons[currency || this.filters.currency || 'EUR'] + ' uk-margin-small-right"></i>',
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

/***/ }
/******/ ]);
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
/***/ function(module, exports) {

	
	module.exports = Vue.extend({

	    data: function () {
	        return _.merge({
	            tags: [],
	            order: {}
	        }, window.$data);
	    },

	    ready: function () {
	        this.resource = this.$resource('api/cart/order/:id');
	        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
	    },

	    computed: {

	        statusOptions: function () {
	            return _.map(this.statuses, function (status, id) { return { text: status, value: id }; });
	        },

	        userOptions: function () {
	            return this.users.map(function (user) { return { text: user.username, value: user.id }; });
	        }
	    },

	    filters: {
	        nl2br: function (str) {
	            //  discuss at: http://phpjs.org/functions/nl2br/
	            return String(str).replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
	        }
	    },

	    methods: {

	        save: function (e) {

	            e.preventDefault();

	            var data = {order: this.order};

	            this.$broadcast('save', data);

	            this.resource.save({id: this.order.id}, data, function (data) {

	                if (!this.order.id) {
	                    window.history.replaceState({}, '', this.$url.route('admin/cart/order/edit', {id: data.file.id}));
	                }

	                this.$set('order', data.order);

	                this.$notify(this.$trans('Order %transaction_id% saved.', {transaction_id: this.order.transaction_id}));

	            }, function (data) {
	                this.$notify(data, 'danger');
	            });
	        },

	        getStatusText: function(order) {
	            return this.statuses[order.status];
	        }

	    }

	});

	$(function () {

	    (new module.exports()).$mount('#order-edit');

	});


/***/ }
/******/ ]);
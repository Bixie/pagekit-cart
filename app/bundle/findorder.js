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
	        return {
	            loading: false,
	            alert: '',
	            step: 1,
	            transaction_id: '',
	            email: '',
	            username: '',
	            password: ''
	        };
	    },

	    created: function () {
	        this.resource = this.$resource('api/cart/order/:task');
	    },

	    methods: {

	        submitForm: function () {
	            this.$set('loading', true);
	            this.$set('alert', '');
	            if (this.step == 1) {
	                this.lookUp();
	            }
	            if (this.step == 2) {
	                this.register();
	            }
	        },

	        lookUp: function () {

	            this.resource.save({task: 'findorder'}, {
	                transaction_id: this.transaction_id,
	                email: this.email
	            }, function (data) {
	                this.$set('loading', false);
	                if (data.success) {
	                    this.$set('step', 2);
	                } else if (data.error) {
	                    this.$set('alert', data.error);
	                }
	            });

	        },

	        register: function () {
	            this.resource.save({task: 'register'}, {
	                transaction_id: this.transaction_id,
	                user: {
	                    email: this.email,
	                    username: this.username,
	                    password: this.password
	                }
	            }, function (data) {
	                this.$set('loading', false);
	                if (data.success) {
	                    this.$set('step', 3);
	                } else if (data.error) {
	                    this.$set('alert', data.error);
	                }
	            });

	        }
	    }

	});

	$(function () {

	    (new module.exports()).$mount('#bixie-findorder');

	});


/***/ }
/******/ ]);
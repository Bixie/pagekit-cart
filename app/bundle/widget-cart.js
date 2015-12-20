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

	module.exports = __webpack_require__(30)

	if (module.exports.__esModule) module.exports = module.exports.default
	;(typeof module.exports === "function" ? module.exports.options : module.exports).template = __webpack_require__(31)
	if (false) {(function () {  module.hot.accept()
	  var hotAPI = require("vue-hot-reload-api")
	  hotAPI.install(require("vue"), true)
	  if (!hotAPI.compatible) return
	  var id = "C:\\BixieProjects\\pagekit\\pagekit\\packages\\bixie\\cart\\app\\components\\widget-cart.vue"
	  if (!module.hot.data) {
	    hotAPI.createRecord(id, module.exports)
	  } else {
	    hotAPI.update(id, module.exports, (typeof module.exports === "function" ? module.exports.options : module.exports).template)
	  }
	})()}

/***/ },

/***/ 30:
/***/ function(module, exports) {

	'use strict';

	// <template>

	//     <div class="uk-grid pk-grid-large" data-uk-grid-margin>

	//         <div class="uk-flex-item-1 uk-form-horizontal">

	//             <div class="uk-form-row">

	//                 <label for="form-title" class="uk-form-label">{{ 'Title' | trans }}</label>

	//                 <div class="uk-form-controls">

	//                     <input id="form-title" class="uk-form-width-large" type="text" name="title" v-model="widget.title" v-validate:required>

	//                     <p class="uk-form-help-block uk-text-danger" v-show="form.title.invalid">{{ 'Title cannot be blank.' | trans }}</p>

	//                 </div>

	//             </div>

	//            <div class="uk-form-row">

	//                 <span class="uk-form-label">{{ 'Currency' | trans }}</span>

	//                 <div class="uk-form-controls uk-form-controls-text">

	//                     <label><input type="checkbox" value="hide-title" v-model="widget.data.show_currency"> {{ 'Show currency selector' |

	//                         trans }}</label>

	//                 </div>

	//             </div>

	//         </div>

	//         <div class="pk-width-sidebar pk-width-sidebar-large">

	//             <partial name="settings"></partial>

	//         </div>

	//     </div>

	// </template>

	// <script>

	module.exports = {

	    section: {
	        label: 'Settings'
	    },

	    replace: false,

	    props: ['widget', 'config', 'form'],

	    created: function created() {
	        this.widget.data = _.assign({ show_currency: true }, this.widget.data);
	    }
	};

	window.Widgets.components['bixie-widget-cart:settings'] = module.exports;

	// </script>

/***/ },

/***/ 31:
/***/ function(module, exports) {

	module.exports = "<div class=\"uk-grid pk-grid-large\" data-uk-grid-margin>\r\n        <div class=\"uk-flex-item-1 uk-form-horizontal\">\r\n\r\n            <div class=\"uk-form-row\">\r\n                <label for=\"form-title\" class=\"uk-form-label\">{{ 'Title' | trans }}</label>\r\n                <div class=\"uk-form-controls\">\r\n                    <input id=\"form-title\" class=\"uk-form-width-large\" type=\"text\" name=\"title\" v-model=\"widget.title\" v-validate:required>\r\n                    <p class=\"uk-form-help-block uk-text-danger\" v-show=\"form.title.invalid\">{{ 'Title cannot be blank.' | trans }}</p>\r\n                </div>\r\n            </div>\r\n\r\n           <div class=\"uk-form-row\">\r\n                <span class=\"uk-form-label\">{{ 'Currency' | trans }}</span>\r\n\r\n                <div class=\"uk-form-controls uk-form-controls-text\">\r\n                    <label><input type=\"checkbox\" value=\"hide-title\" v-model=\"widget.data.show_currency\"> {{ 'Show currency selector' |\r\n                        trans }}</label>\r\n                </div>\r\n            </div>\r\n\r\n\r\n        </div>\r\n        <div class=\"pk-width-sidebar pk-width-sidebar-large\">\r\n\r\n            <partial name=\"settings\"></partial>\r\n\r\n        </div>\r\n    </div>";

/***/ }

/******/ });
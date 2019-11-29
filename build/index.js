/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('awfn/metar', {
  title: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('AWFN Block'),
  icon: 'cloud',
  category: 'widgets',
  attributes: {
    apts: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-apts',
      default: ''
    },
    title: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-title',
      default: ''
    },
    radial_dist: {
      type: 'number',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-radial_dist',
      default: 100
    },
    hours: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-hours',
      default: '2'
    },
    show_metar: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-show_metar',
      default: '1'
    },
    show_taf: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-show_taf',
      default: '1'
    },
    show_pireps: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-show_pireps',
      default: '1'
    },
    show_station_info: {
      type: 'string',
      source: 'attribute',
      selector: 'section',
      attribute: 'data-show_station_info',
      default: '1'
    }
  },
  edit: function edit(props) {
    var _props$attributes = props.attributes,
        apts = _props$attributes.apts,
        title = _props$attributes.title,
        radial_dist = _props$attributes.radial_dist,
        hours = _props$attributes.hours,
        show_metar = _props$attributes.show_metar,
        show_taf = _props$attributes.show_taf,
        show_pireps = _props$attributes.show_pireps,
        show_station_info = _props$attributes.show_station_info,
        setAttributes = props.setAttributes;
    var metarVal = show_metar !== '0',
        tafVal = show_taf !== '0',
        pirepVal = show_pireps !== '0',
        stationVal = show_station_info !== '0';

    var onChangeIcao = function onChangeIcao(newIcao) {
      setAttributes({
        apts: newIcao
      });
    };

    var onChangeTitle = function onChangeTitle(newTitle) {
      setAttributes({
        title: newTitle
      });
    };

    var onChangeDist = function onChangeDist(newDist) {
      setAttributes({
        radial_dist: newDist
      });
    };

    var onChangeHours = function onChangeHours(newHours) {
      setAttributes({
        hours: newHours
      });
    };

    var onChangeShowMetar = function onChangeShowMetar(newMetar) {
      var metar = newMetar ? '1' : '0';
      setAttributes({
        show_metar: metar
      });
    };

    var onChangeShowTaf = function onChangeShowTaf(newTaf) {
      var taf = newTaf ? '1' : '0';
      setAttributes({
        show_taf: taf
      });
    };

    var onChangeShowPireps = function onChangeShowPireps(newPireps) {
      var pireps = newPireps ? '1' : '0';
      setAttributes({
        show_pireps: pireps
      });
    };

    var onChangeShowStationInfo = function onChangeShowStationInfo(newStationInfo) {
      var station = newStationInfo ? '1' : '0';
      setAttributes({
        show_station_info: station
      });
    };

    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", null, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["TextControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('ICAO'),
      onChange: onChangeIcao,
      value: apts
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Radial Distance'),
      onChange: onChangeDist,
      value: radial_dist,
      options: [{
        value: 25,
        label: '25'
      }, {
        value: 50,
        label: '50'
      }, {
        value: 100,
        label: '100'
      }, {
        value: 125,
        label: '125'
      }, {
        value: 150,
        label: '150'
      }, {
        value: 175,
        label: '175'
      }, {
        value: 200,
        label: '200'
      }, {
        value: 250,
        label: '250'
      }, {
        value: 300,
        label: '300'
      }]
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Hours Before Now'),
      onChange: onChangeHours,
      value: hours,
      options: [{
        value: 1,
        label: '1'
      }, {
        value: 2,
        label: '2'
      }, {
        value: 3,
        label: '3'
      }, {
        value: 4,
        label: '4'
      }, {
        value: 5,
        label: '5'
      }, {
        value: 6,
        label: '6'
      }]
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["TextControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Custom Title'),
      onChange: onChangeTitle,
      value: title
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show METAR'),
      onChange: onChangeShowMetar,
      checked: metarVal
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show TAF'),
      onChange: onChangeShowTaf,
      checked: tafVal
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show PIREPS'),
      onChange: onChangeShowPireps,
      checked: pirepVal
    }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["ToggleControl"], {
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__["__"])('Show Station Info'),
      onChange: onChangeShowStationInfo,
      checked: stationVal
    }));
  },
  save: function save(props) {
    var spinnerUrl = opts.spinnerUrl;
    var _props$attributes2 = props.attributes,
        apts = _props$attributes2.apts,
        title = _props$attributes2.title,
        radial_dist = _props$attributes2.radial_dist,
        hours = _props$attributes2.hours,
        show_metar = _props$attributes2.show_metar,
        show_taf = _props$attributes2.show_taf,
        show_pireps = _props$attributes2.show_pireps,
        show_station_info = _props$attributes2.show_station_info;
    var attributes = props.attributes;
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("section", {
      className: "awfn-shortcode",
      "data-apts": apts,
      "data-title": title,
      "data-radial_dist": radial_dist,
      "data-hours": hours,
      "data-show_metar": show_metar,
      "data-show_taf": show_taf,
      "data-show_pireps": show_pireps,
      "data-show_station_info": show_station_info,
      "data-atts": JSON.stringify(attributes)
    }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("img", {
      className: "sc-loading",
      src: spinnerUrl
    }));
  }
});

/***/ }),

/***/ "@wordpress/blocks":
/*!*****************************************!*\
  !*** external {"this":["wp","blocks"]} ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!*********************************************!*\
  !*** external {"this":["wp","components"]} ***!
  \*********************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!******************************************!*\
  !*** external {"this":["wp","element"]} ***!
  \******************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!***************************************!*\
  !*** external {"this":["wp","i18n"]} ***!
  \***************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map
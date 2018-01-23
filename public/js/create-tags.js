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
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
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
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 216);
/******/ })
/************************************************************************/
/******/ ({

/***/ 216:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(217);


/***/ }),

/***/ 217:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createClasses", function() { return createClasses; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createTag", function() { return createTag; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "createTags", function() { return createTags; });
var clone = function clone(items) {
    return JSON.parse(JSON.stringify(items));
};

var validateUserRules = function validateUserRules(text, validation) {
    return validation.filter(function (val) {
        return !new RegExp(val.rule).test(text);
    }).map(function (val) {
        return val.type;
    });
};

var createClasses = function createClasses(text, tags, validation, checkDuplicatesFromInside) {
    var classes = validateUserRules(text, validation);
    if (checkDuplicatesFromInside) {
        if (tags.filter(function (t) {
            return t.text === text;
        }).length > 1) classes.push('duplicate');
    } else {
        if (tags.map(function (t) {
            return t.text;
        }).includes(text)) classes.push('duplicate');
    }
    classes.length === 0 ? classes.push('valid') : classes.push('invalid');
    return classes;
};

var createTag = function createTag(tag, tags, validation, checkDuplicatesFromTags) {
    if (tag.text === undefined) tag = { text: tag };
    var t = clone(tag);
    t.tiClasses = createClasses(t.text, tags, validation, checkDuplicatesFromTags);
    return t;
};

var createTags = function createTags(tags, validation, checkDuplicatesFromTags) {
    return tags.map(function (t) {
        return createTag(t, tags, validation, checkDuplicatesFromTags);
    });
};



/***/ })

/******/ });
/*
 * utils.js
 *
 * Independent helper methods (cross browser, etc..)
 *
 */

// Define module
module.exports = utils = {};

// Useragent info for keycode handling
var uAgent = (typeof navigator !== 'undefined') ? navigator.userAgent : null,
    iPhone = /iphone/i.test(uAgent);

//
// Shallow copy properties from n objects to destObj
//
utils.extend = function (destObj) {
    for (var i = 1; i < arguments.length; i++) {
        for (var key in arguments[i]) {
            destObj[key] = arguments[i][key];
        }
    }
    return destObj;
};

//
// Add a given character to a string at a defined pos
//
utils.addChars = function (str, chars, pos) {
    return str.substr(0, pos) + chars + str.substr(pos, str.length);
};

//
// Remove a span of characters
//
utils.removeChars = function (str, start, end) {
    return str.substr(0, start) + str.substr(end, str.length);
};

//
// Return true/false is num false between bounds
//
utils.isBetween = function (num, bounds) {
    bounds.sort(function (a, b) {
        return a - b;
    });
    return (num > bounds[0] && num < bounds[1]);
};

//
// Helper method for cross browser event listeners
//
utils.addListener = function (el, evt, handler) {
    return (typeof el.addEventListener != "undefined")
        ? el.addEventListener(evt, handler, false)
        : el.attachEvent('on' + evt, handler);
};

//
// Helper method for cross browser implementation of preventDefault
//
utils.preventDefault = function (evt) {
    return (evt.preventDefault) ? evt.preventDefault() : (evt.returnValue = false);
};

//
// Helper method for cross browser implementation for grabbing
// clipboard data
//
utils.getClip = function (evt) {
    if (evt.clipboardData) {
        return evt.clipboardData.getData('Text');
    }
    if (window.clipboardData) {
        return window.clipboardData.getData('Text');
    }
};

//
// Returns true/false if k is a del key
//
utils.isDelKey = function (k) {
    return k === 8 || k === 46 || (iPhone && k === 127);
};

//
// Returns true/false if k is an arrow key
//
utils.isSpecialKey = function (k) {
    var codes = {
        '35': 'end',
        '36': 'home',
        '37': 'leftarrow',
        '38': 'uparrow',
        '39': 'rightarrow',
        '40': 'downarrow'
    };
    // If del or special key
    return codes[k];
};

//
// Returns true/false if modifier key is held down
//
utils.isModifier = function (evt) {
    return evt.ctrlKey || evt.altKey || evt.metaKey;
};
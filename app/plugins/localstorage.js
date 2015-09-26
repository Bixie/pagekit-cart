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

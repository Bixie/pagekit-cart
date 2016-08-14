
window.BixCart = require('./cart.vue');

window.$bixCartComponents = window.$bixCartComponents || [];
Vue.ready(function () {
    window.$bixCart = new Vue(window.BixCart).$mount().$appendTo('body');
    window.$bixCartComponents.forEach(function (component) {
        if (UIkit.$(component.el).length) {
            new Vue(component);
        }
    });
});

module.exports = window.BixCart;
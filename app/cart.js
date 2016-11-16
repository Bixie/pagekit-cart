
window.BixCart = require('./cart.vue');


window.$bixCartComponents = window.$bixCartComponents || [];
Vue.ready(function () {
    require('./lib/currency')(Vue);

    var checkout_page = jQuery('#bix-cart-checkout');
    if (checkout_page.length) {
        window.BixCart.template = '';
        window.BixCart.el = '#bix-cart-checkout';
        window.$bixCart = new Vue(window.BixCart);
    } else {
        window.$bixCart = new Vue(window.BixCart).$mount().$appendTo('body');
    }

    window.$bixCartComponents.forEach(function (component) {
        if (UIkit.$(component.el).length) {
            new Vue(component);
        }
    });
});

module.exports = window.BixCart;
var Cart = Vue.extend(require('./components/cart.vue'));

require('./lib/filters')(Vue);

$(function () {

    window.$bixieCart = (new Cart()).$appendTo('body');

});

module.exports = Cart;
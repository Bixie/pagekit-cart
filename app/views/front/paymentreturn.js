module.exports = {

    name: 'paymentreturn',

    el: '#cart-paymentreturn',

    data: function () {
        return _.merge({
            order: {}
        }, window.$data);
    },

    created: function () {
        window.$bixCart.emptyCart();
    }

};


window.$bixCartComponents = window.$bixCartComponents || [];
window.$bixCartComponents.push(module.exports);
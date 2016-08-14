module.exports = {

    name: 'cart-widget',

    el: '#cart-widget',

    created: function () {
        this.$bixCart = window.$bixCart;
    }
};

window.$bixCartComponents = window.$bixCartComponents || [];
window.$bixCartComponents.push(module.exports);
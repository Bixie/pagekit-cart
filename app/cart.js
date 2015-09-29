var Cart = Vue.extend({

    data: function () {
        return _.merge({
            'cart_loading': false,
            'filters': {
                currency: window.$cart.config.currency || 'EUR'
            },
            'checkout_url': '',
            'config': {},
            'products': {},
            'gateways': [],
            'cartItems': window.$cartItems || []
        }, window.$cart);
    },

    created: function () {
        this.resource = this.$resource('api/cart/cart/:id');
        this.filters = _.assign(this.filters, JSON.parse((this.$localstorage('cart.filters') || '{}')));
    },


    methods: {
        addToCart: function (product) {
            var cartItem = _.merge({
                item_title: product.item.title,
                item_url: product.item.url,
                product_id: product.id
            }, product);
            delete cartItem['item'];
            delete cartItem['id'];
            this.cartItems.push(cartItem);
            this.saveCart();
            this.showCart();
        },

        showCart: function () {
            this.$broadcast('show.bixie.cart');
        },

        removeFromCart: function (idx) {
            this.cartItems.$remove(idx);
            this.saveCart();

        },

        saveCart: _.debounce(function () {
            console.log('save' + this.cartItems.length);
            this.cart_loading = true;
            this.resource.save({}, {cartItems: this.cartItems}, function (data) {
                console.log(data, this.cartItems.length);
                if (data.length == this.cartItems.length) { //todo this is bodgy (but works fine)
                    this.$set('cartItems', data);
                }
                this.cart_loading = false;
            });
        }, 700, {
            'leading': true,
            'trailing': true
        }),

        checkoutSubmit: function (e) {
            e.preventDefault();
            this.$.checkout.doCheckout();
        }
    },

    watch: {
        'filters': {
            handler: function (value) {
                this.$localstorage('cart.filters', JSON.stringify(value));
            },
            deep: true
        }
    },
    components: {
        cartmodal: require('./components/cartmodal.vue'),
        checkout: require('./components/checkout.vue'),
        addtocart: require('./components/addtocart.vue')
    },

    mixins: [
        require('./lib/carthelper'),
        require('./lib/currency')
    ]
});

Vue.use(require('./plugins/localstorage'));

$(function () {

    $('body').append('<div><cartmodal></cartmodal></div>');

    window.$bixieCart = (new Cart()).$mount('body');

});

module.exports = Cart;
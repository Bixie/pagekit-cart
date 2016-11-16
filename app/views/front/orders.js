module.exports = {

    name: 'orders',

    el: '#cart-orders',

    data: function () {
        return _.merge({
            user: {},
            orders: false,
            config: {
                filter: this.$session.get('bixie.site.cart.orders.filter', {
                    user_id: window.$data.user.id,
                    status: '',
                    search: '',
                    order: 'created desc',
                    limit: 20
                })
            },
            pages: 0,
            count: ''
        }, window.$data);
    },

    created: function () {
        this.Order = this.$resource('api/cart/order{/id}');
        this.$watch('config.page', this.load, {immediate: true});
    },

    computed: {

        statusOptions: function () {

            var options = _.map(this.statuses, function (status, id) {
                return { text: status, value: id };
            });

            return [{ label: this.$trans('Filter by'), options: options }];
        }
    },

    methods: {

        cartItems: function (order) {
            var cartItems = order.cartItems.map(function (cartItem) {
                return cartItem.title;
            });
            return cartItems.join(', ');
        },

        load: function () {
            if (!this.user.id) {
                return;
            }
            return this.Order.query(this.config).then(function (res) {
                this.$set('orders', res.data.orders);
                this.$set('pages', res.data.pages);
                this.$set('count', res.data.count);
            });
        },

        getStatusText: function (order) {
            return this.statuses[order.status];
        }

    },

    watch: {
        'config.filter': {
            handler: function (filter) {
                if (this.config.page) {
                    this.config.page = 0;
                } else {
                    this.load();
                }

                this.$session.set('bixie.site.cart.orders.filter', filter);
            },
            deep: true
        }
    },

    mixins: [require('../../lib/currency')]


};

window.$bixCartComponents = window.$bixCartComponents || [];
window.$bixCartComponents.push(module.exports);
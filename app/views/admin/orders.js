module.exports = {

    name: 'orders',

    el: '#cart-orders',

    mixins: [require('../../lib/currency')],

    data: function () {
        return _.merge({
            orders: false,
            config: {
                filter: this.$session.get('bixie.cart.orders.filter', {
                    status: '',
                    search: '',
                    order: 'created desc'
                })
            },
            pages: 0,
            count: '',
            selected: [],
            filters: {
                currency: window.$data.config.currency || 'EUR'
            }
        }, window.$data);
    },

    created: function () {
        this.resource = this.$resource('api/cart/order{/id}');
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

        active: function (order) {
            return this.selected.indexOf(order.id) !== -1;
        },

        load: function () {
            return this.resource.query(this.config).then(function (res) {
                this.$set('orders', res.data.orders);
                this.$set('pages', res.data.pages);
                this.$set('count', res.data.count);
                this.$set('selected', []);
            });

        },

        save: function (order) {
            this.resource.save({ id: order.id }, { order: order }, function (data) {
                this.load();
                this.$notify('Cart order saved.');
            });
        },

        status: function (status) {

            var orders = this.getSelected();

            orders.forEach(function (file) {
                file.status = status;
            });

            this.resource.save({ id: 'bulk' }, { orders: orders }, function (data) {
                this.load();
                this.$notify('Orders saved.');
            });
        },

        toggleStatus: function (order) {
            order.status = order.status === 0 ? 1 : 0;
            this.save(order);
        },

        getSelected: function () {
            return this.orders.filter(function (order) {
                return this.selected.indexOf(order.id) !== -1;
            }, this);
        },

        removeOrders: function () {

            this.resource.delete({id: 'bulk'}, {ids: this.selected}, function () {
                this.load();
                this.$notify('Order(s) deleted.');
            });
        },

        getStatusText: function (order) {
            return this.statuses[order.status];
        },

        getName: function (order) {
            if (order.user_name) {
                return order.user_name + ' (' + order.user_username + ')';
            }
            return order.data.billing_address.firstName + ' ' + order.data.billing_address.lastName;
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

                this.$session.set('bixie.cart.orders.filter', filter);
            },
            deep: true
        }

    }


};

Vue.ready(module.exports);

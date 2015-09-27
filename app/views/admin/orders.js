module.exports = {

    data: function () {
        return _.merge({
            orders: false,
            pages: 0,
            count: '',
            selected: [],
            filters: {
                currency: window.$data.config.currency || 'EUR'
            }
        }, window.$data);
    },

    created: function () {
        this.resource = this.$resource('api/cart/order/:id');
        this.config.filter = _.extend({
            status: '',
            search: '',
            order: 'created desc',
            limit: this.config.orders_per_page
        }, this.config.filter);
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
                return cartItem.item_title;
            });
            return cartItems.join(', ');
        },

        active: function (portfolio) {
            return this.selected.indexOf(portfolio.id) != -1;
        },

        load: function (page) {

            page = page !== undefined ? page : this.config.page;

            return this.resource.query({ filter: this.config.filter, page: page }, function (data) {
                this.$set('orders', data.orders);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
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
            return order.data.billing_address.firstName + ' ' + order.data.billing_address.lastName;
        }

    },

    watch: {
        'config.page': 'load',

        'config.filter': {
            handler: function () { this.load(0); },
            deep: true
        }
    },

    mixins: [require('../../lib/currency')]


};

$(function () {

    new Vue(module.exports).$mount('#cart-orders');

});


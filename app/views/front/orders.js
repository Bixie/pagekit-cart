module.exports = {

    data: function () {
        return _.merge({
            user: {},
            orders: false,
            pages: 0,
            count: ''
        }, window.$data);
    },

    created: function () {
        this.resource = this.$resource('api/cart/order/:id');
        this.config.filter = _.extend({
            user_id: this.user.id,
            status: '',
            search: '',
            order: 'created desc',
            limit: 20
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

        load: function (page) {

            if (!this.user.id) {
                return;
            }

            page = page !== undefined ? page : this.config.page;

            return this.resource.query({ filter: this.config.filter, page: page }, function (data) {
                this.$set('orders', data.orders);
                this.$set('pages', data.pages);
                this.$set('count', data.count);
                this.$set('config.page', page);
            });
        },

        getStatusText: function (order) {
            return this.statuses[order.status];
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

    new Vue(module.exports).$mount('#bixie-orders');
    Vue.asset({
        css: [
            'app/assets/uikit/css/components/form-select.css'
        ]
    }, function () {
    });

});


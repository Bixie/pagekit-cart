
module.exports = Vue.extend({

    data: function () {
        return _.merge({
            tags: [],
            order: {}
        }, window.$data);
    },

    ready: function () {
        this.resource = this.$resource('api/cart/order{/id}');
        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
    },

    computed: {

        statusOptions: function () {
            return _.map(this.statuses, function (status, id) { return { text: status, value: id }; });
        },

        userOptions: function () {
            var options = this.users.map(function (user) { return { text: user.username, value: user.id }; });
            options.unshift({ text: this.$trans('Guest user'), value: 0 });
            return options;

        }
    },

    filters: {
        nl2br: function (str) {
            //  discuss at: http://phpjs.org/functions/nl2br/
            return String(str).replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
        }
    },

    methods: {

        save: function (e) {

            e.preventDefault();

            var data = {order: this.order};

            this.$broadcast('save', data);

            this.resource.save({id: this.order.id}, data, function (data) {

                if (!this.order.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/cart/order/edit', {id: data.file.id}));
                }

                this.$set('order', data.order);

                this.$notify(this.$trans('Order %transaction_id% saved.', {transaction_id: this.order.transaction_id}));

            }, function (data) {
                this.$notify(data, 'danger');
            });
        },

        getStatusText: function(order) {
            return this.statuses[order.status];
        }

    }

});

$(function () {

    (new module.exports()).$mount('#order-edit');

});

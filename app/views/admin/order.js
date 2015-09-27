
module.exports = Vue.extend({

    data: function () {
        return _.merge({
            tags: [],
            order: {}
        }, window.$data);
    },

    created: function () {
    },

    ready: function () {
        this.resource = this.$resource('api/cart/order/:id');
        this.tab = UIkit.tab(this.$$.tab, {connect: this.$$.content});
    },

    computed: {

        statusOptions: function () {
            return _.map(this.statuses, function (status, id) { return { text: status, value: id }; });
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

                this.$notify(this.$trans('Download %title% saved.', {title: this.order.title}));

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

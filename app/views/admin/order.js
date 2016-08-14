
window.CartOrder = {

    name: 'order',

    el: '#cart-order',

    data: function () {
        return _.merge({
            order: {},
            statuses: {},
            users: [],
            sections: [],
            form: {}
        }, window.$data);
    },

    created: function () {

        var sections = [];

        _.forIn(this.$options.components, function (component, name) {
            var options = component.options || {};
            if (options.section) {
                sections.push(_.extend({name: name, priority: 0}, options.section));
            }
        });

        this.$set('sections', _.sortBy(sections, 'priority'));

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

        save: function () {

            this.$resource('api/cart/order{/id}').save({id: this.order.id}, {order: this.order}).then(function (res) {

                if (!this.order.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/cart/order/edit', {id: res.data.file.id}));
                }

                this.order = res.data.order;

                this.$notify(this.$trans('Order %transaction_id% saved.', {transaction_id: this.order.transaction_id}));

            }, function (res) {
                this.$notify(res.data.message || res.data, 'danger');
            });
        },

        getStatusText: function (order) {
            return this.statuses[order.status];
        }

    },
    components: {}

};

Vue.ready(window.CartOrder);

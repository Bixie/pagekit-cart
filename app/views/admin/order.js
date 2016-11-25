
window.CartOrder = {

    name: 'order',

    el: '#cart-order',

    data() {
        return _.assign({
            fetching: false,
            order: {},
            statuses: {},
            users: [],
            sections: [],
            form: {}
        }, window.$data);
    },

    created() {

        let sections = [];

        _.forIn(this.$options.components, (component, name) => {
            let options = component.options || {};
            if (options.section) {
                if (name === 'order-section-emailsender:communication' && !this.templates.length) {
                    return;
                }
                sections.push(_.assign({name: name, priority: 0}, options.section));
            }
        });

        this.$set('sections', _.sortBy(sections, 'priority'));

    },

    computed: {

        statusOptions() {
            return _.map(this.statuses, (status, id) => { return { text: status, value: id }; });
        },

        userOptions() {
            let options = this.users.map(user => { return { text: user.username, value: user.id }; });
            options.unshift({ text: this.$trans('Guest user'), value: 0 });
            return options;
        }
    },

    filters: {
        nl2br(str) {
            //  discuss at: http://phpjs.org/functions/nl2br/
            return String(str).replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
        }
    },

    methods: {

        save() {

            this.$resource('api/cart/order{/id}').save({id: this.order.id}, {order: this.order}).then(res => {
                if (!this.order.id) {
                    window.history.replaceState({}, '', this.$url.route('admin/cart/order/edit', {id: res.data.file.id}));
                }
                this.order = res.data.order;

                this.$notify(this.$trans('Order %transaction_id% saved.', {transaction_id: this.order.transaction_id}));

            }, res => {
                this.$notify(res.data.message || res.data, 'danger');
            });
        },

        fetchTransaction() {
            this.fetching = true;
            this.$resource('api/cart/order/fetchtransaction{/id}').save({id: this.order.id}, {}).then(res => {
                this.order = res.data.order;

                this.$notify(this.$trans('Transactiondata %transaction_id% fetched.', {transaction_id: this.order.transaction_id}));
                this.fetching = false;

            }, res => {
                this.$notify(res.data.message || res.data, 'danger');
                this.fetching = false;
            });
        },

        getStatusText(order) {
            return this.statuses[order.status];
        }

    },
    components: {
        'order-section-emailsender:communication': {

            section: {
                label: 'Communication',
                priority: 5
            },

            props: ['order', 'config', 'form'],

            template: '<email-communication :templates="templates" :ext_key="`bixie.cart.order.${order.id}`" :user_id="order.user_id" :email-data="emailData" :attachments="attachments"></email-communication>',

            data() {
                return {
                    templates: window.$data.templates,
                    emailData: {},
                    attachments: []
                };
            },
            created() {
                this.emailData = {order: this.order};
                this.attachments = this.order.attachments;
            }
        }
    }

};

Vue.ready(window.CartOrder);

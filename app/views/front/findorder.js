module.exports = Vue.extend({

    name: 'find-order',

    el: '#bixie-findorder',

    data: function () {
        return {
            loading: false,
            alert: '',
            step: 1,
            transaction_id: '',
            email: '',
            username: '',
            password: ''
        };
    },

    created: function () {
        this.Order = this.$resource('api/cart/order{/task}');
    },

    methods: {

        submitForm: function () {
            this.$set('loading', true);
            this.$set('alert', '');
            if (this.step === 1) {
                this.lookUp();
            }
            if (this.step === 2) {
                this.register();
            }
        },

        lookUp: function () {

            this.Order.save({task: 'findorder'}, {
                transaction_id: this.transaction_id,
                email: this.email
            }).then(function (res) {
                this.$set('loading', false);
                if (res.data.success) {
                    this.$set('step', 2);
                } else if (res.data.error) {
                    this.$set('alert', res.data.error);
                }
            });

        },

        register: function () {
            this.Order.save({task: 'register'}, {
                transaction_id: this.transaction_id,
                user: {
                    email: this.email,
                    username: this.username,
                    password: this.password
                }
            }).then(function (res) {
                this.$set('loading', false);
                if (res.data.success) {
                    this.$set('step', 3);
                } else if (res.data.error) {
                    this.$set('alert', res.data.error);
                }
            });

        }
    }

});

$(function () {

    (new module.exports()).$mount('#bixie-findorder');

});

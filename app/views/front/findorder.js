module.exports = Vue.extend({

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
        this.resource = this.$resource('api/cart/order/:task');
    },

    methods: {

        submitForm: function () {
            this.$set('loading', true);
            this.$set('alert', '');
            if (this.step == 1) {
                this.lookUp();
            }
            if (this.step == 2) {
                this.register();
            }
        },

        lookUp: function () {

            this.resource.save({task: 'findorder'}, {
                transaction_id: this.transaction_id,
                email: this.email
            }, function (data) {
                this.$set('loading', false);
                if (data.success) {
                    this.$set('step', 2);
                } else if (data.error) {
                    this.$set('alert', data.error);
                }
            });

        },

        register: function () {
            this.resource.save({task: 'register'}, {
                transaction_id: this.transaction_id,
                user: {
                    email: this.email,
                    username: this.username,
                    password: this.password
                }
            }, function (data) {
                this.$set('loading', false);
                if (data.success) {
                    this.$set('step', 3);
                } else if (data.error) {
                    this.$set('alert', data.error);
                }
            });

        }
    }

});

$(function () {

    (new module.exports()).$mount('#bixie-findorder');

});

module.exports = Vue.extend({

    data: function () {
        return window.$data;
    },

    fields: require('../../settings/fields'),

    methods: {

        save: function () {
            this.$http.post('admin/system/settings/config', { name: 'bixie/cart', config: this.config }, function () {
                this.$notify('Settings saved.');
            }).error(function (data) {
                this.$notify(data, 'danger');
            });
        }
    },

    computed: {
        vatOptions: function () {
            var options = [];
            _.forIn(this.config.vatclasses, function (vatclass, value) {
                options.push({value: value, text: vatclass.name});
            });
            return options;
        }
    }

});

$(function () {

    (new module.exports()).$mount('#cart-settings');

});

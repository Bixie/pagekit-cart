module.exports = {

    name: 'settings',

    el: '#cart-settings',

    data: function () {
        return window.$data;
    },

    fields: require('../../settings/fields'),

    methods: {

        save: function () {
            this.$http.post('admin/system/settings/config', { name: 'bixie/cart', config: this.config }).then(function () {
                this.$notify('Settings saved.');
            }, function (res) {
                this.$notify(res.data, 'danger');
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

};

Vue.ready(module.exports);

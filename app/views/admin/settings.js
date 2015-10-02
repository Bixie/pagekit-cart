module.exports = Vue.extend({

    data: function () {
        return window.$data;
    },

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
        timezoneOptions: function () {
            var options = [];
            _.forIn(this.timezones, function (zones, continent) {
                options.push({
                    label: continent,
                    options: (function (zones) {
                        var zoneOptions = [];
                        _.forIn(zones, function (zone, code) {
                            zoneOptions.push({value: code, text: zone});
                        });
                        return zoneOptions;
                    }(zones))
                });
            });
            return options;
        },
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

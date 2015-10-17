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

Vue.field.templates.formrow = require('../../templates/formrow.html');
Vue.field.templates.raw = require('../../templates/raw.html');
Vue.field.types.checkbox = '<p class="uk-form-controls-condensed"><label><input type="checkbox" v-attr="attrs" v-model="value"> {{ optionlabel | trans }}</label></p>';
Vue.field.types.number = '<input type="number" v-attr="attrs" v-model="value" number>';
Vue.field.types.title = '<h3 v-attr="attrs">{{ title | trans }}</h3>';

$(function () {

    (new module.exports()).$mount('#cart-settings');

});

module.exports = Vue.extend({

    data: function () {
        return _.merge({
            'config': {},
            'products': []
        }, window.$cart);
    },

    methods: {
    },

    components: {

        'addtocart': require('./components/addtocart.vue')

    }

});

require('./lib/filters')(Vue);

$(function () {

    (new module.exports()).$mount('.bixie-addtocart');

});

module.exports = Vue.extend({

    data: function () {
        return window.$data;
    },

    methods: {

    }

});

$(function () {

    (new module.exports()).$mount('#cart');

});

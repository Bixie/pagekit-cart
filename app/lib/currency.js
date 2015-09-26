
var icons = {
    'EUR': 'uk-icon-euro',
    'USD': 'uk-icon-dollar'
}, number_format = require('./number_format');

module.exports = {

    methods: {

        calcVat: function (product) {
            var netto = this.convertPrice(product.price, product),
                vatclass = window.$cart.config.vatclasses[product.vat],
                bruto = (Math.round((netto * 100) * ((vatclass.rate / 100) + 1))) / 100;
            return {
                netto: netto,
                bruto: bruto,
                vat: bruto - netto,
                vatclass: vatclass
            };
        },

        convertPrice: function (price, product) {
            price = Number(price);

            if (product && product.currency !== this.filters.currency) {
                price = (Math.round((price * 100) * (window.$cart.config[product.currency + 'to' + this.filters.currency] || 1))) / 100;
            }
            return price;
        },

        productprice: function (product) {
            var price = Number(product.price);

            if (this.config.vat_view === 'incl') {
                price = this.inclVat(product);
            }

            if (product && product.currency !== this.filters.currency) {
                price = this.convertPrice(price, product);
            }

            return this.formatprice(price);
        },

        inclVat: function (product) {
            var vat = this.calcVat(product);
            return vat.bruto;
        },

        getVat: function (product) {
            var vat = this.calcVat(product);
            return vat.vat;
        },

        formatprice: function (price) {
            var icon = '<i class="' + icons[this.filters.currency || 'EUR'] + ' uk-margin-small-right"></i>',
                numberString;
            try {
                numberString = price.toLocaleString(window.$trans.locale, {minimumFractionDigits: 2});
            } catch (ignore) {
                numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
            }
            return icon + numberString;
        }

    },

    filters: {
        productprice: function (product) {
            return this.productprice(product);
        },

        inclVat: function (product) {
            return this.inclVat(product);
        },

        getVat: function (product) {
            return this.getVat(product);
        },

        formatprice: function (price) {
            return this.formatprice(price);
        }

    },

    components: {
        cartlist: require('../components/cartlist.vue')
    }

};
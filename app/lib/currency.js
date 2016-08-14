var icons = {
    'EUR': 'uk-icon-euro',
    'USD': 'uk-icon-dollar'
}, number_format = require('./number_format');

var Currency = function ($bixCart) {

    "use strict";

    function currentCurrency() {
        return $bixCart.filter.currency || $bixCart.config.currency || 'EUR';
    }

    function convertPrice(price, options) {
        price = Number(price);

        if (options && options.currency !== currentCurrency()) {
            price = (Math.round((price * 100) * ($bixCart.config[options.currency + 'to' + currentCurrency()] || 1))) / 100;
        }
        return price;
    }

    function calcVat(options) {
        var netto = convertPrice(options.price, options),
            vatclass = $bixCart.config.vatclasses[options.vat || 'high'],
            bruto = (Math.round((netto * 100) * ((vatclass.rate / 100) + 1))) / 100;
        return {
            netto: netto,
            bruto: bruto,
            vat: bruto - netto,
            vatclass: vatclass
        };
    }

    function inclVat(options) {
        var vat = calcVat(options);
        return vat.bruto;
    }

    function getVat(options) {
        var vat = calcVat(options);
        return vat.vat;
    }

    function formatprice(price, currency) {
        var icon = '<i class="' + icons[currency || currentCurrency()] + ' uk-margin-small-right"></i>',
            numberString;
        try {
            numberString = price.toLocaleString(window.$trans.locale, {minimumFractionDigits: 2});
        } catch (ign) {
            numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
        }
        return icon + numberString;
    }


    function productPrice(options) {
        var price = Number(options.price);

        if ($bixCart.config.vat_view === 'incl') {
            price = inclVat(options);
        }

        if (options && options.currency !== currentCurrency()) {
            price = convertPrice(price, options);
        }

        return formatprice(price);
    }

    function formatPrice(price, currency) {
        var icon = '<i class="' + icons[currency] + ' uk-margin-small-right"></i>',
            numberString;
        try {
            numberString = price.toLocaleString(window.$trans.locale, {minimumFractionDigits: 2});
        } catch (ign) {
            numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
        }
        return icon + numberString;
    }



    return {
        /**
         * Formats the given price.
         *
         * @return {String}               The formatted price
         * @api public
         * @param price
         * @param currency
         */
        formatPrice: function (price, currency) {
            currency = currency || currentCurrency();
            return formatPrice(Number(price), currency);
        },

        /**
         * Formats the given price options.
         *
         * @return {String}               The formatted price
         * @api public
         * @param {Object} options        {price: Number, currency: 'EUR|USD', vat: 'high|low|none'}
         */
        productPrice: function (options) {
            return productPrice(Object.assign({price: 0, currency: 'EUR', vat: 'high'}, options));
        },

        /**
         * Formats a given price including VAT
         *
         * @return {String}               The formatted price
         * @api public
         * @param {Object} options        {price: Number, currency: 'EUR|USD', vat: 'high|low|none'}
         */
        inclVat: function (options) {
            return inclVat(Object.assign({price: 0, currency: 'EUR', vat: 'high'}, options));
        },

        /**
         * Formats a given price VAT amount
         *
         * @return {String}               The formatted price
         * @api public
         * @param {Object} options        {price: Number, currency: 'EUR|USD', vat: 'high|low|none'}
         */
        getVat: function (options) {
            return getVat(Object.assign({price: 0, currency: 'EUR', vat: 'high'}, options));
        }


    };
};

module.exports = function (Vue, $bixCart) {

    Vue.prototype.$cartCurrency = new Currency($bixCart);

    Vue.filter('formatprice', function (price, currency) {
        return this.$cartCurrency.formatPrice(price, currency);
    });
    Vue.filter('productprice', function (options) {
        return this.$cartCurrency.productPrice(options);
    });

};

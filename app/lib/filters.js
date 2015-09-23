module.exports = function (Vue) {
    var icons = {
        'EUR': 'uk-icon-euro',
        'USD': 'uk-icon-dollar'
    };

    function number_format(number, decimals, dec_point, thousands_sep) {
        //  discuss at: http://phpjs.org/functions/number_format/
        number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                        .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
                .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }

    Vue.filter('datetime', function (date) {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        return date ? this.$date(date, 'mediumDate') + ', ' + this.$date(date, 'HH:mm:ss') : '';
    });

    Vue.filter('currency', function (price, product) {

        var siteCurrency = window.$bixieCart.currency || 'EUR',
            icon = '<i class="' + icons[siteCurrency] + ' uk-margin-small-right"></i>',
            numberString;

        if (product && product.currency !== siteCurrency) {
            price = window.$bixieCart.convertPrice(price, product);
        }

        try {
            numberString = Number(price).toLocaleString(window.$locale.locale.replace('_', '-'), {minimumFractionDigits: 2});
        } catch (ignore) {
            numberString = number_format(price, 2, window.$locale.NUMBER_FORMATS.DECIMAL_SEP, window.$locale.NUMBER_FORMATS.GROUP_SEP);
        }
        return icon + numberString;
    });
};
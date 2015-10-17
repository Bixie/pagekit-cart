
var options = require('./options');

module.exports = {
    cart: {
        'currency': {
            type: 'select',
            label: 'Default currency',
            options: {
                'Euro': 'EUR',
                'Dollar': 'USD'
            },
            attrs: {'class': 'uk-form-width-medium'}
        },
        'vat_view': {
            type: 'select',
            label: 'VAT display',
            options: {
                'Show prices including VAT': 'incl',
                'Show prices excluding VAT': 'excl'
            },
            attrs: {'class': 'uk-form-width-medium'}
        },
        'title1': {
            raw: true,
            type: 'title',
            label: '',
            title: 'Add to cart buttons',
            attrs: {'class': 'uk-margin-top'}
        },
        'show_vat': {
            type: 'checkbox',
            label: 'VAT',
            optionlabel: 'Show VAT amount'
        }
    },
    general: {
        'orders_per_page': {
            type: 'number',
            label: 'Orders per page',
            attrs: {'class': 'uk-form-width-small'}
        },
        'server_tz': {
            type: 'select',
            label: 'Server timezone',
            options: options.timezoneOptions(),
            attrs: {'class': 'uk-form-width-medium'}
        },
        'validation_key': {
            label: 'Validation key',
            attrs: {'class': 'uk-form-width-large'}
        },
        'USDtoEUR': {
            type: 'number',
            label: 'USD to EUR conversion rate',
            attrs: {'class': 'uk-form-width-small uk-text-right'}
        },
        'EURtoUSD': {
            type: 'number',
            label: 'EUR to USD conversion rate',
            attrs: {'class': 'uk-form-width-small uk-text-right'}
        }

    }


};
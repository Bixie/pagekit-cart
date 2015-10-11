<template>

    <div v-show="!cartItems.length" class="uk-alert">{{ 'No items in cart yet' | trans }}</div>

    <ul v-show="cartItems.length" class="uk-list uk-list-line">
        <li v-repeat="cartItem: cartItems">
            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-1-2">
                    <a href="{{ cartItem.item_url}}">{{ cartItem.item_title  }}</a>
                </div>
                <div class="uk-width-medium-1-2 uk-text-right">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-1-2">
                            <a v-on="click: removeFromCart($index)" class="uk-icon-trash-o uk-icon-justify uk-icon-hover"></a>
                        </div>
                        <div class="uk-width-1-2">
                            {{{ cartItem | productprice }}}
                        </div>
                    </div>
                </div>
            </div>
        </li>
        <li>
            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-3-4 uk-text-right uk-text-small">
                    <div v-if="config.vat_view == 'incl' || isCheckout"><span>{{ 'Total excluding taxes' | trans }}</span> {{{ totalNetto | formatprice }}}</div>
                    <div><span>{{ 'Total taxes' | trans }}</span> {{{ totalTaxes | formatprice }}}</div>
                    <div v-if="config.vat_view == 'excl' && !isCheckout"><span>{{ 'Total including taxes' | trans }}</span> {{{ totalBruto | formatprice }}}</div>
                </div>
                <div class="uk-width-medium-1-4 uk-text-right">
                    <h3 v-if="config.vat_view == 'incl' || isCheckout">{{{ totalBruto | formatprice }}}</h3>
                    <h3 v-if="config.vat_view == 'excl' && !isCheckout">{{{ totalNetto | formatprice }}}</h3>
                </div>
            </div>
        </li>
    </ul>

</template>

<script>

    module.exports = {

        inherit: true,

        props: ['isCheckout'],

        methods: {
            removeFromCart: function (idx) {
                window.$bixieCart.removeFromCart(idx);
            }
        }


    };

</script>

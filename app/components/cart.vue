<template>

    <v-modal v-ref="cartmodal" large>
        <div class="uk-modal-header">
            <h3>{{ 'Items in cart' | trans }}</h3>
        </div>
        <div class="uk-margin">
            <div v-show="!cartItems.length" class="uk-alert">{{ 'No items in cart yet' | trans }}</div>
            <ul v-show="cartItems.length" class="uk-list uk-list-line">
                <li v-repeat="cartItem: cartItems">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-medium-3-4">
                            <a href="{{ cartItem.item_url}}">{{ cartItem.item_title  }}</a>
                        </div>
                        <div class="uk-width-medium-1-4 uk-text-right">
                            {{{ cartItem.price | currency }}}
                        </div>
                    </div>
                </li>
                <li>
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-medium-3-4">
                        </div>
                        <div class="uk-width-medium-1-4 uk-text-right">
                            {{{ totalNetto | currency }}}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="uk-button uk-modal-close">{{ 'Close' | trans }}</button>
            <button type="button" class="uk-button uk-button-success uk-margin-left">
                <i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'To checkout' | trans }}</button>
        </div>

    </v-modal>
<pre>{{$data.cartItems|json}}</pre>
</template>

<script>
    module.exports = {

        el: function () {
            return document.createElement('div');
        },

        data: function () {
            return {
                'config': window.$cart.config,
                'cartItems': []
            };
        },

        created: function () {
            this.$on('add.bixie.cart', function (product) {
                this.addToCart(product);
            });
            this.$on('show.bixie.cart', function () {
                this.$.cartmodal.open();
            });
        },

        computed: {
            totalNetto: function () {
                var total = 0;
                this.cartItems.forEach(function (cartItem) {
                    total += parseFloat(cartItem.price);
                });
                return total;
            }
        },

        methods: {
            addToCart: function (product) {
                var cartItem = _.merge({
                    item_title: product.item.title,
                    item_url: product.item.url
                }, product);
                delete cartItem['item'];
                this.cartItems.push(cartItem);
                this.$.cartmodal.open();
            }
        }

    };

</script>
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
                            {{{ cartItem.price | currency cartItem}}}
                        </div>
                    </div>
                </li>
                <li>
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-medium-3-4 uk-text-right uk-text-small">
                            <div><span>{{ 'Total taxes' | trans }}</span> {{{ totalTaxes | currency }}}</div>
                            <div><span>{{ 'Total including taxes' | trans }}</span> {{{ totalBruto | currency }}}</div>
                        </div>
                        <div class="uk-width-medium-1-4 uk-text-right">
                            <h3>{{{ totalNetto | currency }}}</h3>
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
    <pre>{{ totalNetto | json}}</pre>
    <pre>{{$data.cartItems | json}}</pre>
</template>

<script>
    module.exports = {

        el: function () {
            return document.createElement('div');
        },

        data: function () {
            return {
                'currency': window.$cart.config.currency,
                'config': window.$cart.config,
                'cartItems': _.merge([], window.$cartItems)
            };
        },

        created: function () {
            this.resource = this.$resource('api/cart/cart/:id');
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
                    total += this.convertPrice(cartItem.price, cartItem);
                }.bind(this));
                return total;
            },
            totalBruto: function () {
                var total = 0;
                this.cartItems.forEach(function (cartItem) {
                    var vat = this.calcVat(cartItem);
                    total += vat.bruto;
                }.bind(this));
                return total;
            },
            totalTaxes: function () {
                return this.totalBruto - this.totalNetto
            }

        },

        methods: {
            addToCart: function (product) {
                var cartItem = _.merge({
                    item_title: product.item.title,
                    item_url: product.item.url,
                    product_id: product.id
                }, product);
                delete cartItem['item'];
                delete cartItem['id'];
                this.cartItems.push(cartItem);
                this.saveCart();
                this.$.cartmodal.open();
            },

            saveCart: function () {
                this.resource.save({}, { cartItems: this.cartItems }, function (data) {
                    console.log(data);
                    this.$notify('Cart updated.');
                });
            },

            calcVat: function (product) {
                var netto = this.convertPrice(product.price, product),
                    vatclass = this.config.vatclasses[product.vat];
                return {
                    netto: netto,
                    bruto: (Math.round((netto * 100) * ((vatclass.rate / 100) + 1))) / 100,
                    vatclass: vatclass
                }
            },

            convertPrice: function (price, product) {
                var siteCurrency = this.currency || 'EUR';
                price = Number(price);
                if (product && product.currency !== siteCurrency) {
                    price = (Math.round((price * 100) * (window.$cart.config[product.currency + 'to' + siteCurrency] || 1))) / 100;
                }
                return price;
            }
        }

    };

</script>
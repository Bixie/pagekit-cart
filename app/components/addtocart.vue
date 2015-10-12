<template>

    <div class="uk-flex uk-flex-space-around uk-flex-wrap">
        <div class="{{ priceHldr }}">
            <strong>{{{ product | productprice }}}</strong>
            <div v-if="config.addtocart.show_vat"><small>{{{ includingVat }}}</small></div>
        </div>
        <div class="{{ buttonHldr }}">
            <button type="button" class="uk-button uk-button-success uk-width-1-1" v-on="click: addToCart(product)">
                <i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'Add to cart' | trans }}
            </button>
        </div>
    </div>

</template>

<script>

    module.exports = {

        data: function () {
            return {
                priceHldr: 'uk-text-right',
                buttonHldr: ''
            };
        },

        props: ['product', 'item_id', 'buttonHldr', 'priceHldr'],

        inherit: true,

        computed: {
            includingVat: function () {
                var vatString = this.formatprice(this.getVat(this.product)),
                        text = this.config.vat_view == 'excl' ? '+ %vat% VAT' : 'incl. %vat% VAT';
                return this.$trans(text, {vat: vatString});
            }
        }

    };

</script>

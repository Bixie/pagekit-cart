<template>
    <div class="uk-flex uk-flex-middle uk-flex-center uk-flex-space-around uk-flex-wrap uk-margin" data-uk-margin="">
        <div class="uk-text-right">
            <strong>{{{ product | productprice }}}</strong>
            <div v-if="config.addtocart.show_vat"><small>{{{ includingVat }}}</small></div>
        </div>
        <div class="">
            <button type="button" class="uk-button uk-button-success" v-on="click: addToCart(product)">
                <i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'Add to cart' | trans }}
            </button>
        </div>

    </div>

</template>

<script>

    module.exports = {

        props: ['product', 'item_id'],

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

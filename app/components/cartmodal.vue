<template>

    <v-modal v-ref="cartmodal" large>
        <div class="uk-modal-header">
            <h3>{{ 'Items in cart' | trans }}</h3>
        </div>
        <div class="uk-margin">
            <cartlist></cartlist>
        </div>
        <div class="uk-modal-footer uk-text-right">
            <button type="button" class="uk-button uk-modal-close">{{ 'Close' | trans }}</button>
            <a v-attr="href: checkout_url" class="uk-button uk-button-success uk-margin-left">
                <i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'To checkout' | trans }}</a>
        </div>

    </v-modal>
    <pre>{{ filters.currency | json}}</pre>
    <pre>{{ totalNetto | json}}</pre>
</template>

<script>
    module.exports = {


        inherit: true,

        ready: function () {
            this.$on('show.bixie.cart', function () {
                this.$.cartmodal.open();
            });
        }

    };

</script>
<template>

    <div>
        <partial :name="item.template"></partial>
    </div>

</template>


<script>

    module.exports = {

        props: {
            item: {type: Object}
        },

        created: function () {
            this.$options.partials = this.$root.$options.partials;
            this.$bixCart = window.$bixCart;
        },
        watch: {
            'item.quantity': function (qty) {
                var price = _.find(this.item.quantity_options, 'quantity', qty);
                console.log('quaCh', price.price);
                this.item.price = price.price * qty;
                this.item.currency = price.currency;
            }
        },
        events: {
        }

    };


</script>

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
            this.$bixCart = this.$root;
        },
        watch: {
            'item.quantity': function (qty) {
                var quantity = _.find(this.item.quantity_data.quantities, qanty => (qanty.min_quantity <= qty && qanty.max_quantity >= qty));
                this.item.price = quantity.price * (this.item.quantity_data.type === 'piece' ? qty : 1);
                this.item.currency = quantity.currency;
            }
        },
        components: {
            'cart-item-quantity': require('./cart-item-quantity.vue')
        }

    };


</script>

<template>

    <div>
        <partial :name="item.template"></partial>
    </div>

</template>


<script>

    module.exports = {

        props: {
            item: {type: Object},
            hash: ''
        },

        created() {
            this.$options.partials = this.$root.$options.partials;
            this.$bixCart = this.$root;
            this.$watch('hash + item.quantity', _.debounce((newVal, oldVal) => {
                if (oldVal && newVal) {
                    this.$bixCart.saveCart();
                }
            }, 800, {leading: true}));
        },
        components: {
            'cart-item-quantity': require('./cart-item-quantity.vue')
        }

    };


</script>

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

        created: function () {
            this.$options.partials = this.$root.$options.partials;
            this.$bixCart = this.$root;
        },
        components: {
            'cart-item-quantity': require('./cart-item-quantity.vue')
        }

    };


</script>

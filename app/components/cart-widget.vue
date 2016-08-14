<template>

    <div>
        <div v-if="$bixCart.error" class="uk-alert uk-alert-danger">{{ $bixCart.error }}</div>

        <h3>Winkelwagen items</h3>
        <ul class="uk-list uk-list-line">
            <li v-for="item in $bixCart.cart.items">
                <cart-item :item.sync="item"></cart-item>
            </li>
        </ul>
        <button type="button" class="uk-button" @click="$bixCart.emptyCart">Leeg winkelwagen</button>

        <cart-address class="uk-margin" v-if="$bixCart.cart.items.length" :address.sync="$bixCart.cart.DeliveryAddress" :countries="countries"></cart-address>

        <cart-delivery class="uk-margin" v-if="$bixCart.cart.DeliveryAddress.City"></cart-delivery>

        <button v-show="$bixCart.cart.DeliveryOptionId" type="button" class="uk-button uk-button-primary" @click="$bixCart.placeOrder">Bestellen</button>

    </div>


</template>


<script>

    module.exports = {

        props: {
        },

        data: function () {
            return _.assign({
                countries: {}
            }, window.$data);
        },

        created: function () {
            this.$bixCart = window.$bixCart;

        },
        methods: {
        },
        components: {
            'cart-item': require('./cart-item.vue'),
            'cart-address': require('./cart-address.vue'),
            'cart-delivery': require('./cart-delivery.vue')
        }

    };

    window.$bixCartComponents = window.$bixCartComponents || [];
    window.$bixCartComponents.push(module.exports);


</script>

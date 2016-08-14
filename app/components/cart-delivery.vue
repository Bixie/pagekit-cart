<template>
    <div class="uk-form">

        <ul v-if="$bixCart.cart.delivery_options.length" class="uk-list uk-list-line">
            <li v-for="delivery_option in $bixCart.cart.delivery_options">
                <label class="uk-flex uk-flex-middle uk-flex-space-between">
                    <div class="uk-margin-right">
                        <input type="radio" v-model="$bixCart.cart.delivery_option_id" :value="delivery_option.id"/>
                    </div>
                    <div class="uk-flex-item-1">
                        <strong>{{ getDaysFormat(delivery_option.business_days) }}</strong> - {{ delivery_option.eta_date | date}}
                        <span v-if="delivery_option.price"><br>{{{ delivery_option.price | formatprice }}}</span>
                    </div>
                </label>
            </li>
        </ul>
        <p v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></p>


    </div>

</template>


<script>

    module.exports = {

        props: {
        },

        created: function () {
            this.$bixCart = window.$bixCart;
            if ($bixCart.cart.delivery_option_id === '' && $bixCart.cart.delivery_options.length) {
                $bixCart.cart.delivery_option_id = _.first($bixCart.cart.delivery_options).id
            }
        },

        methods: {
            getDaysFormat: function (days) {
                return this.$transChoice('{0} %count% days|{1} %count% day|]1,Inf[ %count% days',
                        days, {count: days}
                );
            }
        }
    };


</script>

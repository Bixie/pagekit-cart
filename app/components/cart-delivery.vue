<template>
    <div class="uk-form">

        <ul v-if="!$bixCart.saving" class="uk-list uk-list-line">
            <li v-for="delivery_option in deliveryOptions.options">
                <label class="uk-flex uk-flex-middle uk-flex-space-between">
                    <div class="uk-margin-right">
                        <input type="radio" v-model="$bixCart.cart.delivery_option_id" :value="delivery_option.option_id"/>
                    </div>
                    <div class="uk-flex-item-1">
                        <strong>{{ getDaysFormat(delivery_option.business_days) }}</strong> - {{
                        delivery_option.eta_date | date}}
                        <span v-if="delivery_option.price"><br>{{{ {price: delivery_option.price} | productprice }}}</span>
                    </div>
                </label>
            </li>
            <li v-for="message in deliveryOptions.messages" class="uk-text-primary">{{ message }}</li>
            <li v-for="error in deliveryOptions.errors" class="uk-text-danger">{{ error }}</li>
        </ul>
        <p v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></p>

        <div v-if="$bixCart.validating && !valid" class="uk-alert uk-alert-danger">
            {{ 'Please select a delivery option' | trans }}
        </div>

        <div v-if="$bixCart.deliveryerror" class="uk-alert uk-alert-danger">
            {{ deliveryerror }}
        </div>

    </div>

</template>


<script>

    module.exports = {

        props: {
        },

        created: function () {
            this.$bixCart = this.$root;
            this.checkValue();
            this.$watch('$bixCart.cart.deliveryOptions.options', this.checkValue);
        },

        computed: {
            valid() {
                return !!this.$bixCart.cart.delivery_option_id;
            },
            countOptions() {
                return $bixCart.cart.deliveryOptions.options && $bixCart.cart.deliveryOptions.options.length;
            },
            deliveryOptions() {
                return $bixCart.cart.deliveryOptions;
            }
        },

        methods: {
            checkValue() {
                if (this.countOptions) {
                    var current = _.find(this.deliveryOptions.options, {option_id: this.$bixCart.cart.delivery_option_id});
                    if (!current) {
                        this.$bixCart.cart.delivery_option_id = _.first(this.deliveryOptions.options).option_id
                    }
                }
                //not loaded yet?
                if ($bixCart.cart.deliveryOptions.options === undefined) {
                    this.$bixCart.saveCart();
                }

            },
            getDaysFormat: function (days) {
                return this.$transChoice('{0} %count% days|{1} %count% day|]1,Inf[ %count% days',
                        days, {count: days}
                );
            }
        }
    };


</script>

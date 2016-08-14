<template>
    <div class="uk-form uk-form-stacked">

        <ul v-if="$bixCart.cart.payment_options.length" class="uk-list uk-list-line">
            <li v-for="payment_option in $bixCart.cart.payment_options">
                <label class="uk-flex uk-flex-middle uk-flex-space-between">
                    <div class="uk-margin-right">
                        <input type="radio" v-model="$bixCart.cart.payment_option_name" :value="payment_option.name"/>
                    </div>
                    <div v-if="payment_option.data.image.src" class="uk-margin-small-right uk-width-1-6">
                         <img :src="$url(payment_option.data.image.src)" :alt="payment_option.data.image.alt"/>
                    </div>
                    <div class="uk-flex-item-1">
                        <strong>{{ payment_option.title || payment_option.name }}</strong>
                        <span v-if="payment_option.price"><br>{{ payment_option.price | currency payment_option.currency }}</span>
                    </div>
                </label>
            </li>
        </ul>
        <p v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></p>

        <div v-if="show_card" class="uk-margin uk-form-horizontal">
            <div class="uk-form-row">
                <label class="uk-form-label">{{ 'Credit card number' | trans }}</label>
                <div class="uk-form-controls">
                    <div class="uk-form-icon uk-width-1-1">
                        <i class="uk-icon-credit-card"></i>
                        <input v-model="card.number" name="number" type="text"
                               class="uk-width-1-1">
                    </div>
                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.number">
                        {{ 'Please enter card number' | trans }}</p>
                </div>
            </div>
            <div class="uk-form-row">
                <div class="uk-form-controls uk-form-controls-text">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-1-3">
                            <select v-model="card.expiryMonth" name="expiryMonth" class="uk-width-1-1">
                                <option v-for="month in months" :value="month.value">{{ month.text }}</option>
                            </select>

                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.expiryMonth">
                                {{ 'Please enter expiry month' | trans }}</p>
                        </div>
                        <div class="uk-width-1-3">
                            <select v-model="card.expiryYear" name="expiryYear" class="uk-width-1-1">
                                <option v-for="year in years" :value="year.value">{{ year.text }}</option>
                            </select>

                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.expiryYear">
                                {{ 'Please enter expiry year' | trans }}</p>
                        </div>
                        <div class="uk-width-1-3">
                            <input v-model="card.cvv" name="vvc" type="text" class="uk-width-1-1"
                                   :placeholder="$trans('CVV')" maxlength="3">

                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.cvv">
                                {{ 'Please enter card number' | trans }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>


<script>

    module.exports = {

        props: ['card'],

        computed: {
            show_card: function () {
                var method = _.find($bixCart.cart.payment_options, 'name', $bixCart.cart.payment_option_name);
                return method && method.settings.card;
            },
            months: function () {
                var options = [{value: '', text: this.$trans('Month')}];
                for (var m = 1; m <= 12; m++) {
                    options.push({value: m.toString(), text: m.toString()});
                }
                return options;
            },
            years: function () {
                var nowYear = (new Date()).getFullYear(),
                        options = [{value: '', text: this.$trans('Year')}];
                for (var y = nowYear; y < (nowYear + 10); y++) {
                    options.push({value: y.toString(), text: y.toString()});
                }
                return options;
            }
        },

        created: function () {
            this.$bixCart = window.$bixCart;
            if ($bixCart.cart.payment_option_name === '' && $bixCart.cart.payment_options.length) {
                $bixCart.cart.payment_option_name = _.first($bixCart.cart.payment_options).name
            }
        }
    };


</script>

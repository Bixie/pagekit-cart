<template>

    <div class="uk-grid uk-grid-width-medium-1-2">
        <div>

            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'Invoice Address' | trans }}</h3>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-user"></i>
                            <input v-model="checkout.invoice_address.name" name="name" type="text"
                                   v-on="blur: validateField('invoice_address.name')"
                                   class="uk-width-1-1" placeholder="{{ 'Name' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.invoice_address.name">
                            {{ 'Please enter your name' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-building"></i>
                            <input v-model="checkout.invoice_address.address" name="address" type="text"
                                   v-on="blur: validateField('invoice_address.address')"
                                   class="uk-width-1-1" placeholder="{{ 'Address' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.invoice_address.address">
                            {{ 'Please enter an address' | trans }}</p>

                    </div>
                </div>

                <div v-show="show_address_2" class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-building"></i>
                            <input v-model="checkout.invoice_address.address_2" name="address_2" type="text"
                                   class="uk-width-1-1" placeholder="{{ 'Address line 2' | trans }}">
                        </div>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" value="hide-show_address_2"
                                      v-model="show_address_2"> {{ 'Show address line 2' | trans }}</label>
                    </div>
                </div>


                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-map-pin"></i>
                            <input v-model="checkout.invoice_address.zipcode" name="name" type="text"
                                   v-on="blur: validateField('invoice_address.zipcode')"
                                   class="uk-width-1-1" placeholder="{{ 'Zipcode' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.invoice_address.zipcode">
                            {{ 'Please enter a zipcode' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-map-o"></i>
                            <input v-model="checkout.invoice_address.city" name="name" type="text"
                                   v-on="blur: validateField('invoice_address.city')"
                                   class="uk-width-1-1" placeholder="{{ 'City' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.invoice_address.city">
                            {{ 'Please enter a city' | trans }}</p>
                    </div>
                </div>


                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <select v-model="checkout.invoice_address.country" name="country" options="countryList"
                                v-on="blur: validateField('invoice_address.country')"
                                class="uk-width-1-1"></select>
                    </div>
                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.invoice_address.country">
                        {{ 'Please select a country' | trans }}</p>
                </div>
            </div>


        </div>
        <div>
            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'Payment method' | trans }}</h3>

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="radio" value="PAYPAL"
                                      v-model="checkout.payment.method"> {{ 'Paypal' | trans }}</label>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="radio" value="STRIPE"
                                      v-model="checkout.payment.method"> {{ 'Stripe' | trans }}</label>
                    </div>
                </div>

            </div>
            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'To payment' | trans }}</h3>

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" name="agreed" value="agreed"
                                      v-model="checkout.agreed"> {{ 'I agree with the terms and conditions' | trans }}</label>
                    </div>
                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.agreed">
                        {{ 'Please agree with the terms and conditions' | trans }}</p>
                </div>

                <div class="uk-margin uk-text-right">
                    <button class="uk-button uk-button-large uk-button-success">
                        <i v-show="!spin" class="uk-icon-check uk-margin-small-right"></i>
                        <i v-show="spin" class="uk-icon-circle-o-notch uk-icon-spin uk-margin-small-right"></i>
                        {{ 'To payment' | trans }}
                    </button>
                </div>

              </div>
        </div>
    </div>
<pre>{{checkout|json}}</pre>
</template>

<script>

module.exports = {

    data: function () {
        return {
            spin: false,
            show_address_2: false,
            checkout: {
                agreed: false,
                invoice_address: {
                    name: 'name',
                    address: 'address',
                    address_2: '',
                    zipcode: '3456',
                    city: 'City',
                    country: 'NL'
                },
                payment: {
                    method: window.$cart.config.default_payment,
                    price: 0
                }
            },
            invalid: {}
        };
    },

    inherit: true,

    created: function () {
        this.config.required_checkout.forEach(function (name) {
            this.$set('invalid.' + name, false);
        }.bind(this));
    },

    methods: {
        doCheckout: function () {
            if (this.spin) {
                return;
            }
            console.log('checkout');
            var vm = this;
            if(this.validateCheckout()) {
                console.log(this.checkout.payment.method);
                this.spin = true;
                this.resource.save({id: 'checkout'}, { cartItems: this.cartItems, checkout: this.checkout }, function (data) {

                    console.log(data);
                    vm.$set('spin', false);

                });
            }
        },

        validateCheckout: function () {
            var invalid = false;
            this.config.required_checkout.forEach(function (name) {
                invalid = !this.validateField(name) || invalid;
            }.bind(this));

            return !invalid;
        },

        validateField: function (name) {
            var valid = !!this.$get('checkout.' + name);
            this.$set('invalid.' + name, !valid);
            return valid;
        }
    },

    computed: {
        countryList: function () {
            var options = [{value: '', text: this.$trans('Country')}];
            options.push({value: 'NL', text: this.$trans('Netherlands')});
            return options;
        }
    }

};

</script>
<template>

    <div v-if="cartItems.length" class="uk-grid uk-grid-width-medium-1-2">
        <div>

            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'Billing address' | trans }}</h3>

                <p v-if="!user.id" class="uk-text-small">{{ 'Do you have an account?' | trans }} <a
                        v-attr="href: login_url">{{ 'Please log in' | trans }}</a></p>


                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-user uk-text-primary"></i>
                            <input v-model="checkout.billing_address.firstName" name="firstName" type="text"
                                   v-on="blur: validateField('checkout.billing_address.firstName')"
                                   class="uk-width-1-1" placeholder="{{ 'First name' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger"
                           v-show="invalid.checkout.billing_address.firstName">
                            {{ 'Please enter your first name' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-user uk-text-primary"></i>
                            <input v-model="checkout.billing_address.lastName" name="lastName" type="text"
                                   v-on="blur: validateField('checkout.billing_address.lastName')"
                                   class="uk-width-1-1" placeholder="{{ 'Last name' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.lastName">
                            {{ 'Please enter your last name' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-envelope-o uk-text-primary"></i>
                            <input v-model="checkout.billing_address.email" name="email" type="email"
                                   v-on="blur: validateField('checkout.billing_address.email')"
                                   class="uk-width-1-1" placeholder="{{ 'Email address' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.email">
                            {{ 'Please enter your email address' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-phone"></i>
                            <input v-model="checkout.billing_address.phone" name="phone" type="text"
                                   class="uk-width-1-1" placeholder="{{ 'Phone number' | trans }}">
                        </div>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-building-o uk-text-primary"></i>
                            <input v-model="checkout.billing_address.address1" name="address1" type="text"
                                   v-on="blur: validateField('checkout.billing_address.address1')"
                                   class="uk-width-1-1" placeholder="{{ 'Address' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.address1">
                            {{ 'Please enter an address' | trans }}</p>

                    </div>
                </div>

                <div v-show="show_address2" class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-building-o"></i>
                            <input v-model="checkout.billing_address.address2" name="address2" type="text"
                                   class="uk-width-1-1" placeholder="{{ 'Address line 2' | trans }}">
                        </div>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="checkbox" value="hide-show_address2"
                                      v-model="show_address2"> {{ 'Show address line 2' | trans }}</label>
                    </div>
                </div>


                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-map-pin uk-text-primary"></i>
                            <input v-model="checkout.billing_address.postcode" name="postcode" type="text"
                                   v-on="blur: validateField('checkout.billing_address.postcode')"
                                   class="uk-width-1-1" placeholder="{{ 'Zipcode' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.postcode">
                            {{ 'Please enter a zipcode' | trans }}</p>

                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-map-o uk-text-primary"></i>
                            <input v-model="checkout.billing_address.city" name="city" type="text"
                                   v-on="blur: validateField('checkout.billing_address.city')"
                                   class="uk-width-1-1" placeholder="{{ 'City' | trans }}">
                        </div>
                        <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.city">
                            {{ 'Please enter a city' | trans }}</p>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <div class="uk-form-icon uk-width-1-1">
                            <i class="uk-icon-map-o"></i>
                            <input v-model="checkout.billing_address.state" name="state" type="text"
                                   v-on="blur: validateField('checkout.billing_address.state')"
                                   class="uk-width-1-1" placeholder="{{ 'State' | trans }}">
                        </div>
                    </div>
                </div>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <select v-model="checkout.billing_address.country" name="country" options="countryList"
                                v-on="change: validateField('checkout.billing_address.country')"
                                class="uk-width-1-1"></select>
                    </div>
                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.checkout.billing_address.country">
                        {{ 'Please select a country' | trans }}</p>
                </div>
            </div>


        </div>
        <div>
            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'Payment method' | trans }}</h3>

                <div v-repeat="gateway: gateways" class="uk-form-row">
                    <div class="uk-form-controls uk-form-controls-text">
                        <label><input type="radio" value="{{ gateway.shortName | trans }}"
                                      v-model="checkout.payment.method"> {{ gateway.name | trans }}</label>
                    </div>
                </div>

                <div v-show="show_card" class="uk-margin">
                    <div class="uk-form-row">
                        <div class="uk-form-controls">
                            <div class="uk-form-icon uk-width-1-1">
                                <i class="uk-icon-credit-card"></i>
                                <input v-model="card.number" name="number" type="text"
                                       v-on="blur: validateField('card.number')"
                                       class="uk-width-1-1" placeholder="{{ 'Credit card number' | trans }}">
                            </div>
                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.number">
                                {{ 'Please enter card number' | trans }}</p>
                        </div>
                    </div>
                    <div class="uk-form-row">
                        <div class="uk-form-controls uk-form-controls-text">
                            <div class="uk-grid uk-grid-small">
                                <div class="uk-width-1-3">
                                    <select v-model="card.expiryMonth" name="expiryMonth" options="months"
                                            v-on="blur: validateField('card.expiryMonth')"
                                            class="uk-width-1-1"></select>

                                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.expiryMonth">
                                        {{ 'Please enter expiry month' | trans }}</p>
                                </div>
                                <div class="uk-width-1-3">
                                    <select v-model="card.expiryYear" name="expiryYear" options="years"
                                            v-on="blur: validateField('card.expiryYear')"
                                            class="uk-width-1-1"></select>

                                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.expiryYear">
                                        {{ 'Please enter expiry year' | trans }}</p>
                                </div>
                                <div class="uk-width-1-3">
                                    <input v-model="card.cvv" name="vvc" type="text"
                                           v-on="blur: validateField('card.cvv')"
                                           class="uk-width-1-1" placeholder="{{ 'CVV' | trans }}">

                                    <p class="uk-form-help-block uk-text-danger" v-show="invalid.card.cvv">
                                        {{ 'Please enter card number' | trans }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="uk-text-danger" v-show="invalid.payment.method">
                    {{ 'Please select a payment method' | trans }}</p>

                <div v-if="checkouterror" class="uk-alert uk-alert-danger">{{ checkouterror | trans }}</div>

            </div>
            <div class="uk-panel uk-panel-box uk-form">

                <h3 class="uk-panel-title">{{ 'To payment' | trans }}</h3>

                <div class="uk-form-row">
                    <div class="uk-form-controls">
                        <textarea name="comment" id="form-comment" cols="30" rows="3" class="uk-width-1-1"
                                  v-model="checkout.comment" placeholder="{{ 'Add a comment' | trans }}"></textarea>
                    </div>
                </div>

                <div v-if="!user.id" class="uk-margin">
                    <div class="uk-form-row">
                        <div class="uk-form-controls uk-form-controls-text">
                            <label><input type="checkbox" value="hide-show_register"
                                          v-model="show_register"> {{ 'Create an account' | trans }}</label>
                        </div>
                    </div>

                    <div v-show="show_register" class="uk-form-row">
                        <div class="uk-form-controls">
                            <div class="uk-form-icon uk-width-1-1">
                                <i class="uk-icon-user uk-text-primary"></i>
                                <input v-model="user.username" name="username" type="text"
                                       v-on="blur: validateField('user.username')"
                                       class="uk-width-1-1" placeholder="{{ 'Username' | trans }}">
                            </div>
                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.user.username">
                                {{ 'Username cannot be empty' | trans }}</p>
                        </div>
                    </div>

                    <div v-show="show_register" class="uk-form-row">
                        <div class="uk-form-controls">
                            <div class="uk-form-icon uk-form-password">
                                <i class="uk-icon-key uk-text-primary"></i>
                                <input id="form-password" class="uk-form-large uk-form-width-large" type="password"
                                       placeholder="{{ 'Password' | trans}}"
                                       name="password" v-model="user.password">
                                <a href="" class="uk-form-password-toggle" tabindex="-1"
                                   data-uk-form-password="{ lblShow: $trans('Show'), lblHide: $trans('Hide') }">{{
                                    'Show' | trans }}</a>
                            </div>
                            <p class="uk-form-help-block uk-text-danger" v-show="invalid.user.password">
                                {{ 'Password cannot be blank.' | trans }}</p>
                        </div>
                    </div>

                    <div v-if="registererror" class="uk-alert uk-alert-warning">{{ registererror | trans }}</div>

                </div>

                <terms agreed="{{@ checkout.agreed }}" invalid="{{ invalid.checkout.agreed }}"></terms>

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

    <v-modal v-ref="redirectmodal" lightbox>
        <div class="uk-panel uk-panel-space uk-text-center">
            <h1 class="uk-heading-large"><i class="uk-icon-check uk-text-success uk-margin-small-right"></i>
                {{ 'Payment successful' | trans }}</h1>

            <p>{{ 'You are redirected...' | trans }}</p>

            <p><i class="uk-icon-refresh uk-icon-spin"></i></p>
        </div>

    </v-modal>

</template>

<script>

    module.exports = {

        data: function () {
            return _.merge({
                spin: false,
                order_id: 0,
                registererror: '',
                checkouterror: '',
                show_card: false,
                show_register: false,
                show_address2: false,
                checkout: {
                    agreed: false,
                    user_tz: '',
                    comment: '',
                    billing_address: {
                        firstName: '',
                        lastName: '',
                        email: '',
                        phone: '',
                        address1: '',
                        address2: '',
                        postcode: '',
                        city: '',
                        state: '',
                        country: ''
                    },
                    payment: {
                        method: '',
                        price: 0
                    }
                },
                card: {
                    number: '',
                    expiryMonth: '',
                    expiryYear: '',
                    cvv: ''
                },
                invalid: {}
            }, window.$checkout);
        },

        inherit: true,

        created: function () {
            this.config.required_checkout.forEach(function (name) {
                this.$set('invalid.' + name, false);
            }.bind(this));
            this.$set('invalid.payment.method', false);
            if (this.gateways.length === 1) {
                this.$set('checkout.payment.method', this.gateways[0].shortName);
            }
            this.checkout.user_tz = jstz.determine().name();
        },

        methods: {
            doCheckout: function () {
                if (this.spin) {
                    return;
                }
                var vm = this;

                if (this.validateCheckout()) {

                    this.spin = true;
                    this.$set('checkouterror', '');
                    this.$set('registererror', '');

                    this.resource.save({id: 'checkout'}, {
                        cartItems: this.cartItems,
                        cardData: this.card,
                        user: this.show_register ? this.user : [],
                        checkout: _.merge({currency: this.filters.currency, order_id: this.order_id}, this.checkout)
                    }, function (data) {

                        vm.$set('spin', false);
                        if (data.order.id) {
                            vm.$set('order_id', data.order.id);
                        }
                        if (data.error) {
                            vm.$set('checkouterror', data.checkouterror);
                            vm.$set('registererror', data.registererror);
                        } else {
                            console.log(data);
                            if (data.redirectUrl) {
                                if (data.order.status === 1) {
                                    //reset cart oon orig vm
                                    this.$set('cartItems', data.cartItems);
                                    vm.$.redirectmodal.open();
                                    setTimeout(function () {
                                        window.location.href = data.redirectUrl;
                                    }, 500)
                                } else {
                                    window.location.href = data.redirectUrl;
                                }
                            }

                        }

                    });
                }
            },

            validateCheckout: function () {
                var invalid = false;

                this.config.required_checkout.forEach(function (name) {
                    invalid = !this.validateField(name) || invalid;
                }.bind(this));

                if (this.show_card) {
                    ['card.number', 'card.expiryMonth', 'card.expiryYear', 'card.cvv'].forEach(function (name) {
                        invalid = !this.validateField(name) || invalid;
                    }.bind(this));
                }

                if (!this.checkout.payment.method) {
                    invalid = true;
                }
                this.$set('invalid.payment.method', !this.checkout.payment.method);

                return !invalid;
            },

            validateField: function (name) {
                var valid = !!this.$get(name);
                this.$set('invalid.' + name, !valid);
                return valid;
            }
        },

        computed: {
            show_card: function () {
                return ['Stripe'].indexOf(this.checkout.payment.method) > -1;
            },
            countryList: function () {
                var options = [{value: '', text: this.$trans('Country')}];
                _.forIn(this.countries, function (text, value) {
                    options.push({value: value, text: text});
                });
                return options;
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
        }

    };

</script>
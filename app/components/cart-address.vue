<template>
    <div class="uk-form uk-form-horizontal">

        <div class="uk-flex uk-flex-middle uk-margin">
            <h3 class="uk-margin-remove">{{ title }}</h3>
            <a v-show="edit" @click="save" class="uk-margin-small-left">{{ 'Save' | trans }}</a>
            <a v-else @click="edit = true" class="uk-margin-small-left">{{ 'Edit' | trans }}</a>
        </div>


        <div v-show="edit">
            <form class="uk-form" name="cartAddressForm" v-validator="cartAddressForm" @submit.prevent="save | valid">

                <div>
                    <label class="uk-form-label">{{ 'First Name' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.first_name" name="address_first_name" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Middle Name' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.middle_name" name="address_middle_name" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Last Name' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.last_name" name="address_last_name" debounce="500"
                               v-validate:required :class="{'uk-form-danger': fieldInvalid('address_last_name')}"/>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_last_name')">
                            {{ 'Please provide your last name.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Company Name' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large" v-model="address.company_name" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Email address' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.email" name="address_email" debounce="500" v-validate:email
                               v-validate:required :class="{'uk-form-danger': fieldInvalid('address_email')}"/>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_email')">
                            {{ 'Please provide your email address.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Address 1' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.address1" name="address_address_1" debounce="500"
                               v-validate:required :class="{'uk-form-danger': fieldInvalid('address_address_1')}"/>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_address_1')">
                            {{ 'Please provide your address.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Address 2' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.address2" name="address_address_2" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Postal Code' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.zipcode" name="address_zipcode" debounce="500"
                               v-validate:required :class="{'uk-form-danger': fieldInvalid('address_zipcode')}"/>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_zipcode')">
                            {{ 'Please provide your postal code.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'City' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.city" name="address_city" debounce="500"
                               v-validate:required :class="{'uk-form-danger': fieldInvalid('address_city')}"/>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_city')">
                            {{ 'Please provide your city.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'County' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.county" name="address_county" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'State' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.state" name="address_state" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Country' | trans }}</label>
                    <div class="uk-form-controls">
                        <select class="uk-form-width-large" v-model="address.country_code" name="address_country_code"
                                v-validate:required :class="{'uk-form-danger': fieldInvalid('address_country_code')}">
                            <option value="">{{ 'Please select' | trans }}</option>
                            <option v-for="country in orderedCountries" :value="country.code">{{ country.name }}
                            </option>
                        </select>
                        <p class="uk-form-help-block uk-text-danger" v-show="fieldInvalid('address_country_code')">
                            {{ 'Please provide your country.' | trans }}
                        </p>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Phone' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.phone" name="address_phone" debounce="500"/>
                    </div>
                </div>

                <div class="uk-margin-small-top">
                    <label class="uk-form-label">{{ 'Mobile' | trans }}</label>
                    <div class="uk-form-controls">
                        <input type="text" class="uk-form-width-large"
                               v-model="address.mobile" name="address_mobile" debounce="500"/>
                    </div>
                </div>

                <div class="uk-text-right uk-margin">
                    <button type="submit" class="uk-button uk-button-small"><i
                            class="uk-icon-check uk-text-success uk-icon-hover uk-margin-small-right"></i>
                        {{ 'Save' | trans }}
                    </button>
                </div>

            </form>
        </div>
        <div v-else>
            <div class="uk-grid uk-grid-width-medium-1-2" data-uk-grid-margin>
                <div>
                    <strong>{{ full_name }}</strong><br>
                    <span v-if="address.company_name">{{ address.company_name }}<br></span>
                    <span>{{ address.address1 }}<br></span>
                    <span v-if="address.address2">{{ address.address2 }}<br></span>
                    <span>{{ address.zipcode }} {{ address.city }}<br></span>
                    <span v-if="address.county || address.state">{{ address.county }} {{ address.state }}<br></span>
                    <span>{{ country_name }}<br></span>
                </div>
                <div>
                    <span>{{ address.email }}<br></span>
                    <span v-if="address.phone">{{ address.phone }}<br></span>
                    <span v-if="address.mobile">{{ address.mobile }}<br></span>
                </div>
            </div>
        </div>
    </div>

</template>


<script>

    module.exports = {

        props: {
            address: Object,
            countries: Object,
            title: String
        },

        data: function () {
            return {
                edit: true,
                cartAddressForm: false
            }
        },

        created: function () {
            if (!this.validate()) {
                this.edit = false;
            }
        },

        methods: {
            save: function () {
                this.$dispatch('address.saved');
                this.edit = false;
            },
            fieldInvalid: function (field_name) {
                if (this.cartAddressForm && this.cartAddressForm[field_name]) {
                    return this.cartAddressForm[field_name].invalid;
                }
                return false;
            },
            validate: function () {
                return this.cartAddressForm.valid;
            }
        },

        computed: {
            full_name: function () {
                return [this.address.first_name, this.address.middle_name, this.address.last_name].filter(function (name) {
                    return !!name;
                }).join(' ')
            },
            country_name: function () {
                return this.countries[this.address.country_code] || '';
            },
            orderedCountries: function () {
                var coll = [];
                _.forIn(this.countries, function (name, code) {
                    coll.push({code: code, name: name});
                });
                return _.sortBy(coll, 'name');
            }
        }

    };


</script>

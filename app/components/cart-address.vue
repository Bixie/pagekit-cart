<template>
    <div class="uk-form uk-form-horizontal">

        <div class="uk-flex uk-flex-middle uk-margin">
            <h3 class="uk-margin-remove">{{ title }}</h3>
            <a v-show="edit" @click="save" class="uk-margin-small-left">{{ 'Save' | trans }}</a>
            <a v-else @click="edit = true" class="uk-margin-small-left">{{ 'Edit' | trans }}</a>
        </div>


        <div v-show="edit">
            <div>
                <label class="uk-form-label">{{ 'First Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.first_name" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Middle Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.middle_name" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Last Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.last_name" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Company Name' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.company_name" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'email address' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.email" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Address 1' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.address1" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Address 2' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.address2" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Postal Code' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.zipcode" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'City' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.city" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'County' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.county" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'State' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.state" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Country' | trans }}</label>
                <div class="uk-form-controls">
                    <select class="uk-form-width-large" v-model="address.country_code">
                        <option value="">{{ 'Please select' | trans }}</option>
                        <option v-for="country in orderedCountries" :value="country.code">{{ country.name }}</option>
                    </select>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Phone' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.phone" debounce="500"/>
                </div>
            </div>
            <div class="uk-margin-small-top">
                <label class="uk-form-label">{{ 'Mobile' | trans }}</label>
                <div class="uk-form-controls">
                    <input type="text" class="uk-form-width-large" v-model="address.mobile" debounce="500"/>
                </div>
            </div>

            <div class="uk-text-right uk-margin">
                <button type="button" @click="save" class="uk-button uk-button-small"><i
                   class="uk-icon-check uk-text-success uk-icon-hover uk-margin-small-right"></i>{{ 'Save' | trans }}</button>
            </div>
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
                edit: true
            }
        },

        created: function () {
            if (this.isValid) {
                this.edit = false;
            }
        },

        methods: {
            save: function () {
                if (this.isValid) {
                    this.$dispatch('address.saved');
                    this.edit = false;
                }
            }
        },

        computed: {
            isValid: function () {
                return !!(this.full_name && this.address.email && this.address.address1
                            && this.address.zipcode && this.address.city && this.address.country_code);
            },
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

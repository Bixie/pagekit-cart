<template>
    <div class="uk-form">

        <div class="uk-flex">
            <div><input id="cart_terms_condicitons" type="checkbox" v-model="confirmed"/></div>
            <div class="uk-margin-left">
                <label for="cart_terms_condicitons"><strong>{{ 'I agree with the terms and conditions' | trans }}</strong></label><br/>
                <a v-if="!show" class="uk-text-small" @click="showTerms">{{ 'Show terms and conditions' | trans }}</a>
                <a v-else class="uk-text-small" @click="show = false">{{ 'Hide terms and conditions' | trans }}</a>
            </div>
        </div>

        <div v-if="show" class="uk-scrollable-box">
            <div v-if="content">
                {{{ content }}}
            </div>
            <div v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></div>
        </div>

    </div>

</template>


<script>

    module.exports = {

        props: {
            'confirmed': Boolean
        },

        data: function () {
            return {
                show: false,
                content: ''
            };
        },

        created: function () {
            this.$bixCart = window.$bixCart;
        },

        methods: {
            showTerms: function () {
                this.show = true;
                if (!this.content) {
                    this.$bixCart.getTerms().then(function (res) {
                        this.content = res.data.html;
                    }.bind(this));
                }
            }
        }
    };


</script>

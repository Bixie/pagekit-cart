<template>
    <div class="uk-form">

        <div v-el:ui class="uk-flex">
            <div><input id="cart_terms_condicitons" type="checkbox" v-model="confirmed"/></div>
            <div class="uk-margin-left">
                <label for="cart_terms_condicitons"><strong>{{ 'I agree with the terms and conditions' | trans }}</strong></label><br/>
                <a v-if="!show || modal" class="uk-text-small" @click="showTerms">{{ 'Show terms and conditions' | trans }}</a>
                <a v-else class="uk-text-small" @click="show = false">{{ 'Hide terms and conditions' | trans }}</a>
            </div>
        </div>

        <div v-if="show && !modal" class="uk-scrollable-box">
            <div v-if="content">
                {{{ content }}}
            </div>
            <div v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></div>
        </div>

        <div v-if="modal" v-el:termsmodal class="uk-modal">
            <div class="uk-modal-dialog">
                <a class="uk-close uk-modal-close"></a>

                <div v-if="content" class="uk-overflow-container">
                    {{{ content }}}
                </div>
                <div v-else class="uk-text-center"><i class="uk-icon-spinner uk-icon-spin"></i></div>

                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="uk-button uk-modal-close">{{ 'Close' | trans }}</button>
                </div>
            </div>
        </div>
    </div>

</template>


<script>

    module.exports = {

        props: {
            'confirmed': Boolean,
            'modal': {type: Boolean, default: true}
        },

        data() {
            return {
                show: false,
                content: ''
            };
        },

        created() {
            this.$bixCart = this.$root;
        },

        methods: {
            reset: function () {
                UIkit.$(this.$els.ui).removeClass('uk-animation-shake uk-form-danger');
            },
            invalid: function () {
                this.$nextTick(() => UIkit.$(this.$els.ui).addClass('uk-animation-shake uk-form-danger'));
            },
            showTerms() {
                this.show = true;
                if (!this.content) {
                    this.$bixCart.getTerms().then(res => this.content = res.data.html);
                }
                if (this.modal) {
                    var modal = UIkit.modal(this.$els.termsmodal, {
                        modal: false
                    });
                    modal.on('show.uk.modal', () => UIkit.$(this.$els.termsmodal).appendTo(UIkit.$body));
                    modal.on('hide.uk.modal', e => e.stopPropagation()); //prevent closing main modal
                    modal.show();

                }
            }
        }
    };


</script>

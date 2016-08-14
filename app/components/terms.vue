<template>


    <div class="uk-form-row">
        <div class="uk-form-controls uk-form-controls-text">
            <label><input type="checkbox" name="agreed" value="agreed"
                          v-model="agreed"> {{ termsText1 }}<a @click="showTerms">{{ linkText }}</a>{{ termsText2 }}</label>
        </div>
        <p class="uk-form-help-block uk-text-danger" v-show="invalid">
            {{ 'Please agree with the terms and conditions' | trans }}</p>
    </div>

    <v-modal v-ref="termsmodal" large>{{{ content }}}</v-modal>

</template>

<script>

module.exports = {

    data: function () {
        return {
            textParts: [],
            content: ''
        }
    },

    props: ['agreed', 'invalid'],

    created: function () {
        //this is silly
        this.textParts = this.$trans('I agree with the %link%terms and conditions%/link%.', {'link': '||', '/link': '||'}).split('||');
    },

    methods: {
        showTerms: _.debounce(function (e) {
            e.preventDefault();
            this.$http.get('api/cart/cart/terms', {}, function (data) {
                this.content = data.html;
                this.$.termsmodal.open()
            }).error(function (data) {
                this.$notify(data, 'danger');
            });

        }, 1000, {leading: true, trailing: false})
    },

    computed: {
        termsText1: function () {
            return this.textParts[0];
        },
        termsText2: function () {
            return this.textParts[2];
        },
        linkText: function () {
            return this.textParts[1];
        }
    }

};

</script>
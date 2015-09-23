<template>
    <div class="uk-grid uk-grid-small uk-flex-middle uk-margin" data-uk-margin="">
        <div class="uk-width-medium-1-3 uk-text-right">{{{ product.price | currency product}}}</div>
        <div class="uk-width-medium-2-3">
            <button class="uk-button uk-button-success" v-on="click: addToCart(product)">
                <i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'Add to cart' | trans }}
            </button>
        </div>


    </div>

</template>

<script>

    module.exports = {

        props: ['product', 'item_id'],

        data: function () {
            return {
                config: window.$cart.config
            }
        },

        created: function () {
        },

        computed: {
            fileName: function () {
                return this.file.split('/').pop();
            }
        },

        methods: {
            addToCart: function (product) {
                window.$bixieCart.addToCart(product);
            }
        }

    };

    Vue.component('addtocart', function (resolve, reject) {
        Vue.asset({
            js: [
//                'app/assets/uikit/js/components/upload.min.js',
//                'app/system/modules/finder/app/bundle/panel-finder.js'
            ]
        }, function () {
            resolve(module.exports);
        })
    });

</script>

<template>

    <div :id="id" class="uk-flex">
        <div class="uk-flex-item-1">
            <template v-if="quantity_data.quantities.length > 1">

                <template v-if="quantity_data.type === 'piece'">
                    <template v-if="showOptions">
                        <div class="uk-grid uk-grid-small uk-grid-divider uk-margin-small-bottom" :class="'uk-grid-width-1-' + rows">
                            <div v-for="qty in quantity_rows">
                                <div class="uk-text-center">
                                    <div>
                                <span class="uk-text-small uk-text-nowrap">
                                    {{ quantityHeader(qty, $index) }}
                                </span>
                                    </div>
                                    <div>
                                        <strong class="uk-text-nowrap">{{ qty | productprice }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-if="quantity_data.quantities.length > rows" class="uk-margin-small-bottom">
                            <div class="uk-button-dropdown" :data-uk-dropdown="`{justify:'#${id}'}`">

                                <!-- This is the element toggling the dropdown -->
                                <a class="">{{ 'More quantities' | trans }}</a>

                                <!-- This is the dropdown -->
                                <div class="uk-dropdown">
                                    mioe
                                </div>
                            </div>
                        </div>
                    </template>

                    <div class="uk-grid uk-grid-small uk-form">
                        <div class="uk-width-2-3 uk-flex uk-flex-middle">
                            <label class="uk-form-label">{{ 'Quantity' | trans }}</label>
                            <input type="number" class="uk-flex-item-1 uk-width-1-1 uk-form-large uk-margin-small-left uk-text-center"
                                   v-model="item.quantity" min="1" number/>
                        </div>
                        <div class="uk-width-1-3 uk-flex uk-flex-middle uk-flex-right">
                            <strong class="uk-margin-right uk-text-nowrap">
                                {{{ formatted_price }}}
                            </strong>
                        </div>
                    </div>
                </template>
                <template v-if="quantity_data.type === 'total'">
                    <div class="uk-flex uk-flex-middle uk-form">
                        <label class="uk-form-label">{{ 'Quantity' | trans }}</label>
                        <select class="uk-flex-item-1 uk-margin-small-left"
                                v-model="item.quantity" number>
                            <option v-for="qty in quantity_data.quantities" :value="qty.min_quantity">
                                {{ $trans('Qty. %min_quantity% : ', {
                                'min_quantity': qty.min_quantity
                                }) }}{{ qty | productprice }}
                            </option>
                        </select>
                    </div>
                </template>

            </template>
            <template v-else>

                <div class="uk-grid uk-grid-small">
                    <div class="uk-width-2-3 uk-text-right">
                        {{ $trans('Qty. %quantity%', {'quantity': item.quantity}) }}
                    </div>
                    <div class="uk-width-1-3 uk-flex uk-flex-middle uk-flex-right">
                        <strong class="uk-margin-right uk-text-nowrap">{{{ formatted_price }}}</strong>
                    </div>
                </div>

            </template>
        </div>
        <slot></slot>
    </div>

</template>


<script>

    module.exports = {

        props: {
            item: Object,
            hash: String,
            rows: {type: Number,  default: 5},
            showOptions: {type: Boolean,  default: false}
        },

        data() {
            return {
                id: _.uniqueId('atc'),
            };
        },

        created() {
        },

        watch: {
            'item.quantity + hash': function () {
                var quantity = _.find(this.quantity_data.quantities, qanty => (qanty.min_quantity <= this.item.quantity && qanty.max_quantity >= this.item.quantity));
                this.item.price = quantity.price * (this.quantity_data.type === 'piece' ? this.item.quantity : 1);
                this.item.currency = quantity.currency;
            }
        },

        computed: {
            quantity_data() {
                return this.item.quantity_data[this.hash] || {quantities: [], type: ''};
            },
            quantity_rows() {
                return this.quantity_data.quantities.slice(0, this.rows);
            },
            formatted_price() {
                return this.$cartCurrency.productPrice({
                    price: this.item.price,
                    currency: this.item.currency,
                    vat: this.item.data.vat
                });
            }
        },
        methods: {
            quantityHeader(qty, idx) {
                return (idx === (this.quantity_data.quantities.length - 1) && (qty.max_quantity === 0 ||  qty.max_quantity > 100000))
                        ? this.$trans('Qty. %min_quantity% and up', {
                    'min_quantity': qty.min_quantity,
                    'max_quantity': qty.max_quantity
                }) : this.$trans('Qty. %min_quantity% - %max_quantity%', {
                    'min_quantity': qty.min_quantity,
                    'max_quantity': qty.max_quantity
                });
            }
        }

    };


</script>

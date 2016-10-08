<template>

    <div id="bix-cart">
        <v-modal v-ref:modal modifier="uk-modal-dialog-blank" :options="{bgclose: false}" :closed="checkoutModalDestroy">
            <partial :name="modal_template"></partial>
        </v-modal>
    </div>

</template>

<script>
    var md5 = require('md5');
    var defaultAddress = {
        first_name: '',
        middle_name: '',
        last_name: '',
        company_name: '',
        email: '',
        address1: '',
        address2: '',
        zipcode: '',
        city: '',
        county: '',
        state: '',
        country_code: '',
        phone: '',
        mobile: ''
    };
    var defaultItem = {
        id: '',
        sku: '',
        title: '',
        quantity: 1,
        price: 0,
        currency: 'EUR',
        vat: 'high',
        quantity_options: [],
        data: {},
        template: 'default-product'
    };

    var defaultCart = {
        delivery_address: _.assign({}, defaultAddress),
        items: [],
        delivery_options: [],
        payment_options: [],
        delivery_option_id: '',
        payment_option_name: '',
        checkout: {
            error: ''
        },
        order: {
            reference: '',
            comment: '',
        },
        card: {
            number: '',
            expiryMonth: '',
            expiryYear: '',
            cvv: ''
        },
        confirmed: false
    };

    module.exports = {

        name: 'bix-cart',

        data: function () {
            return _.merge({
                modal_template: 'default-cart-modal',
                error: '',
                checkouterror: '',
                filter: {
                    currency: 'EUR'
                },
                reference_show: false,
                comment_show: false,
                checkoutmodal: false,
                checkingout: false,
                checkout: {},
                order: {
                    id: 0,
                    status: 0,
                    ext_key: 'game2art.order.new',
                    reference: '',
                    data: {comment: ''}
                },
                checkout_url: '',
                login_url: '',
                user: {},
                config: {},
                cart: {}
            }, window.$bix_cart);
        },

        created: function () {
            var storedCart = localStorage.getItem('bixcart.cart');
            this.Cart = this.$resource('api/cart/cart', {}, {
                terms: {method: 'GET', url: 'api/cart/cart/terms'},
                checkout: {method: 'POST', url: 'api/cart/cart/checkout'}
            });
            //localstorage sync
            this.cart = storedCart ? JSON.parse(storedCart) : _.assign({}, defaultCart);
            this.$watch('cart', function (cart) {
                //fill order info
                this.order.reference = cart.order.reference;
                this.order.data.comment = cart.order.comment;
                //do not store cc info
                localStorage.setItem('bixcart.cart', JSON.stringify(_.assign({}, cart, {card: defaultCart.card})));
            }, {deep: true});
            this.$watch('total_price', this.saveCart);
        },

        events: {
            'address.saved': 'saveCart'
        },

        watch: {
            'reference_show': function (value) {
                if (value) {
                    this.$els.reference.focus();
                }
            },
            'comment_show': function (value) {
                if (value) {
                    this.$els.comment.focus();
                }
            }
        },

        computed: {
            currency: function () {
                return this.filter.currency;
            },
            nr_items_format: function () {
                return this.$transChoice('{0} %count% items|{1} %count% item|]1,Inf[ %count% items',
                        this.nr_items, {count: this.nr_items}
                );
            },
            nr_items: function () {
                return this.cart.items.length || 0;
            },
            total_items: function () {
                return _.reduce(this.cart.items, function (sum, item) {
                    return sum + item.price;
                }, 0);
            },
            delivery_price: function () {
                var delivery_option = _.find(this.cart.delivery_options, 'id', this.cart.delivery_option_id);
                if (delivery_option) {
                    return delivery_option.price;
                }
                return 0;
            },
            payment_price: function () {
                var payment_option = _.find(this.cart.payment_options, 'name', this.cart.payment_option_name);
                if (payment_option) {
                    return payment_option.price;
                }
                return 0;
            },
            total_price: function () {
                return this.total_items + this.delivery_price + this.payment_price;
            },
            vat_calc: function () {
                var vat_calc = {none: 0, low: 0, high: 0};
                _.forEach(this.cart.items, function (cartItem) {
                    vat_calc[cartItem.vat] += cartItem.price * 100;
                }, this);
                if (this.delivery_price) {
                    vat_calc['high'] += this.delivery_price * 100;
                }
                if (this.payment_price) {
                    vat_calc['high'] += this.payment_price * 100;
                }
                return {
                    total: Math.round(this.$cartCurrency.getVat({price:  vat_calc['high'], vat: 'high'})
                         + this.$cartCurrency.getVat({price:  vat_calc['low'], vat: 'low'})) / 100,
                    low:  {
                        netto: vat_calc['low'] / 100,
                        vat: Math.round(this.$cartCurrency.getVat({price:  vat_calc['low'], vat: 'low'})) / 100
                    },
                    high: {
                        netto: vat_calc['high'] / 100,
                        vat: Math.round(this.$cartCurrency.getVat({price:  vat_calc['high'], vat: 'high'})) / 100
                    }
                };
            },
            total_bruto: function () {
                return this.total_price + this.vat_calc.total;
            },
            delivery_valid: function () {
                return this.$refs.delivery_address && this.$refs.delivery_address.validate();
            },
            cart_valid: function () {
                return !!(this.delivery_valid && this.cart.delivery_option_id && this.cart.payment_option_name && this.cart.confirmed)
            }
        },

        methods: {
            openCart: function () {
                this.$refs.modal.open();
            },
            closeCart: function () {
                this.$refs.modal.close();
            },
            addItem: function (item) {
                var existing = _.find(this.cart.items, 'sku', item.sku);
                if (existing) {
                    this.addQuantity(existing, item.quantity);
                } else {
                    this.cart.items.push(_.merge({}, defaultItem, item, {id: md5(item.sku  + item.title)}));
                }
            },
            addQuantity: function (item, quantity) {
                var des_option = _.find(item.quantity_options, 'quantity', (item.quantity + quantity));
                if (des_option) {
                    item.quantity = des_option.quantity;
                    item.price = des_option.price * item.quantity;
                    item.currency = des_option.currency;
                } else {
                    //todo refine this. now goto next option
                    var idx = _.findIndex(item.quantity_options, 'quantity', quantity);
                    if (idx < item.quantity_options.length) {
                        item.quantity = item.quantity_options[(idx + 1)].quantity;
                        item.price = item.quantity_options[(idx + 1)].price * item.quantity;
                        item.currency = item.quantity_options[(idx + 1)].currency;
                    }
                }
            },
            removeItem: function (item) {
                this.cart.items.$remove(item);
            },
            emptyCart: function () {
                this.cart =  _.assign({}, defaultCart)
            },
            saveCart: function () {
                this.resetErrors();
                console.log('save ' + this.cart.items.length);
                this.Cart.save({}, {cart: this.cart}).then(function (res) {
                    console.log('saved ' + res.data.items.length);
                    if (res.data.items) { //valid result?
                        this.cart.items = res.data.items;
                        this.cart.delivery_options = res.data.delivery_options;
                        this.cart.payment_options = res.data.payment_options;
                    }
                }, function (res) {
                    this.setError(res.data.message || res.data);
                });
            },
            getTerms: function () {
                return this.Cart.terms().then(_.noop(), function (res) {
                    this.setError(res.data.message || res.data);
                });
            },
            validate: function () {
                var valid = true;
                ['delivery', 'payment'].forEach(function (ref) {
                    if (this.$refs[ref]) {
                        var res = this.$refs[ref].validate();
                        valid = valid ? res : false;
                    }
                }.bind(this));
                return valid;
            },
            doCheckout: function () {
                if (!this.validate()) {
                    return;
                }
                this.resetErrors();
                this.checkingout = true;
                console.log('checkout ' + this.cart.items.length);
                this.Cart.checkout({}, {cart: this.cart, order_data: this.order, user_data: {}}).then(function (res) {
                    this.checkout = res.data.checkout;
                    this.order = res.data.order;
                    if (this.checkout.redirect_url) {
                        setTimeout(function () {
                            window.location.href = this.checkout.redirect_url;
                        }.bind(this));
                    }
                    if (this.order.status === 1) {
                        this.emptyCart();
                    }
                    this.checkingout = false;
                    this.checkoutModalOpen();
                }, function (res) {
                    this.checkingout = false;
                    this.setError(res.data.message || res.data);
                });

            },
            checkoutModal: function () {
                if (this.checkoutmodal) {
                    return this.checkoutmodal;
                }
                this.checkoutmodal = UIkit.modal(this.$els.checkoutmodal, {
                    bgclose: false,
                    center: true,
                    modal: false
                });
                this.checkoutmodal.on('hide.uk.modal', function (e) {
                    e.stopPropagation(); //prevent closing main modal
                });
                return this.checkoutmodal;
            },
            checkoutModalOpen: function () {
                this.checkoutModal().show();
            },
            checkoutModalClose: function () {
                this.checkoutModal().hide();
            },
            checkoutModalDestroy: function () {
                this.checkoutmodal = false;
            },
            vatLabel: function (vat_type) {
                return this.$trans('%perc%\% of %amount%', {
                    'perc': this.config.vatclasses[vat_type].rate,
                    'amount': this.$cartCurrency.formatNumber(this.vat_calc[vat_type].netto)
                });
            },
            resetErrors: function () {
                this.checkouterror = '';
                this.error = '';
            },
            setError: function (error) {
                this.error = error;
                if (UIkit.notify) UIkit.notify(error, 'danger');
            }
        },

        partials: {
            'default-product': require('./templates/product/default-product.html'),
            'default-cart-modal': require('./templates/cart/modal.html'),
        },

        components: {
            'cart-item': require('./components/cart-item.vue'),
            'cart-address': require('./components/cart-address.vue'),
            'cart-delivery': require('./components/cart-delivery.vue'),
            'cart-payment': require('./components/cart-payment.vue'),
            'cart-terms': require('./components/cart-terms.vue')
        }

    };

</script>

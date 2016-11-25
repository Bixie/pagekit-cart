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
        quantity_data: {},
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

        data() {
            return _.merge({
                modal_template: 'default-cart-modal',
                checkout_template: 'default-cart-checkout',
                error: '',
                delivery_errors: [],
                checkouterror: '',
                saving: false,
                validating: false,
                filter: this.$session.get('bixie.cart.filter', {
                    currency:  '',
                    vat_view: '',
                    show_specs: false
                }),
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

        created() {
            this.$cartCurrency.setCartObject(this);
            this.Cart = this.$resource('api/cart/cart', {}, {
                terms: {method: 'GET', url: 'api/cart/cart/terms'},
                checkout: {method: 'POST', url: 'api/cart/cart/checkout'}
            });
            //localstorage sync
            var storedCart = localStorage.getItem('bixcart.cart');
            this.cart = storedCart ? JSON.parse(storedCart) : _.assign({}, defaultCart);
            this.$watch('cart', cart => {
                //fill order info
                this.order.reference = cart.order.reference;
                this.order.data.comment = cart.order.comment;
                //do not store cc info
                localStorage.setItem('bixcart.cart', JSON.stringify(_.assign({}, cart, {card: defaultCart.card})));
            }, {deep: true});
            this.$watch('filter', filter => {
                this.$session.set('bixie.cart.filter', filter);
            }, {deep: true});
//            this.$watch('total_price', this.saveCart);
        },

        events: {
//            'address.saved': 'saveCart'
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
            currency() {
                return this.filter.currency;
            },
            nr_items_format() {
                return this.$transChoice('{0} %count% items|{1} %count% item|]1,Inf[ %count% items',
                        this.nr_items, {count: this.nr_items}
                );
            },
            nr_items() {
                return this.cart.items.length || 0;
            },
            total_items() {
                return _.reduce(this.cart.items, (sum, item) => {
                    return sum + item.price;
                }, 0);
            },
            delivery_price() {
                var delivery_option = _.find(this.cart.delivery_options, 'id', this.cart.delivery_option_id);
                if (delivery_option && this.total_items) {
                    return delivery_option.price;
                }
                return 0;
            },
            payment_price() {
                var payment_option = _.find(this.cart.payment_options, 'name', this.cart.payment_option_name);
                if (payment_option && this.total_items) {
                    return payment_option.price;
                }
                return 0;
            },
            total_price() {
                return this.total_items + this.delivery_price + this.payment_price;
            },
            vat_calc() {
                var vat_calc = {none: 0, low: 0, high: 0};
                _.forEach(this.cart.items, cartItem => {
                    vat_calc[cartItem.vat] += cartItem.price * 100;
                });
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
            total_bruto() {
                return this.total_price + this.vat_calc.total;
            },
            total_formatted() {
                if ((this.filter.vat_view || this.config.vat_view) === 'incl') {
                    return this.$cartCurrency.formatPrice(this.total_bruto);
                }
                return this.$cartCurrency.formatPrice(this.total_price);
            },
            delivery_valid() {
                return this.$refs.delivery_address && this.$refs.delivery_address.validate();
            }
        },

        methods: {
            openCart() {
                if (this.config.checkout_type == 'modal') {
                    this.$refs.modal.open();
                } else {
                    window.location.href = this.checkout_url;
                }
            },
            closeCart() {
                this.$refs.modal.close();
            },
            addItem(item) {
                var existing = false;//_.find(this.cart.items, 'sku', item.sku); //todo match specs/props
                if (existing) {
                    this.addQuantity(existing, item.quantity);
                } else {
                    this.cart.items.push(_.merge({}, defaultItem, item, {id: md5(item.sku  + item.title + JSON.stringify(item.data))}));
                }
                return this.saveCart();
            },
            addQuantity(item, qty) {
                var quantities = item.quantity_data.quantities;
                var des_option = _.find(quantities, qanty => (qanty.min_quantity <= qty && qanty.max_quantity >= qty));
                if (des_option) {
                    item.quantity = des_option.quantity;
                    item.price = des_option.price * item.quantity;
                    item.currency = des_option.currency;
                } else {
                    //todo refine this. now goto next option
                    var idx = _.findIndex(quantities, 'quantity', qty);
                    if (idx < item.quantity_data.length) {
                        item.quantity = quantities[(idx + 1)].quantity;
                        item.price = quantities[(idx + 1)].price * item.quantity;
                        item.currency = quantities[(idx + 1)].currency;
                    }
                }
            },
            removeItem(item) {
                this.cart.items.$remove(item);
                return this.saveCart();
            },
            emptyCart() {
                this.cart =  _.assign({}, defaultCart, {delivery_address: this.cart.delivery_address});
                return this.saveCart();
            },
            saveCart() {
                this.saving = true;
                this.resetErrors();
                console.log('save ' + this.cart.items.length);
                return this.Cart.save({}, {cart: this.cart}).then(res => {
                    console.log('saved ' + res.data.items.length);
                    if (res.data.items) { //valid result?
                        this.cart.items = res.data.items;
                        this.cart.delivery_options = res.data.delivery_options;
                        this.cart.payment_options = res.data.payment_options;
                    }
                    this.saving = false;
                }, res => {
                    this.setError(res.data.message || res.data);
                    this.saving = false;
                });
            },
            getTerms() {
                return this.Cart.terms().then(_.noop(), res => {
                    this.setError(res.data.message || res.data);
                });
            },
            validate() {
                var valid = true;
                ['delivery', 'payment'].forEach(ref => {
                    if (this.$refs[ref]) {
                        var res = this.$refs[ref].valid;
                        valid = valid ? res : false;
                    }
                });
                if (this.$refs.terms && !this.$refs.terms.confirmed) {
                    setTimeout(() => this.$refs.terms.invalid(), 50);
                    return false;
                }
                return valid;
            },
            doCheckout() {
                this.validating  = true;
                this.resetErrors();
                if (!this.validate()) {
                    return;
                }
                this.validating  = false;
                this.checkingout = true;
                console.log('checkout ' + this.cart.items.length);
                this.Cart.checkout({}, {cart: this.cart, order_data: this.order, user_data: {}}).then(res => {
                    this.checkout = res.data.checkout;
                    this.order = res.data.order;
                    if (this.order.status === 1) {
                        this.emptyCart();
                    }
                    this.$nextTick(() => {
                        if (this.checkout.redirect_url) {
                            setTimeout(() => {
                                window.location.href = this.checkout.redirect_url;
                            }, 300);
                        }
                        this.checkingout = false;
                        this.checkoutModalOpen();
                    });
                }, res => {
                    this.checkingout = false;
                    this.setError(res.data.message || res.data);
                });

            },
            checkoutModal() {
                if (this.checkoutmodal) {
                    return this.checkoutmodal;
                }
                this.checkoutmodal = UIkit.modal(this.$els.checkoutmodal, {
                    bgclose: false,
                    center: true,
                    modal: false
                });
                this.checkoutmodal.on('show.uk.modal', () => UIkit.$(this.$els.checkoutmodal).appendTo(UIkit.$body));
                this.checkoutmodal.on('hide.uk.modal', e => e.stopPropagation()); //prevent closing main modal
                return this.checkoutmodal;
            },
            checkoutModalOpen() {
                this.checkoutModal().show();
            },
            checkoutModalClose() {
                this.checkoutModal().hide();
            },
            checkoutModalDestroy() {
                this.checkoutmodal = false;
            },
            vatLabel(vat_type) {
                return this.$trans('%perc%\% of %amount%', {
                    'perc': this.config.vatclasses[vat_type].rate,
                    'amount': this.$cartCurrency.formatNumber(this.vat_calc[vat_type].netto)
                });
            },
            resetErrors() {
                if (this.$refs.terms) {
                    this.$refs.terms.reset();
                }
                this.checkouterror = '';
                this.error = '';
            },
            setError(error) {
                this.error = error;
                if (UIkit.notify) UIkit.notify(error, 'danger');
            }
        },

        partials: {
            'default-product': require('./templates/product/default-product.html'),
            'default-cart-modal': require('./templates/cart/modal.html'),
            'default-cart-checkout': require('./templates/cart/checkout.html')
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

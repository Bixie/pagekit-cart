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
        currency: 0,
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
        },

        events: {
            'address.saved': 'saveCart'
        },

        watch: {
            'total_price': 'saveCart'
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
                var item_total = _.reduce(this.cart.items, function (sum, item) {
                    return sum + item.price;
                }, 0);

                console.log('total_priceCalx', item_total);
                return item_total + this.delivery_price + this.payment_price;
            },
            delivery_valid: function () {
                return this.$refs.delivery && this.$refs.delivery.isValid;
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
            doCheckout: function () {
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

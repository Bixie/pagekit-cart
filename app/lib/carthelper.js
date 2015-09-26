
module.exports = {

    computed: {
        totalNetto: function () {
            var total = 0;
            this.cartItems.forEach(function (cartItem) {
                total += this.convertPrice(cartItem.price, cartItem);
            }.bind(this));
            return total;
        },
        totalBruto: function () {
            var total = 0;
            this.cartItems.forEach(function (cartItem) {
                var vat = this.calcVat(cartItem);
                total += vat.bruto;
            }.bind(this));
            return total;
        },
        totalTaxes: function () {
            return this.totalBruto - this.totalNetto;
        }
    }

};
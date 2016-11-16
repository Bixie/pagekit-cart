var assets = __dirname + "/../../../app/assets";

module.exports = [

    {
        entry: {
            "widget-cart": "./app/components/widget-cart.vue",
            "bixie-cart": "./app/cart.js",
            "bixie-cart-widget": "./app/views/cart-widget.js",
            /*views*/
            "orders": "./app/views/front/orders.js",
            "findorder": "./app/views/front/findorder.js",
            "paymentreturn": "./app/views/front/paymentreturn.js",
            "cart-settings": "./app/views/admin/settings.js",
            "admin-order": "./app/views/admin/order.js",
            "admin-orders": "./app/views/admin/orders.js"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        resolve: {
            alias: {
                "md5$": assets + "/js-md5/js/md5.js"
            }
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                {test: /\.vue$/, loader: "vue"},
                {test: /\.html/, loader: "vue-html"},
                {test: /\.js/, loader: 'babel', query: {presets: ['es2015']}}
            ]
        }
    }

];

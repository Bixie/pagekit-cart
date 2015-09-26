module.exports = [

    {
        entry: {
            "download-section-cart": "./app/components/download-section-cart.vue",

            "widget-cart": "./app/components/widget-cart.vue",
            "bixie-cart": "./app/cart.js",
            /*frontpage views*/
    /*        "checkout": "./app/views/checkout.js",*/
            /*admin views*/
            "cart-settings": "./app/views/admin/settings.js",
            "admin-order": "./app/views/admin/order.js",
            "admin-orders": "./app/views/admin/orders.js"
        },
        output: {
            filename: "./app/bundle/[name].js"
        },
        externals: {
            "lodash": "_",
            "jquery": "jQuery",
            "uikit": "UIkit",
            "vue": "Vue"
        },
        module: {
            loaders: [
                {test: /\.vue$/, loader: "vue"}
            ]
        }
    }

];

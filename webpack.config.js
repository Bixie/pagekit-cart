module.exports = [

    {
        entry: {
            "widget-cart": "./app/components/widget-cart.vue",
            "bixie-cart": "./app/cart.js",
            /*views*/
            "orders": "./app/views/front/orders.js",
            "findorder": "./app/views/front/findorder.js",
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
                {test: /\.vue$/, loader: "vue"},
                {test: /\.html/, loader: "html"}
            ]
        }
    }

];

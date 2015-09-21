module.exports = [

    {
        entry: {
            /*pagekit addons*/
            /*frontpage views*/
            "cart": "./app/views/cart.js",
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

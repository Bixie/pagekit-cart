module.exports = {


    timezoneOptions: function () {
        var options = {};
        _.forIn(window.$data.timezones, function (zones, continent) {
            options[continent] = (function (zones) {
                var zoneOptions = {};
                _.forIn(zones, function (zone, code) {
                    zoneOptions[zone] = code;
                });
                return zoneOptions;
            }(zones));
        });
        return options;
    }

};
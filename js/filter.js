Vue.filter('money_format', function (value) {
    var pattern = /(?=((?!\b)\d{3})+$)/g;
    return value.replace(pattern, ',');
})
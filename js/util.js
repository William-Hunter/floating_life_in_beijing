var web_root = ".";

function quest(url, data, func) {
    $.ajax({
        url: url,
        methods: 'POST',
        dataType: 'json',
        async: false,
        data: data,
        success: func
    })
}

function GetQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

var API = {
    marketProducts: web_root + '/cgi/MarketController.php?func=market_products',
    mystock: web_root + '/cgi/MarketController.php?func=mystock'
}
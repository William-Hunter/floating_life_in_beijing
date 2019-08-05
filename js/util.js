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

function GetUrlParamter(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

var API = {
    stations:web_root + '/cgi/StationController.php',
    marketInit: web_root + '/cgi/MarketController.php?func=marketInit',
    marketInfo: web_root + '/cgi/MarketController.php?func=marketInfo',
    mystate:web_root + '/cgi/CharacterController.php?func=mystate',
    buy:web_root + '/cgi/MarketController.php?func=buy',
    sell:web_root + '/cgi/MarketController.php?func=sell',
}
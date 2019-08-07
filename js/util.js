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
    showAllHouses:web_root + '/cgi/HouseController.php?func=show',
    buyHouses:web_root + '/cgi/HouseController.php?func=buy',
    showAllBriberyPlan:web_root + '/cgi/GovernorController.php?func=show',
    buyBriberyPlan:web_root + '/cgi/GovernorController.php?func=bribery',
    showAllSurgery:web_root + '/cgi/HospitalController.php?func=show',
    dealSurgery:web_root + '/cgi/HospitalController.php?func=deal',
    bankInit:web_root + '/cgi/BankController.php?func=init',
    borrow:web_root + '/cgi/BankController.php?func=borrow',
    repay:web_root + '/cgi/BankController.php?func=repay',



}


var bottomBar = Vue.extend({
    template:
        `<div class="container-fluid">
            <br><br><br>
            <div class="row">
                <div class="col-md-offset-3 col-md-1 ">
                    <button class="btn btn-info" type="button" v-on:click="station">火车站</button>
                </div>
                <div class="col-md-1 ">
                    <button class="btn btn-info" type="button" v-on:click="authentic">中南海</button>
                </div>
                <div class="col-md-1 ">
                    <button class="btn btn-info" type="button" v-on:click="medic">江湖郎中</button>
                </div>
                <div class="col-md-1 ">
                    <button class="btn btn-info" type="button" v-on:click="house">房产中介</button>
                </div>
                <div class="col-md-1 ">
                    <button class="btn btn-info" type="button" v-on:click="bank">地下钱庄</button>
                </div>
            </div>
            <br><br><br>
            <div class="row">
                <div class="col-md-offset-3 col-md-3 ">
                    <div class="row">
                        健康 <span class="badge">100%</span>
                    </div>
                    <div class="row">
                        资金 <span class="badge">2000000</span>
                    </div>
                </div>
                <div class="col-md-3">
                    罪恶 <span class="badge">300</span>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        仓储 <span class="badge">11/200</span>
                    </div>
                    <div class="row">
                        负债 <span class="badge">50000</span>
                    </div>
                </div>
            </div>
        </div>`,
    data:function () {
        return {'a':1}
    },
    methods: {
        station: function () {
            console.log("station")
            window.location.href = web_root + "/station.html"
        },
        authentic: function () {
            console.log("authentic")
            window.location.href = web_root + "/authentic.html"
        },
        medic: function () {
            console.log("medic")
            window.location.href = web_root + "/medic.html"
        },
        house: function () {
            console.log("house")
            window.location.href = web_root + "/house.html"
        },
        bank: function () {
            console.log("bank")
            window.location.href = web_root + "/bank.html"
        }
    }
})
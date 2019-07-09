var bottomBar = Vue.extend({
    template:
        `<div class="container-fluid" >
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
                        健康 <span class="badge">{{state.health}}%</span>
                    </div>
                    <div class="row">
                        资金 <span class="badge">{{state.money}}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    罪恶 <span class="badge">{{state.crime}}</span>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        仓储 <span class="badge">{{state.stock}}/{{state.stock}}</span>
                    </div>
                    <div class="row">
                        负债 <span class="badge">{{state.debt}}</span>
                    </div>
                </div>
            </div>
        </div>`,
    data:function () {
        return {"state":{}}
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
    },
    created(){
        var this_ = this;
        var place_id = GetUrlParamter("id");
        quest(API.mystate, {}, function (data) {
            if (data.code == 200) {
                this_.state=data.state
                console.log(this_.state)
            }
        });
    }
})
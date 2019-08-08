<?php

require_once('MongoUtil.php');
class Calc {
    /**
     * 计算已有库存数
     * @return mixed
     */
    final static public function myStockAmount(){
        $command = [
            'aggregate' => 'inventory',
            'pipeline' => [
                [
                    '$group' => [
                        "_id" => [],
                        "total" => [
                            '$sum' => '$quantity'
                        ]
                    ]
                ]
            ],
            'cursor' => (object)[]
        ];
        return  MongoUtil::command($command)[0]['total'];
    }

    /**
     * 罪恶值上涨
     * @param $money  涉案金额
     * @return mixed
     */
    final static public function evilRise($money){
        $state=MongoUtil::query("character",[],['limit' => 1])[0];
        $crime=($money*(random_int(1,19)/100.0))+$state['crime'];
        $state['crime']=(int)$crime;
        MongoUtil::insertOrUpdateById('character',$state);
    }

    /**
     * 我的信息
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     */
    final static public function mystate(){
        $state=MongoUtil::query("character",[],['limit' => 1])[0];
        $stockAmount=Calc::myStockAmount();
        if($stockAmount==null)$stockAmount=0;
        $state['stocked']=$stockAmount;
        return $state;
    }

    final static public function rateChange(){
        return  round(random_int(5,15)/100,2);
    }


    function changePrice($product,$coefficient){
        $new_price = $product['base_price'] *$coefficient;
        $product['current_price'] = round($new_price, 2);
        MongoUtil::insertOrUpdateById('product', $product);
    }

}
?>

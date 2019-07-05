<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/4
 * Time: 16:49
 */

require_once('config.php');

class MongoUtil {

    /**
     * 通过对象id查找信息
     * @param $ObjectId
     * @return array|null
     */
    final static public function queryById($collection,$ObjectId) {
        $filter = [
            '_id' => new MongoDB\BSON\ObjectId($ObjectId)
        ];
    
        $data=MongoUtil::query($collection,$filter);
        
        if($data[0]!=null){
            return $data[0];
        }else{
            return;
        }
    }
    
    
    
    
    /**
     * 查询数据，没有参数的话，就是查询所有
     * @param array $filter
     * @param null $queryOptions
     * @return array|null
     * @throws \MongoDB\Driver\Exception\Exception
     */
    final static public function query($collection,$filter=[],$queryOptions=null) {
        global $mongo_config;
        
//        $manager=MongoUtil::createConnect();
        $manager=MongoUtil::connect();
        
//        $queryOptions = [
            //    'projection' => ['_id' => 0],
            //    'sort' => ['x' => -1],
//        ];
        
        $query = new MongoDB\Driver\Query($filter, $queryOptions);
        $cursor = $manager->executeQuery($mongo_config['db'] . ".".$collection, $query);
        $retu=null;
        foreach ($cursor as $document) {
            $retu[] = (array)$document;
        }
        return $retu;
    }
    
    final static private function createConnect() {
        global $mongo_config;
        $connStr="mongodb://" . $mongo_config['user'] . ":" . $mongo_config['passwd'] . "@" . $mongo_config['IP'] .":". $mongo_config['port'];
        return new MongoDB\Driver\Manager($connStr);
    }
    
    
    /**
     * 创建链接
     * @return bool|\MongoDB\Driver\Manager
     */
    final static private function connect () {
        global $mongo_config;
        try {
            $connStr = "mongodb://" . $mongo_config['IP'] . ":" . $mongo_config['port'] ;
//            echo $connStr;
            $options = array(
                'username' => $mongo_config['user'],
                'password' => $mongo_config['passwd'],
                'readPreference' => $mongo_config['read_preference']
//                'connectTimeoutMS' => intval($mongo_config['connect_timeout_ms']),
//                'socketTimeoutMS' => intval($mongo_config['socket_timeout_ms']),
            );
            $mc = new MongoDB\Driver\Manager($connStr,$options);
            return $mc;
        } catch (Exception $e) {
            return false;
        }
    }
    
    final static public function find ($query = array(), $fields = array(), $collection, $sort = array(), $limit = 0, $skip = 0) {
        $conn = MongoUtil::connect();
        if (empty($conn)) {
            return false;
        }
        try {
            $data = array();
            $options = array();
            if (!empty($query)) {
                $options['projection'] = array_fill_keys($fields, 1);
            }
            if (!empty($sort)) {
                $options['sort'] = $sort;
            }
            if (!empty($limit)) {
                $options['skip'] = $skip;
                $options['limit'] = $limit;
            }
            $mongoQuery = new MongoDB\Driver\Query($query, $options);
            $readPreference = new MongoDB\Driver\ReadPreference(MongoDB\Driver\ReadPreference::RP_SECONDARY);
            $cursor = $conn->executeQuery($collection, $mongoQuery, $readPreference);
            foreach ($cursor as $value) {
                $data[] = (array)$value;
            }
            return $data;
        } catch (Exception $e) {
            //记录错误日志
        }
        return false;
    }
}

?>


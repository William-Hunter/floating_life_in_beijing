<?php
/**
 * Created by PhpStorm.
 * User: william
 * Date: 2019/7/4
 * Time: 16:49
 */

use MongoDB\Driver\BulkWrite;

require_once('config.php');

class MongoUtil {

    final static public function generatesId ($collection) {
        $options = [
            'limit' => 1,
            '$project'=>['_id'=>1]
        ];
        $data = MongoUtil::query($collection, [], $options);
        return;
    }

    /**
     * 根据id删除记录
     * @param $collection   表名
     * @param $id     记录id
     * @return array|bool
     */
    final static public function deleteById($collection,$id){
        try{
            global $mongo_config;
            $manager = MongoUtil::connect();
            $bulk = new MongoDB\Driver\BulkWrite;
            $filter = ['_id' => $id];
            $Options = ['limit' => 0];
            $bulk->delete($filter, $Options);
            $result=$manager->executeBulkWrite($mongo_config['db'] . "." . $collection, $bulk);
            if($result->getDeletedCount()>0){
                return true;
            }else{
                throw new Exception("删除失败");
                return false;
            }
        }catch (Exception $e){
            return array("code" => 500, "msg" =>$e->getMessage());
        }
    }
    
    /**
     * 根据id修改，如果不存在就插入
     * @param $collection
     * @param $newObj
     * @return bool
     */
    final static public function insertOrUpdateById ($collection, $newObj) {
        try{
            $operater=MongoUtil::insertOrUpdate($collection,['_id' => $newObj['_id']],$newObj);
            if($operater->getModifiedCount()>0||$operater->getInsertedCount()>0){
                return true;
            }else{
                throw new Exception("插入失败");
                return false;
            }
        }catch (Exception $e){
            return array("code" => 500, "msg" =>$e->getMessage());
        }
    }
    
    
    /**
     * 更新和插入功能
     * @param $collection
     * @param array $filter
     * @param array $newObj
     * @param array $updateOptions
     * @return \MongoDB\Driver\WriteResult
     */
    final static public function insertOrUpdate ($collection, $filter = [], $newObj = [], array $updateOptions = []) {
        global $mongo_config;
        $manager = MongoUtil::connect();
        $bulk = new MongoDB\Driver\BulkWrite;
        $newObj = ['$set' => $newObj];
        $updateOptions = ['multi' => true, 'upsert' => true];
        $bulk->update($filter, $newObj, $updateOptions);
        $result=$manager->executeBulkWrite($mongo_config['db'] . "." . $collection, $bulk);
        return $result;
    }
    
    
    
    
    
    /**
     * 通过对象id查找信息
     * @param $ObjectId
     * @return array|null
     */
    final static public function queryById ($collection, $ObjectId) {
        $filter = [
            '_id' => $ObjectId
        ];
        $options = [
            'limit' => 1
        ];
        $data = MongoUtil::query($collection, $filter, $options);
        if ($data[0] <> null) {
            return $data[0];
        } else {
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
    final static public function query ($collection, $filter = [], $queryOptions = null) {
        global $mongo_config;
        $manager = MongoUtil::connect();
        $query = new MongoDB\Driver\Query($filter, $queryOptions);
        $cursor = $manager->executeQuery($mongo_config['db'] . "." . $collection, $query);
        $retu = null;
        foreach ($cursor as $document) {
            $retu[] = (array)$document;
        }
        return $retu;
    }
    
    
    final static public function command ($command) {
        global $mongo_config;
        try {
            $manager = MongoUtil::connect();
            $commandObj = new MongoDB\Driver\Command($command);
            
            $cursor = $manager->executeCommand($mongo_config['db'], $commandObj);
            $data = null;
            foreach ($cursor as $value) {
                $data[] = (array)$value;
            }
            return $data;
        } catch (Exception $e) {
            //记录错误日志
            echo 'Caught exception: ', $e->getMessage(), "\n";
            return false;
        }
    }
    
    
    /**
     * 创建链接
     * @return bool|\MongoDB\Driver\Manager
     */
    final static private function connect () {
        global $mongo_config;
        try {
            $connStr = "mongodb://" . $mongo_config['IP'] . ":" . $mongo_config['port'];
//            echo $connStr;
            $options = array(
                'username' => $mongo_config['user'],
                'password' => $mongo_config['passwd'],
                'readPreference' => $mongo_config['read_preference']
//                'connectTimeoutMS' => intval($mongo_config['connect_timeout_ms']),
//                'socketTimeoutMS' => intval($mongo_config['socket_timeout_ms']),
            );
            return new MongoDB\Driver\Manager($connStr, $options);
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
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
            echo 'Caught exception: ', $e->getMessage(), "\n";
            return false;
        }
        return false;
    }
}

?>


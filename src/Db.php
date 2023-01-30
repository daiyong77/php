<?php

namespace Daiyong;

use PDO;
use PDOException;

//数据库
//db::connect('mysql:host=127.0.0.1;dbname=test','root','root','表前缀','utf8');
//db::$pdo 可以使用pdo原生写法，前提必须先连接数据库
class Db
{
    private static $config = array();
    public static $pdo;
    public static $key = 'id'; //表的主键
    //插入数据(只支持简易sql,原生请用query)
    // insert('table',array('key'=>'value','key2'=>'value2'))
    // return 插入的主键号
    public static function insert($table, $data = array())
    {
        $table = self::tableName($table);
        foreach ($data as $k => $v) {
            if (!$v && $v !== 0) unset($data[$k]);
        }
        if (!$data) {
            exit($table . '插入的内容不能全为空' . PHP_EOL);
        }
        $keys = array_keys($data);
        $keys1 = '`' . implode('`,`', $keys) . '`';
        $keys2 = ':' . implode(',:', $keys);
        $sql = "INSERT INTO {$table} ({$keys1}) VALUES ({$keys2})";
        $data_new = array();
        foreach ($data as $k => $v) {
            $data_new[':' . $k] = $v;
        }
        self::query($sql, $data_new);
        return self::$pdo->lastinsertid();
    }
    //删除数据(只支持简易sql,原生请用query)
    //delete('table',array('id'=>1,'username'=>'daiyong'))
    //给只有一个?的语句传参可以为字符串
    //delete('table','fid=? or id!=?',array(1,2))
    //delete('table','fid=:fid or id!=:id',array(':id'=>1,':fid'=>2))
    //return 影响的行数
    public static function delete($table, $where = array(), $data = array())
    {
        $table = self::tableName($table);
        if (!$where) {
            exit('不允许对' . $table . '进行全表删除' . PHP_EOL);
        }
        if (is_array($where)) {
            $array = self::arrayWhere($where);
            if (!$array['where']) {
                exit('不允许对' . $table . '进行全表删除' . PHP_EOL);
            }
            $where = $array['where'];
            $data = $array['data'];
        }
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return self::query($sql, $data)->rowCount();
    }
    //修改数据
    //update('table',array('name'=>'daiyong','age'=>'24'),array('id'=>1))
    //给只有一个?的语句传参可以为字符串
    //update('table',array('name'=>'daiyong'),'id=? or id=?',array(1,2))
    //update('table',array('name'=>'daiyong'),'id=:id or id=:id2',array(':id'=>1,':id2'=>2))
    //return 影响的行数
    public static function update($table, $data = array(), $where = array(), $where_data = array())
    {
        if (!$where) {
            exit('不允许对' . $table . '进行全表修改' . PHP_EOL);
        }
        $table = self::tableName($table);
        //解析修改的部分
        $set = array();
        $set_data = array();
        if (is_string($where) && strpos($where, '?')) { //?形式
            foreach ($data as $k => $v) {
                if (strpos($k, '|^')) {
                    $set[] = '`' . str_replace('|^', '', $k) . '`=' . $v;
                } else {
                    $set[] = '`' . $k . '`=?';
                    $set_data[] = $v;
                }
            }
        } else { //:形式
            foreach ($data as $k => $v) {
                if (strpos($k, '|^')) {
                    $set[] = '`' . str_replace('|^', '', $k) . '`=' . $v;
                } else {
                    $set[] = '`' . $k . '`=:' . $k;
                    $set_data[':' . $k] = $v;
                }
            }
        }
        $set = implode(',', $set);
        //条件
        if (is_array($where)) {
            $return = self::arrayWhere($where);
            $where = $return['where'];
            $data = array_merge($set_data, $return['data']);
        } else {
            if (!is_array($where_data)) {
                $where_data = array($where_data);
            }
            $data = array_merge($set_data, $where_data);
        }
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        return self::query($sql, $data)->rowCount();
    }
    //查询列表
    //findAll('table|username,password',array('id'=>1),'order by id desc limit 0,10','id')
    //给只有一个?的语句传参可以为字符串
    //findAll('select * from table where sex=? or sex=? limit 0,10',array('男','未知'))
    //以id为数组key值
    //findAll('select * from table where sex=:sex limit 0,10',array(':sex'=>'男'),'id')
    //return array
    public static function findAll($sql, $data = array(), $ol = '', $key = '')
    {
        if (!(strpos($sql, ' ') === 0 || strpos($sql, ' '))) {
            if (!(strpos($ol, ' ') === 0 || strpos($ol, ' '))) {
                $key = $ol;
                $ol = '';
            }
            if (!$ol) $ol = 'limit 0,500'; //没有写就默认查询10000条
            //处理简易sql
            $sql = explode('|', $sql);
            $table = self::tableName($sql[0]);
            if (!isset($sql[1])) {
                $sql[1] = '*';
            }
            $array = self::arrayWhere($data);
            $where = '';
            if ($array['where']) {
                $where = 'where ' . $array['where'];
            }
            $sql = "select {$sql[1]} from {$table} {$where} {$ol}";
            $data = $array['data'];
        } else {
            $key = $ol; //默认以该字段为key值
        }
        $result = self::query($sql, $data);
        $data = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            if (is_array($row) && count($row) == 1) {
                if ($key && isset($row[$key])) {
                    $data[$row[$key]] = current($row);
                } else {
                    $data[] = current($row);
                }
            } elseif (is_array($row) && count($row) == 2 && $key && isset($row[$key])) {
                $key_val = $row[$key];
                unset($row[$key]);
                $data[$key_val] = current($row);
            } else {
                if ($key && isset($row[$key])) {
                    $data[$row[$key]] = $row;
                } else {
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
    //查询单个
    //find('table|username,password',array('id'=>1))
    //给只有一个?的语句传参可以为字符串
    //find('select * from table where id=? or is=?',array('男','1'))
    //find('select * from table where id=:id and is=:is',array(':id'=>'男',':is'=>'24'))
    //rerturn array|string
    public static function find($sql, $data = array(), $ol = '')
    {
        if (!(strpos($sql, ' ') === 0 || strpos($sql, ' '))) {
            if (!$ol) $ol = 'limit 0,1';
            //处理简易sql
            $sql = explode('|', $sql);
            $table = self::tableName($sql[0]);
            if (!isset($sql[1])) {
                $sql[1] = '*';
            }
            $array = self::arrayWhere($data);
            $where = '';
            if ($array['where']) {
                $where = 'where ' . $array['where'];
            }
            $sql = "select {$sql[1]} from {$table} {$where} {$ol}";
            $data = $array['data'];
        }
        $result = self::query($sql, $data);
        $data = $result->fetch(PDO::FETCH_ASSOC);
        if (is_array($data) && count($data) == 1) {
            $data = current($data);
        }
        return $data;
    }
    //数据库连接
    //connect('mysql:host=127.0.0.1;dbname=login','root','root','utf8');
    //return pdo
    public static function connect($connect = '', $username = '', $password = '', $charset = 'utf8')
    {
        if (is_array($connect) && $connect) {
            $username = $connect['username'];
            $password = $connect['password'];
            $charset = $connect['charset'];
            $connect = $connect['connect'];
        }
        if (!$connect) {
            return self::$pdo;
        } else {
            self::$config = array(
                'connect' => $connect,
                'username' => $username,
                'password' => $password,
                'charset' => $charset,
            );
        }
        try {
            self::$pdo = @new PDO(self::$config['connect'], self::$config['username'], self::$config['password']);
        } catch (PDOException $e) {
            exit('数据库连接失败，错误信息：' . iconv('gbk', 'utf-8', $e->getMessage()) . PHP_EOL);
        }
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //产生致命错误
        self::$pdo->exec('set names ' . self::$config['charset']);
        return self::$pdo;
    }
    //执行sql
    //query('sql语句可带?或:key','数据')
    //return sql结果
    public static function query($sql, $data = array())
    {
        if (!is_array($data)) $data = array($data);
        try {
            $result = self::connect()->prepare($sql);
            @$result->execute($data);
        } catch (PDOException $e) {
            //可能连接会断掉重连一次
            try {
                $result = self::connect(self::$config['connect'], self::$config['username'], self::$config['password'], self::$config['charset'])->prepare($sql);
                @$result->execute($data);
            } catch (PDOException $e) {
                foreach ($data as $k => $v) {
                    if (strlen($v) > 32) $v = '*****';
                    $sql = str_replace($k, "'" . $v . "'", $sql);
                }
                exit($sql . '|' . $e->getMessage() . PHP_EOL);
            }
        }
        return $result;
    }
    //将数组转换成pdo标准sql语句与数据
    // arrayWhere(array(
    //     'username|like'=>'%daiyong%',
    //     'password'=>'123456'
    // ));
    // return array(
    //     'where'=>'username like :username and password=:password',
    //     'data'=>array(
    //         ':username'=>'%daiyong%',
    //         ':password'=>'123456'
    //     )
    // )
    private static function arrayWhere($array)
    {
        $where = array();
        $data = array();
        foreach ($array as $k => $v) {
            $k = explode('|', $k);
            if (isset($k[1])) {
                $relation = $k[1];
            } else {
                $relation = '=';
            }
            if (strpos($relation, '^')) {
                $where[] = '`' . $k[0] . '` ' . str_replace('^', '', $relation) . ' ' . $v;
            } else {
                //为防止更插入或添加时的参数重复所以添加“_”
                $where[] = '`' . $k[0] . '` ' . $relation . ' :_' . $k[0] . (isset($k[1]) ? md5($k[1]) : '');
                $data[':_' . $k[0] . (isset($k[1]) ? md5($k[1]) : '')] = $v;
            }
        }
        $where = implode(' and ', $where);
        return array(
            'where' => $where,
            'data' => $data
        );
    }
    //获取树形数据父级信息
    public static function getFather($table, $fid)
    {
        return array_reverse(self::getFather2($table, $fid));
    }
    public static function getFather2($table, $fid)
    {
        $list = array();
        $father = db::find($table, array('id' => $fid));
        $list[] = $father;
        if ($father['fid']) {
            $list = array_merge($list, self::getFather2($table, $father['fid']));
        }
        return $list;
    }
    //获取树形数据获取子集id
    public static function getChildId($table, $id)
    {
        $list = array();
        $child = db::findAll($table . '|id', array('fid' => $id));
        foreach ($child as $v) {
            $list[] = $v;
            $list = array_merge($list, self::getChildId($table, $v));
        }
        return $list;
    }
    //解析简略sql写法的表名
    private static function tableName($table)
    {
        $has = strpos($table, '`');
        if (!$has && $has !== 0) {
            if (!strpos($table, '.')) {
                $table = '`' . $table . '`';
            } else {
                $table = '`' . implode('`.`', explode('.', $table)) . '`';
            }
        }
        return $table;
    }
}

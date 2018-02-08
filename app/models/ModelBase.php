<?php
require_once(dirname(__FILE__) . '/../../config/DBConfig.php');

class ModelBase
{
    protected $db;

    public function __construct()
    {
        $this->initDB();
    }

    public function initDB()
    {
        $this->db = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME.';charset=utf8'
            ,DB_USER
            ,DB_PASSWD);
        // 静的プレースホルダを用いるようにエミュレーションを無効化
        $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function fetch($sql)
    {
        $stmt = $this->db->query($sql);
        $stmt -> execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchAll($sql, $type)
    {
        $stmt = $this->db->query($sql);
        $stmt -> execute();
        return $stmt->fetchAll($type);
    }

    public function prepare_fetchAll($sql, $bindval, $type)
    {
        $stmt = $this->db->prepare($sql);

        foreach ($bindval as $val){
            $stmt->bindValue($val["param"], $val["val"], $val["type"]);
        }

        $stmt -> execute();
        return $stmt->fetchAll($type);
    }

    public function execute($sql)
    {
        return $this->db->exec($sql);
    }

    function begin() {
        return $this->db->beginTransaction();
    }
    function commit() {
        return $this->db->commit();
    }
    function rollback() {
        return $this->db->rollBack();
    }
}
?>
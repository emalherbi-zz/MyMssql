<?php

/*
 * Eduardo Malherbi Martins (http://emalherbi.com/)
 * Copyright @emm
 * Full Stack Web Developer.
 */

set_time_limit(0);

ini_set('memory_limit', '512M');
ini_set('mssql.timeout', '999999');
ini_set('max_execution_time', '999999');
ini_set('soap.wsdl_cache_ttl', '999999');
ini_set('mssql.textlimit', '2147483647');
ini_set('mssql.textsize', '2147483647');

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') || define('ROOT', realpath(__DIR__));

class MyMssql
{
    private $db = null;
    private $ini = null;

    public function __construct()
    {
        $this->ini = parse_ini_file(ROOT.DS.'MyMssql.ini');
        $this->connect();
    }

    public function connect()
    {
        if (!empty($this->db)) {
            return $this->db;
        }

        $hostname = $this->ini['HOSTNAME'];
        $username = $this->ini['USERNAME'];
        $password = $this->ini['PASSWORD'];
        $database = $this->ini['DATABASE'];

        try {
            if ('SQLSRV' === $this->ini['ADAPTER']) {
                $driver = "sqlsrv:server=$hostname; database=$database";
            } else {
                $driver = "mssql:host=$hostname; dbname=$database";
            }

            $this->db = new PDO($driver, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die(print_r($e->getMessage()));
        }
    }

    public function disconnect()
    {
        $this->db = null;
    }

    public function fetchRow($sql)
    {
        $query = $this->query($sql);

        $result = false;
        foreach ($query as $row) {
            $result = $row;
            break;
        }

        return $result;
    }

    public function fetchAll($sql)
    {
        $query = $this->query($sql);

        $result = array();
        foreach ($query as $row) {
            $result[] = $row;
        }

        return $result;
    }

    public function exec($sql)
    {
        try {
            $this->connect();

            return $this->db->exec($sql);
        } catch (Exception $e) {
            die(print_r($e->getMessage()));
        }
    }

    public function begin()
    {
        $this->connect();
        $this->db->beginTransaction();
    }

    public function commit()
    {
        $this->connect();
        $this->db->commit();
    }

    public function rollback()
    {
        $this->connect();
        $this->db->rollBack();
    }

    private function query($sql)
    {
        try {
            $this->connect();
            $query = $this->db->query($sql, PDO::FETCH_ASSOC);

            if (empty($query)) {
                $this->logger('ERROR SQL: ', $sql);

                return false;
            }

            return $query;
        } catch (Exception $e) {
            die(print_r($e->getMessage()));
        }
    }

    private function logger($str, $result)
    {
        if (false == $this->ini['DEBUG']) {
            return;
        }

        $date = date('y-m-d');
        $hour = date('H:i:s');

        @mkdir(ROOT, 0777, true);
        @chmod(ROOT, 0777);

        $log = '';
        $log .= "[$hour] > $str \n";
        $log .= "[RESULT] > $result \n\n";

        $file = fopen(ROOT.DS."logger-$date.txt", 'a+b');
        fwrite($file, $log);
        fclose($file);
    }
}

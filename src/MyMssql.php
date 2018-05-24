<?php

/*
 * Eduardo Malherbi Martins (http://emalherbi.com/)
 * Copyright @emm
 * Full Stack Web Developer.
 */

namespace MyMssql;

use Exception;
use PDO;

set_time_limit(0);

ini_set('memory_limit', '512M');
ini_set('mssql.timeout', '999999');
ini_set('max_execution_time', '999999');
ini_set('soap.wsdl_cache_ttl', '999999');
ini_set('mssql.textlimit', '2147483647');
ini_set('mssql.textsize', '2147483647');

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

class MyMssql
{
    private $DS = null; // DS
    private $RT = null; // ROOT
    private $DL = null; // DIR LOG

    private $db = null;
    private $ini = null;

    public function __construct($ini = array(), $dl = '')
    {
        $this->DS = DIRECTORY_SEPARATOR;
        $this->RT = realpath(dirname(__FILE__));
        $this->DL = empty($dl) ? realpath(dirname(__FILE__)) : $dl;

        if (!empty($ini)) {
            $this->setIni($ini);
        }
        $this->ini = $this->getIni();
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

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Connect');
            $this->logger('HOSTNAME: '.$hostname);
            $this->logger('USERNAME: '.$username);
            $this->logger('PASSWORD: '.$password);
            $this->logger('DATABASE: '.$database);
        }

        try {
            if ('SQLSRV' === $this->ini['ADAPTER']) {
                $driver = "sqlsrv:server=$hostname; database=$database";
            } else {
                $driver = "mssql:host=$hostname; dbname=$database";
            }

            $this->db = new PDO($driver, $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            $err = $e->getMessage();
            $this->logger('MyMssql Connect', $err);
            die(print_r($e->getMessage()));
        }
    }

    public function disconnect()
    {
        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Disconnect');
        }

        $this->db = null;
    }

    public function fetchOne($sql)
    {
        $query = $this->query($sql);

        $result = array();
        foreach ($query as $row) {
            $result[] = $row;
            break;
        }

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Fetch One: '.json_encode($result));
        }

        return empty($result) ? false : $result[0];
    }

    public function fetchRow($sql)
    {
        $query = $this->query($sql);

        $result = array();
        foreach ($query as $row) {
            $result[] = $row;
            break;
        }

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Fetch Row: '.json_encode($result));
        }

        return empty($result) ? false : $result[0];
    }

    public function fetchAll($sql)
    {
        $query = $this->query($sql);

        $result = array();
        foreach ($query as $row) {
            $result[] = $row;
        }

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Fetch All: '.json_encode($result));
        }

        return empty($result) ? false : $result;
    }

    public function exec($sql)
    {
        try {
            $this->connect();

            if (true == $this->ini['VERBOSE']) {
                $this->logger('MyMssql Exec: '.$sql);
            }

            return $this->db->exec($sql);
        } catch (Exception $e) {
            $err = $e->getMessage();
            $this->logger($sql, $err);
            die(print_r($e->getMessage()));
        }
    }

    public function begin()
    {
        $this->connect();

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Begin Transaction');
        }

        if ('SQLSRV' === $this->ini['ADAPTER']) {
            $this->db->beginTransaction();
        } else {
            $this->logger('MyMssql Begin Transaction Only Works in SQLSRV ADAPTER');
        }
    }

    public function commit()
    {
        $this->connect();

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Commit');
        }

        if ('SQLSRV' === $this->ini['ADAPTER']) {
            $this->db->commit();
        } else {
            $this->logger('MyMssql Commit Only Works in SQLSRV ADAPTER');
        }
    }

    public function rollback()
    {
        $this->connect();

        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql RollBack');
        }

        if ('SQLSRV' === $this->ini['ADAPTER']) {
            $this->db->rollBack();
        } else {
            $this->logger('MyMssql RollBack Only Works in SQLSRV ADAPTER');
        }
    }

    public function getIni()
    {
        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Get Ini');
        }

        return parse_ini_file($this->RT.$this->DS.'MyMssql.ini');
    }

    private function setIni($ini = array())
    {
        if (true == $this->ini['VERBOSE']) {
            $this->logger('MyMssql Set Ini');
        }

        INI::write($this->RT.$this->DS.'MyMssql.ini', array('INI' => $ini));
    }

    private function query($sql)
    {
        try {
            $this->connect();

            if (true == $this->ini['VERBOSE']) {
                $this->logger('MyMssql Query: '.$sql);
            }

            return $this->db->query($sql, PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $err = $e->getMessage();
            $this->logger($sql, $err);
            die(print_r($e->getMessage()));
        }
    }

    private function logger($str, $err = '')
    {
        $date = date('Y-m-d');
        $hour = date('H:i:s');

        @mkdir($this->DL, 0777, true);
        @chmod($this->DL, 0777);

        $log = '';
        $log .= "[$hour] > $str \n";
        if (!empty($err)) {
            $log .= "[ERROR] > $err \n\n";
        }

        $file = fopen($this->DL.$this->DS."log-$date.txt", 'a+b');
        fwrite($file, $log);
        fclose($file);
    }
}

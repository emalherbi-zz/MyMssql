<?php

/*
 * Eduardo Malherbi Martins (http://emalherbi.com/)
 * Copyright @emm
 * Full Stack Web Developer.
 */

namespace MyMssql;

class MyMssqlSx extends MyMssql
{
    public function __construct($ini = array(), $dl = '')
    {
        parent::__construct($ini, $dl);
    }

    public function fetchRowSx($sxName, $params, $test = false)
    {
        return $this->querySx($sxName, $params, $test, 'fetchRow');
    }

    public function fetchAllSx($sxName, $params, $test = false)
    {
        return $this->querySx($sxName, $params, $test, 'fetchAll');
    }

    public function execSx($sxName, $params, $test = false)
    {
        return $this->querySx($sxName, $params, $test, 'exec');
    }

    private function querySx($sxName, $params, $test = false, $function = 'exec')
    {
        if (false === $this->sxExist($sxName)) {
            return 'Stored Procedure '.$sxName.' not find.';
        }

        $array = $this->getFields($sxName);

        if (count($params) !== count($array)) {
            return 'Parameters reported differently than stored procedure parameters.';
        }

        $sql = $sxName.' ';

        for ($i = 0; $i < count($params); ++$i) {
            if (0 !== $i) {
                $sql .= ', ';
            }

            $type = strtoupper($array[$i]['TYPE']);

            if (in_array($type, array('DATETIME', 'SMALLDATETIME', 'TIMESTAMP', 'CHAR', 'NCHAR', 'SQLCHAR', 'TEXT', 'NTEXT', 'VARCHAR', 'NVARCHAR', 'SQLVARCHAR', 'BINARY', 'VARBINARY', 'IMAGE'), true)) {
                $sql .= "'".$params[$i]."'";
            } else {
                $sql .= $params[$i];
            }
        }

        if (true === $test) {
            echo '<pre>';
            echo print_r($sql);
            echo '</pre>';
            exit;
        }

        return parent::$function($sql);
    }

    private function sxExist($sxName)
    {
        $sql = '';
        $sql .= ' SELECT COUNT(*) AS EXIST ';
        $sql .= ' FROM SYSOBJECTS ';
        $sql .= " WHERE ID = OBJECT_ID('".$sxName."')";

        $array = parent::fetchRow($sql);
        $count = $array['EXIST'];

        return ($count > 0) ? true : false;
    }

    private function getFields($sxName)
    {
        $sql = '';
        $sql .= ' SELECT ';
        $sql .= ' COLUMNS.NAME AS COLUMNS, ';
        $sql .= ' TYPES.NAME AS TYPE, ';
        $sql .= ' COLUMNS.ISOUTPARAM, ';
        $sql .= ' COLUMNS.LENGTH ';
        $sql .= ' FROM ';
        $sql .= ' SYSOBJECTS AS TABLES, ';
        $sql .= ' SYSCOLUMNS AS COLUMNS, ';
        $sql .= ' SYSTYPES AS TYPES ';
        $sql .= ' WHERE ';
        $sql .= ' TABLES.ID = COLUMNS.ID ';
        $sql .= ' AND COLUMNS.USERTYPE = TYPES.USERTYPE ';
        $sql .= " AND TABLES.NAME = '".$sxName."'";
        $sql .= ' ORDER BY TABLES.ID ';

        return parent::fetchAll($sql);
    }
}

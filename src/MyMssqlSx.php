<?php

/*
 * Eduardo Malherbi Martins (http://emalherbi.com/)
 * Copyright @emm
 * Full Stack Web Developer.
 */

// require_once 'MyMssql.php';

namespace MyMssqlSx;

class MyMssqlSx extends MyMssql
{
    public function __construct()
    {
        // $this->connect();
    }

    // public function exeSql($sql)
    // {
    //     return $this->exec($sql);
    // }

    // public function closeConnection()
    // {
    //     $this->disconnect();
    // }

    // public function acessaSxRetornaSelect($sxName, $params)
    // {
    //     if ($this->sxExist($sxName)) {
    //         $array = $this->getFields($sxName);

    //         $sql = $sxName.' ';

    //         for ($i = 0; $i < count($params); ++$i) {
    //             if (!(0 === $i)) {
    //                 $sql .= ',';
    //             }

    //             if (in_array($this->getTypeSql($array[$i]['TIPO']), array(SQLVARCHAR, SQLCHAR), true)) {
    //                 $sql .= "'".$params[$i]."'";
    //             } else {
    //                 $sql .= $params[$i];
    //             }
    //         }

    //         return $this->exec($sql);
    //     }

    //     return 'Stored Procedure '.$sxName.' not find.';
    // }

    // public function acessaSX($sxName, $params, $isOutput = false, $test = false)
    // {
    //     if ($this->sxExist($sxName)) {
    //         $array = $this->getFields($sxName);

    //         if (count($params) !== count($array)) {
    //             return 'Parameters reported differently than stored procedure parameters.';
    //         }

    //         return $this->execSx($sxName, $params, $array, $isOutput, $test);
    //     }

    //     return 'Stored Procedure '.$sxName.' not find.';
    // }

    // public function acessaSX2($sx_name, $params, $is_output, $return_num = false, $teste = false)

    public function execSx($sxName, $params, $isOutput = false, $isReturnRow = false, $test = false)
    {
        if ($this->sxExist($sxName)) {
            $array = $this->getFields($sxName);

            if (count($params) !== count($array)) {
                return 'Parameters reported differently than stored procedure parameters.';
            }

            $sql = '';
            $sql .= ' BEGIN ';
            $sql .= $this->sqlDeclare($array);
            $sql .= $this->sqlSet($array, $params);
            $sql .= " EXEC $sxName ";
            $sql .= $this->sqlValues($array, $params);

            if (true === $isOutput) {
                $arrOutput = $this->sqlSelectOutput($array);
                $sql .= $arrOutput['sql'];
            }

            $sql .= ' END ';

            if (true === $test) {
                echo '<pre>';
                echo print_r($sql);
                echo '</pre>';
                exit;
            }

            if (false === $isOutput) {
                $result = $this->exec($sql);
                $this->disconnect();

                return empty($result) ? false : true;
            }

            if (empty($arrOutput['sql'])) {
                $result = $this->exec($sql);
                $this->disconnect();

                return empty($result) ? false : true;
            }

            $result = $this->fetchRow($sql);

            if (true === $isReturnRow) {
                $this->disconnect();

                return $result;
            }

            $this->disconnect();

            $id = $result["'".$arrOutput['column']."'"];

            return $id;
        }

        $this->disconnect();

        return 'Stored Procedure '.$sxName.' not find.';
    }

    /* START */

    private function sxExist($sxName)
    {
        $sql = '';
        $sql .= ' SELECT COUNT(*) AS EXIST ';
        $sql .= ' FROM SYSOBJECTS ';
        $sql .= " WHERE ID = OBJECT_ID('".$sxName."')";

        $array = $this->fetchRow($sql);
        $count = $array['EXIST'];

        return ($count > 0) ? true : false;
    }

    private function getFields($sxName)
    {
        $sql = '';
        $sql .= ' SELECT ';
        $sql .= ' COLUNAS.NAME AS COLUNA, ';
        $sql .= ' TIPOS.NAME AS TIPO, ';
        $sql .= ' COLUNAS.ISOUTPARAM AS ISOUTPARAM, ';
        $sql .= ' COLUNAS.LENGTH AS TAMANHO ';
        $sql .= ' FROM ';
        $sql .= ' SYSOBJECTS AS TABELAS, ';
        $sql .= ' SYSCOLUMNS AS COLUNAS, ';
        $sql .= ' SYSTYPES AS TIPOS ';
        $sql .= ' WHERE ';
        $sql .= ' TABELAS.ID = COLUNAS.ID ';
        $sql .= ' AND COLUNAS.USERTYPE = TIPOS.USERTYPE ';
        $sql .= " AND TABELAS.NAME = '".$sxName."'";
        $sql .= ' ORDER BY TABELAS.ID ';

        return $this->fetchAll($sql);
    }

    // private function getTypeSql($type)
    // {
    //     if ('bit' === $type) {
    //         return SQLBIT;
    //     } elseif ('tinyint' === $type) {
    //         return SQLINT1;
    //     } elseif ('smallint' === $type) {
    //         return SQLINT4;
    //     } elseif ('int' === $type) {
    //         return SQLINT4;
    //     } elseif ('bigint' === $type) {
    //         return SQLINT4;
    //     } elseif ('datetime' === $type) {
    //         return SQLINT4;
    //     } elseif ('timestamp' === $type) {
    //         return SQLINT4;
    //     } elseif ('float' === $type) {
    //         return SQLFLT8;
    //     } elseif ('decimal' === $type) {
    //         return SQLFLT8;
    //     } elseif ('money' === $type) {
    //         return SQLFLT8;
    //     } elseif ('numeric' === $type) {
    //         return SQLFLT8;
    //     } elseif ('real' === $type) {
    //         return SQLFLT8;
    //     } elseif ('smallmoney' === $type) {
    //         return SQLFLT8;
    //     } elseif ('char' === $type) {
    //         return SQLCHAR;
    //     } elseif ('nchar' === $type) {
    //         return SQLCHAR;
    //     } elseif ('smalldatetime' === $type) {
    //         return SQLCHAR;
    //     } elseif ('text' === $type) {
    //         return SQLTEXT;
    //     } elseif ('ntext' === $type) {
    //         return SQLTEXT;
    //     } elseif ('varchar' === $type) {
    //         return SQLVARCHAR;
    //     } elseif ('binary' === $type) {
    //         return SQLVARCHAR;
    //     } elseif ('image' === $type) {
    //         return SQLVARCHAR;
    //     } elseif ('nvarchar' === $type) {
    //         return SQLVARCHAR;
    //     } elseif ('sql_variant' === $type) {
    //         return SQLVARCHAR;
    //     } elseif ('varbinary' === $type) {
    //         return SQLVARCHAR;
    //     }
    // }

    private function sqlDeclare($array)
    {
        $sql = '';

        for ($i = 0; $i < count($array); ++$i) {
            $column = $array[$i]['COLUNA'];
            $type = trim(strtoupper($array[$i]['TIPO']));
            $isOutParam = $array[$i]['ISOUTPARAM'];
            $size = $array[$i]['TAMANHO'];

            if ('DECIMAL' === $type) {
                $type = 'REAL';
            } elseif ('TEXT' === $type) {
                $type = 'VARCHAR(8000)';
            } elseif ('VARCHAR' === $type || 'NVARCHAR' === $type || 'CHAR' === $type || 'NCHAR' === $type || 'NTEXT' === $type) {
                $type = "$type($size)";
            }

            $sql .= " DECLARE $column $type ";
        }

        return $sql;
    }

    private function sqlSet($array, $params)
    {
        $sql = '';

        for ($i = 0; $i < count($array); ++$i) {
            $value = $params[$i];
            $column = $array[$i]['COLUNA'];
            $type = trim(strtoupper($array[$i]['TIPO']));
            $isOutParam = $array[$i]['ISOUTPARAM'];

            $sql .= " SET $column = ";

            if (!isset($value)) {
                $sql .= 'NULL';
            } elseif ('VARCHAR' === $type || 'IMAGE' === $type || 'NVARCHAR' === $type || 'SQL_VARIANT' === $type || 'VARBINARY' === $type || 'DATETIME' === $type || 'TIMESTAMP' === $type || 'CHAR' === $type || 'NCHAR' === $type || 'NTEXT' === $type || 'SMALLDATETIME' === $type || 'TEXT' === $type || 'BINARY' === $type) {
                $sql .= "'$value'";
            } elseif ('INT' === $type || 'BIT' === $type) {
                $sql .= (int) $value;
            }
        }

        return $sql;
    }

    private function sqlValues($array, $params)
    {
        $sql = '';

        for ($i = 0; $i < count($array); ++$i) {
            $value = $params[$i];
            $column = $array[$i]['COLUNA'];
            $type = trim(strtoupper($array[$i]['TIPO']));
            $isOutParam = $array[$i]['ISOUTPARAM'];

            if (0 !== $i) {
                $sql .= ',';
            }

            $sql .= " $column ";
            $sql .= (true == $isOutParam) ? ' OUTPUT ' : ' ';
            $sql .= " '$value' ";
        }

        return $sql;
    }

    private function sqlSelectOutput($array)
    {
        $sql = '';
        $column = '';

        for ($i = 0; $i < count($array); ++$i) {
            $column = $array[$i]['COLUNA'];
            $isOutParam = $array[$i]['ISOUTPARAM'];

            if ($isOutParam) {
                if (0 !== $i) {
                    $sql .= ',';
                }

                if (0 === $i) {
                    $sql .= ' SELECT ';
                }

                $sql .= " $column AS ".trim(str_replace('@', '', $column));
            }
        }

        return array(
            'sql' => $sql,
            'column' => $column,
        );
    }
}

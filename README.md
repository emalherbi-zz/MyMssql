# MyMssql

My Mssql PDO (MSSQL or SQLSRV)

# Install

```
composer require emalherbi/mymssql
```

# Usage

```php
require_once 'vendor/autoload.php';

try {
    // define timezone if not defined in ini file.
    if (@date_default_timezone_get() !== @ini_get('date.timezone')) {
        @date_default_timezone_set('America/Sao_Paulo');
    }

    $mssql = new MyMssql\MyMssql(array(
        'VERBOSE' => true,
        'ADAPTER' => 'SQLSRV', // or MSSQL
        'HOSTNAME' => '192.168.1.100', // or 192.168.1.100\\SQL2016
        'USERNAME' => 'USERNAME',
        'PASSWORD' => 'PASSWORD',
        'DATABASE' => 'DATABASE',
    ), realpath(dirname(__FILE__)), 'UTF-8'); // Ex.: ISO-8859-1

    $isConnect = $mssql->isConnect();
    echo '<pre>';
    echo print_r($isConnect);
    echo '</pre>';   

    $row = $mssql->fetchRow('SELECT * FROM CLIENTES');
    echo '<pre>';
    echo print_r($row);
    echo '</pre>';

    $all = $mssql->fetchAll('SELECT TOP 5 * FROM CLIENTES');
    echo '<pre>';
    echo print_r($all);
    echo '</pre>';

    $exec = $mssql->exec('UPDATE CLIENTES SET NOME = \'TESTE 123\' WHERE ID_CLIENTE = 450');
    echo '<pre>';
    echo print_r($exec);
    echo '</pre>';

    $mssql->begin();

    $exec = $mssql->exec('UPDATE CLIENTES SET NOME = \'TESTE 456\' WHERE ID_CLIENTE = 450');
    echo '<pre>';
    echo print_r($exec);
    echo '</pre>';

    $mssql->commit();

    $sxName = 'SX_CLIENTES';
    $params = array(1, 385);
    $result = $mssql->fetchRowSx($sxName, $params);
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';

    $sxName = 'SX_CLIENTES';
    $params = array(2, 385);
    $result = $mssql->fetchAllSx($sxName, $params);
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';

    $sxName = 'SX_CLIENTES_SAVE';
    $params = array(2, '2017-01-01', 385, 0, 0, 0, 0);
    $result = $mssql->execSx($sxName, $params);
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';

    $result = $mssql->execScript("IF OBJECT_ID('USUARIOS') IS NULL
        BEGIN
            CREATE TABLE [dbo].[USUARIOS]
            (
                [ID_USUARIOS] [INT] IDENTITY(1,1) NOT NULL,
                [NOME] VARCHAR(100) NOT NULL,
                PRIMARY KEY CLUSTERED
            ([ID_USUARIOS] ASC) WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
            ) ON [PRIMARY]
        END");
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';    

    echo 'Success...';
} catch (Exception $e) {
    $mssql->rollback();

    die(print_r($e->getMessage()));
}
```


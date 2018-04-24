# MyMssql

My Mssql PDO (MSSQL or SQLSRV)

# Install

```
composer require emalherbi/mymssql
```

# Usage

```php
require_once 'vendor/autoload.php';

// define timezone if not defined in ini file.
if (@date_default_timezone_get() !== @ini_get('date.timezone')) {
    @date_default_timezone_set('America/Sao_Paulo');
}

$conn = array(
    'VERBOSE' => false,
    'ADAPTER' => 'SQLSRV',
    'HOSTNAME' => '192.168.1.1',
    'USERNAME' => 'USERNAME',
    'PASSWORD' => 'PASSWORD',
    'DATABASE' => 'DATABASE',
);

$log = realpath(dirname(__FILE__));

$myMssql = new MyMssql\MyMssql($conn, $log);		

$result = $myMssql->fetchRow('SELECT * FROM TABLEX');	
echo '<pre>';
echo print_r($result);
echo '</pre>';

$result = $myMssql->fetchAll('SELECT TOP 5 * FROM TABLEX');	
echo '<pre>';
echo print_r($result);
echo '</pre>';

$result = $myMssql->exec('UPDATE TABLEX SET NAMEX=\'TESTE 000\' WHERE ID = 1');	
echo '<pre>';
echo print_r($result);
echo '</pre>';

try {
    $myMssql->begin();	

    $result = $myMssql->exec('UPDATE TABLEX SET NAMEX=\'TEST 111\' WHERE ID = 2');	
    echo '<pre>';
    echo print_r($result);
    echo '</pre>';

    $myMssql->commit();	     
} catch (Exception $e) {
    $myMssql->rollback();	 
    die(print_r($e->getMessage()));
}

exit;
```


# MyMssql

My Mssql Pdo (MSSQL or SQLSRV)

# Install

```
composer require emalherbi/mymssql
```

# Usage

```php
defined('PS') || define('PS', PATH_SEPARATOR);
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT') || define('ROOT', realpath(dirname(__FILE__)));
	
require_once ROOT.DS.'vendor'.DS.'autoload.php';
	
$myMssql = new MyMssql(array('INI' => array(
    'DEBUG' => false,
    'ADAPTER' => 'SQLSRV',
    'HOSTNAME' => '192.168.1.1',
    'USERNAME' => 'USERNAME',
    'PASSWORD' => 'PASSWORD',
    'DATABASE' => 'DATABASE',
)));		

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


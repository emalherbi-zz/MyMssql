# MyMssql

My Mssql Pdo (MSSQL or SQLSRV)

# Usage

```php
require_once "MyMssql.php";	
	
$myMssql = new MyMssql();		

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


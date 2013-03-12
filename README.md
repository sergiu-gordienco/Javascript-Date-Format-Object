mysql-query-composer
====================

An PHP class That allows you to build very complicate queries in a very easy mode

### Initialization and configuration

```php
	$db	= new MysqlQueryComposer();
	$db->setConfig('host',(string) @$_config['db']['host']);
	$db->setConfig('user','root');
	$db->setConfig('pass','');
	$db->setConfig('base','your-db-name');
	
	$db->connect();
	$db->connectDb();
```
> #### Use it as a global
> 
> ```php
> 	function __db() {
> 		global $db;
> 		return $db;
> 	}
> ```
> so we will use `__db()->...` insead of `global $db;$db->...`


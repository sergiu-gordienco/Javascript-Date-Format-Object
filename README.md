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

### Simple Queries

#### Select Query

> ```php
> 	$_db->select(
> 		'tbl_users'	=> 'users',
> 		array(
> 			'tbl_users.id'	=> 33,
> 			'tbl_users.type'	=> 'client'
> 		),
> 		array(
> 			'id'	=> 'tbl_users.id',
> 			'name'	=> 'tbl_users.name',
> 			'type'	=> 'tbl_users.type'
> 		),
> 		"LIMIT 0 , 5");
> ```
> 
> ##### Result Query
> 
> ```sql
> 	SELECT
> 		`your-db-name`.`tbl_users`.`id` as `id`,
> 		`your-db-name`.`tbl_users`.`name` as `name`,
> 		`your-db-name`.`tbl_users`.`type` as `type`
> 		FROM
> 			`your-db-name`.`users` AS `tbl_users`
> 	WHERE
> 		`tbl_users`.`id` = "33"
> 		AND
> 		`tbl_users`.`type` = "client"
> 	LIMIT 0 , 5
> ```
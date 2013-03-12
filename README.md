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
> 	__db()->select(
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
> ```mysql
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

#### Update Query

```php
	__db()->update(
		'users',
		array(
			'type'	=> "admin",
			'name'	=> "( CONCAT(`name`,'+updated') )"
		),
		array(
			'and',	// optional row
			'type'	=> "client",
			'name NOT LIKE "Mark%"',
			array(
				'or',
				'id < 100',
				'id > 200'
			)
		),
		"LIMIT 100"
	);
```

##### Result Query

```mysql
	UPDATE
		`your-db-name`.`users`
	SET
		`your-db-name`.`users`.`type` = "admin",
		`your-db-name`.`users`.`name` = ( CONCAT(`name`,'+updated') )
	WHERE
		`your-db-name`.`users`.`type`	= "client"
		AND
		`your-db-name`.`users`.`name` NOT LIKE 0x4d61726b25
		AND (
			`your-db-name`.`users`.`id` < 100
			OR
			`your-db-name`.`users`.`id` > 200
		)
	LIMIT 100
```

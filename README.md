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

> ```php
> 	__db()->update(
> 		'users',
> 		array(
> 			'type'	=> "admin",
> 			'name'	=> "( CONCAT(`name`,'+updated') )"
> 		),
> 		array(
> 			'and',	// optional row
> 			'type'	=> "client",
> 			'name NOT LIKE "Mark%"',
> 			array(
> 				'or',
> 				'id < 100',
> 				'id > 200'
> 			)
> 		),
> 		"LIMIT 100"
> 	);
> ```
> 
> ##### Result Query
> 
> ```mysql
> 	UPDATE
> 		`your-db-name`.`users`
> 	SET
> 		`your-db-name`.`users`.`type` = "admin",
> 		`your-db-name`.`users`.`name` = ( CONCAT(`name`,'+updated') )
> 	WHERE
> 		`your-db-name`.`users`.`type`	= "client"
> 		AND
> 		`your-db-name`.`users`.`name` NOT LIKE 0x4d61726b25
> 		AND (
> 			`your-db-name`.`users`.`id` < 100
> 			OR
> 			`your-db-name`.`users`.`id` > 200
> 		)
> 	LIMIT 100
> ```
=============================
Javascript-Date-Format-Object
=============================

Date and Time - Formatting a date or time in JavaScript

Examples of Use
---------------

### Initialization

```html
	<script src="m_date.js" type="text/Javascript" charset="utf-8" ></script>
	<script type="text/javascript">
		// we initialize an object that use current time ( time when object m_date was created )
		var date_format	= m_date();

		document.write("<pre>");

		document.write("\n	Date: HH:mm:ss » "+m_date().format("H:i:s"));

		document.write("\n	Date: YYYY:MM:dd » "+m_date().format("Y:m:d"));

		document.write("</pre>");
	</script>
```

### Using always the current time or changing IT

```js
	// initialization of object
	var date_format	= m_date();

	// renew current time in object
	m_date().setTime();
	// alert the current time
	alert(m_date.format("Y:m:d H:i:s"));

	// add a specific time **May 13 2012 13:45:20**
	m_date().setTime('2012 5 13 13:45:20');
	// alert updated time
	alert(m_date.format("Y:m:d H:i:s"));

	// a shorter mode to write
	alert(m_date.setTime('2012 5 13 13:45:20').format("Y:m:d H:i:s"));
```

### Relative time Changing

```js
	// initialization of object
	var date_format	= m_date();

	// renew current time in object
	m_date().setTime();
	// alert the current time
	alert(m_date.format("Y:m:d H:i:s"));

	// add + 60 seconds
	m_date().setTime(60*1000);
	alert(m_date.format("Y:m:d H:i:s"));

	// now reducing + 120 seconds
	m_date().setTime(-120*1000);
	alert(m_date.format("Y:m:d H:i:s"));
```

### Escaping chars

```js
	// initialization of object
	var date_format	= m_date();

	// renew current time in object
	m_date().setTime();
	// alert the current time but,
	//	add a char "T" at the beginning of response
	alert(m_date.format("\\T Y:m:d H:i:s"));

```

### Changing Months Names

```js
	// initialization of object
	var date_format	= m_date();
	// change months names
	date_format.months	= [
		'Ianuarie','Februarie','Martie','Aprilie','Mai','Iunie',
		'Iulie','August','Septembrie','Octombrie','Noiembrie','Decembrie'];
	// alert the current month in short and long form
	alert(m_date.format("M F"));

	// change days names
	date_format.days	= [
		'Duminică','Luni ','Marti','Miercuri','Joi','Vineri','Sambata'];
	// alert the current month in short and long form
	alert(m_date.format("l D"));

```

Additional Configurations for Date Formating
--------------------------------------------

### Format

`The format of the outputted date string. See the formatting options below. There are also several predefined date constants that may be used instead, so for example DATE_RSS contains the format string 'D, d M Y H:i:s'.`

``The following characters are recognized in the format parameter string format character``
+ Day
	* **d** -	Day of the month, 2 digits with leading zeros 	01 to 31
	* **D** -	A textual representation of a day, three letters 	Mon through Sun
	* **j** -	Day of the month without leading zeros 	1 to 31
	* **l** -	(lowercase 'L') 	A full textual representation of the day of the week 	Sunday through Saturday
	* **N** -	ISO-8601 numeric representation of the day of the week;	1 (for Monday) through 7 (for Sunday)
	* **S** -	English ordinal suffix for the day of the month, 2 characters 	st, nd, rd or th. Works well with j
	* **w** -	Numeric representation of the day of the week 	0 (for Sunday) through 6 (for Saturday)
	* **z** -	The day of the year (starting from 0) 	0 through 365 
+ Week 	--- 	---
	* **W** -	ISO-8601 week number of year, weeks starting on Monday; 	Example: 42 (the 42nd week in the year)
+ Month
	* **F** -	A full textual representation of a month, such as January or March 	January through December
	* **m** -	Numeric representation of a month, with leading zeros 	01 through 12
	* **M** -	A short textual representation of a month, three letters 	Jan through Dec
	* **n** -	Numeric representation of a month, without leading zeros 	1 through 12
	* **t** -	Number of days in the given month 	28 through 31
+ Year
	* **L** -	Whether it's a leap year 	1 if it is a leap year, 0 otherwise.
	* **Y** -	A full numeric representation of a year, 4 digits 	Examples: 1999 or 2003
	* **y** -	A two digit representation of a year 	Examples: 99 or 03
+ Time
	* **a** -	Lowercase Ante meridiem and Post meridiem 	am or pm
	* **A** -	Uppercase Ante meridiem and Post meridiem 	AM or PM
	* **B** -	Swatch Internet time 	000 through 999
	* **g** -	12-hour format of an hour without leading zeros 	1 through 12
	* **G** -	24-hour format of an hour without leading zeros 	0 through 23
	* **h** -	12-hour format of an hour with leading zeros 	01 through 12
	* **H** -	24-hour format of an hour with leading zeros 	00 through 23
	* **i** -	Minutes with leading zeros 	00 to 59
	* **s** -	Seconds, with leading zeros 	00 through 59
	* **u** -	Microseconds
+ Timezone
	* **O** -	Difference to Greenwich time (GMT) in hours 	Example: +0200
	* **P** -	Difference to Greenwich time (GMT) with colon between hours and minutes; 	Example: +02:00
	* **Z** -	Timezone offset in seconds. The offset for timezones west of UTC is always negative, and for those east of UTC is always positive. 	-43200 through 50400
+ Full Date/Time
	* **c** -	ISO 8601 date; 	2004-02-12T15:19:21+00:00
	* **r** -	» RFC 2822 formatted date 	Example: Thu, 21 Dec 2000 16:01:07 +0200
	* **U** -	Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)

Unrecognized characters in the format string will be printed as-is.




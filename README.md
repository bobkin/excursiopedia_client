# What #
Excursiopedia.com demo client.
Client can download information via Excursiopedia XML-API and show some usage samples.  



# Requirement #
PHP 5.2+ 
PDO Extension (SQLite or MySQL)
MySQL or SQLite database 



# Installation #

Download all files or make “git clone”
Edit include/ep.start.php
	Add user name constant
	Add api_key constant
	Setup DB connection
	Check that “db” directory writable (if use SQLite) 

Start upload script
	php upload/script.php

View and run samples at /samples/ directory.


# Work with EP data #
 
EP class extend easy ORM ReadBean (http://redbeanphp.com). It can help write easy output code like this:

```php
<?php require(dirname(__FILE__).'/../include/ep.start.php');?>

<?php $city = EP::findOne('city','slug = ? ',array('praha'));?>
    
<?php if($city):?>
   <h1><?php echo $city->country->name?> / <?php echo $city->name?></h1>
   
   <ul> 
       <?php foreach($city->ownActivity as $activity):?>
           <li><a href="activity.php?id=<?php echo $activity->id?>"><?php echo $activity->title?></a>
       <?php endforeach;?>
   </ul>
<?php endif?>
```
   

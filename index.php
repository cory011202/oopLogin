<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cory
 * Date: 1/15/13
 * Time: 9:05 PM
 * To change this template use File | Settings | File Templates.
 */

require_once("classes/database.php");
require_once("classes/user.php");
require_once("lib/functions.php");

$connect = new dataBase();

$connect->connect();
/*$insertUsersQuery = mysql_query("INSERT INTO users (id,userName,password,emailAddr,isActive)
                           VALUES (NULL,'cory011202','cory011202@gmail.com',0)");
$insertUsersQuery = mysql_query("INSERT INTO users (id,userName,password,emailAddr,isActive)
                           VALUES (NULL,'cory011202','cory011202@gmail.com',0)");*/
$newUser = new user();
$newUser->username = 'Cory011202';
$newUser->password = sha1('testpw');
$newUser->emailAddr = 'Cory011202@gmail.com';
$newUser->save();
$newUser->setInactive();
$updateUser = User::getByUsername('cory011202');
echo $updateUser->password;
$updateUser->save();


?>
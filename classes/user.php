<?php
  class User{
      private $uid; // user userId
      private $fields; // other record fields 
      // initialize a User object
      public function __construct(){
          $this-> uid = null;
          $this-> fields = array('username' => "",
          'password' => "",
          'emailAddr' => "",
          'isActive' => false);
          }
          // override magic method to retrieve properties
          public function __get($field){
              if ($field == 'userId'){
                  return $this-> uid;
              }else{
                  return $this-> fields[$field];
              }
          }
          // override magic method to set properties
          public function __set($field, $value){
              if (array_key_exists($field, $this-> fields)){
                  $this-> fields[$field] = $value;
              }
          }
          // return if username is valid format
          public static function validateUsername($username){
              //return preg_match(�/^[A-Z0-9]{2,20}$/i�, $username);
          }
          // return if email address is valid format
          public static function validateEmailAddr($email){
              return filter_var($email, FILTER_VALIDATE_EMAIL);
          }
          // return an object populated based on the record�s user userId
          public static function getById($user_id){
              $user = new User();
              $query = sprintf('SELECT userName, password, emailAddr, isActive ' .
              'FROM %sUSER WHERE userId = %d',
              DB_TBL_PREFIX,
              $user_id);
              $result = mysql_query($query, $GLOBALS['DB']);
              if (mysql_num_rows($result)){
                  $row = mysql_fetch_assoc($result);
                  $user-> username = $row['userName'];
                  $user-> password = $row['password'];
                  $user-> emailAddr = $row['emailAddr'];
                  $user-> isActive = $row['isActive'];
                  $user-> uid = $user_id;
              }
              mysql_free_result($result);
              return $user;
          }
          // return an object populated based on the record�s username
          public static function getByUsername($username){
              $user = new User();
              $query = sprintf('SELECT userId, password, emailAddr, isActive ' .
              'FROM %sUSER WHERE userName = �%s�',
              DB_TBL_PREFIX,
              mysql_real_escape_string($username, $GLOBALS['DB']));
              $result = mysql_query($query, $GLOBALS['DB']);
              if (mysql_num_rows($result)){
                  $row = mysql_fetch_assoc($result);
                  $user-> username = $username;
                  $user-> password = $row['password'];
                  $user-> emailAddr = $row['emailAddr'];
                  $user-> isActive = $row['isActive'];
                  $user-> uid = $row['userId'];
              }
              mysql_free_result($result);
              return $user;
          }
          // save the record to the database
          public function save(){
              if ($this-> uid){
                  $query = sprintf('UPDATE %sUSER SET userName = �%s�, ' .
                  'password = �%s�, emailAddr = �%s�, isActive = %d ' .
                  'WHERE userId = %d',
                  DB_TBL_PREFIX,
                  mysql_real_escape_string($this-> username, $GLOBALS['DB']),
                  mysql_real_escape_string($this-> password, $GLOBALS['DB']),
                  mysql_real_escape_string($this-> emailAddr, $GLOBALS['DB']),
                  $this-> isActive,
                  $this-> userId);
                  mysql_query($query, $GLOBALS['DB']);
              }else{
/*                  $query = sprintf('INSERT INTO %sUSER (userName, password, ' .
                  'emailAddr, isActive) VALUES (�%s�, �%s�, �%s�, %d)',
                  DB_TBL_PREFIX,
                  mysql_real_escape_string($this-> username, $GLOBALS['DB']),
                  mysql_real_escape_string($this-> password, $GLOBALS['DB']),
                  mysql_real_escape_string($this-> emailAddr, $GLOBALS['DB']),
                  $this-> isActive);*/
                  $query=mysql_query("INSERT INTO users (userName, password,emailAddr, isActive) VALUES ('$this->username',
                   '$this->password', '$this->emailAddr', '$this->isActive')");
                   

                  if ($query)
                  {
                      $this-> uid = mysql_insert_id($GLOBALS['DB']);
                      return true;
                  }else{
                      return false;
                  }
              }
          }
          // set the record as inactive and return an activation token
          public function setInactive(){
              $this-> isActive = false;
              $this-> save(); // make sure the record is saved
              $token = random_text(5);
              $query = sprintf('INSERT INTO %spending (userId, TOKEN) ' .
              'VALUES (%d, �%s�)',
              DB_TBL_PREFIX,
              $this-> uid, $token);
              return (mysql_query($query, $GLOBALS['DB'])) ? $token : false;
          }
          // clear the user�s pending status and set the record as active
          public function setActive($token){
              $query = sprintf('SELECT TOKEN FROM %spending WHERE userId = %d ' .
              'AND TOKEN = �%s�',
              DB_TBL_PREFIX,
              $this-> uid,
              mysql_real_escape_string($token, $GLOBALS['DB']));
              $result = mysql_query($query, $GLOBALS['DB']);
              if (!mysql_num_rows($result)){
                  mysql_free_result($result);
                  return false;
              }else{
                  mysql_free_result($result);
                  $query = sprintf('DELETE FROM %spending WHERE userId = %d ' .
                  'AND TOKEN = �%s�',
                  DB_TBL_PREFIX,
                  $this-> uid,
                  mysql_real_escape_string($token, $GLOBALS['DB']));
                  if (!mysql_query($query, $GLOBALS['DB'])){
                      return false;
                  }else{
                      $this-> isActive = true;
                      return $this-> save();
                  }
              }
          }
  }
?>

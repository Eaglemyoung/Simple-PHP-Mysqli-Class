<?php
/**
 * Myslq Class by Eagle
 * History:2017-04-22
 * Version:0.2
 */
class mysqlClass
{
  //Mysql base variables
  public $dbMassage;  // Show mysql query status

  private $host;    // Mysql host
  private $uname;   // MySQL username
  private $passwd;  // MySQL password
  private $dbname;  // MySQL database
  private $port;    // MySQL database's port
  private $charset; // MySQL charset

  protected $databaseLink;

  // Class construct
  function __construct($uname,$passwd,$host='localhost',$dbname="",$port=3306,$charset='utf8'){
    $this->host    = $host;
    $this->port    = $port;
    $this->uname   = $uname;
    $this->passwd  = $passwd;
    $this->dbname  = $dbname;
    $this->charset = $charset;

    $this->Connect();
  }

  // Class destructor

  function __destruct(){
    $this->closeConnection();
  }


  private function Connect(){
    if ($this->dbname == '') {
      $this->databaseLink = new mysqli($this->host,$this->uname, $this->passwd,$this->dbname,$this->port);
      $this->databaseLink->set_charset($this->charset);
      $this->connectError();
    } else{
      $this->databaseLink = new mysqli($this->host, $this->uname,$this->passwd,$this->dbname,$this->port);
      $this->databaseLink->set_charset($this->charset);
      $this->connectError();
    }
  }

  private function connectError(){
    if ($this->databaseLink->connect_errno) {
      $this->dbMassage = 'Could not connect to server: '.$this->databaseLink->connect_error;
    }
  }

  private function closeConnection(){
    $this->databaseLink->close();
  }

  // mysql_real_escape_string value
  private function escapeValue($value){
    if(is_array($value)){
        $i = 0;
      foreach($value as $key=>$val){
        if(!is_array($value[$key])){
          $data[$key] = $this->databaseLink->real_escape_string($data[$key]);
          $i++;
          }
      }
    }else{
      $data = $this->databaseLink->real_escape_string($data);
    }
    return $value;
  }

  private function arrayVale($arr){
    if(gettype($arr) == "array"){
      foreach ($arr as &$val) {
          $vals .= $val.",";
      }
      $vals = substr($vals,0,strlen($vals)-1);
      return $vals;
    }else {
      return $arr;
    }
  }

  private function arrayDotVale($arr){
    if(gettype($arr) == "array"){
      foreach ($arr as &$val) {
          $vals .= "'".$val."',";
      }
      $vals = substr($vals,0,strlen($vals)-1);
      return $vals;
    }else {
      return $arr;
    }
  }

  private function arrayEqualVale($arrClos,$arrVal){
    if(gettype($arrClos) == "array" AND gettype($arrVal) == "array"){
      $arrays = array_combine($arrClos,$arrVal);
      foreach ($arrays as $key => $value) {
        $vals .= $key.'="'.$value.'",';
      }
      $vals = substr($vals,0,strlen($vals)-1);
      return $vals;
    }else {
      $vals = $arrClos.'="'.$arrVal.'"';
      return $vals;
    }
  }

  public function select($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT ".$cols;
      return $this;
  }

  public function insert($tableName,$tableClos,$tableVal){
      $tableName = $this->escapeValue($tableName);
      $tableClos = $this->escapeValue($tableClos);
      $tableVal  = $this->escapeValue($tableVal);
      $tableClos = $this->arrayVale($tableClos);
      $tableVal  = $this->arrayDotVale($tableVal);
      $this->sql .= "INSERT INTO ".$tableName." (".$tableClos.") VALUES (".$tableVal.")";
      return $this;
  }

  public function update($tableName,$tableClos,$tableVal){
      $tableName = $this->escapeValue($tableName);
      $tableClos = $this->escapeValue($tableClos);
      $tableVal  = $this->escapeValue($tableVal);
      $tableVals = $this->arrayEqualVale($tableClos,$tableVal);
      $this->sql .= "UPDATE ".$tableName." SET ".$tableVals;
      return $this;
  }
  public function delete($tableName)
  {
    $tableName = $this->escapeValue($tableName);
    $this->sql .= "DELETE FROM ".$tableName;
    return $this;
  }

  public function from($tableName){
      $tableName = $this->escapeValue($tableName);
      $this->sql .= " FROM ".$tableName;
      return $this;
  }


  public function where($whereVal)
  {
    $whereVal = $this->escapeValue($whereVal);
    $this->sql .= " WHERE ".$whereVal;
    return $this;
  }

  public function limit($limitVal)
  {
    $limitVal = $this->escapeValue($limitVal);
    $this->sql .= " LIMIT ".$limitVal;
    return $this;
  }

  public function orderBy($orderByVal,$upDown)
  {
    $orderByVal = $this->escapeValue($orderByVal);
    $upDown = $this->escapeValue($upDown);
    if ($upDown == NULL OR $upDown == "up" ) {
        $this->sql .= " ORDER BY ".$orderByVal." ASC";
        return $this;
    }
    if ($upDown == "down") {
        $this->sql .= " ORDER BY ".$orderByVal." DESC";
        return $this;
    }
  }

  public function groupBy($groupByVal,$upDown)
  {
    $groupByVal = $this->escapeValue($groupByVal);
    $upDown = $this->escapeValue($upDown);
    if ($upDown == NULL OR $upDown == "up" ) {
        $this->sql .= " GROUP BY ".$orderByVal." ASC";
        return $this;
    }
    if ($upDown == "down") {
        $this->sql .= " ORDER BY ".$orderByVal." DESC";
        return $this;
    }
  }

  public function having($havingVal)
  {
    $havingVal = $this->escapeValue($havingVal);
    $this->sql .= " HAVING ".$havingVal;
    return $this;
  }

  public function min($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT MIN(".$cols.")";
      return $this;
  }

  public function max($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT MAX(".$cols.")";
      return $this;
  }

  public function count($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT COUNT(".$cols.")";
      return $this;
  }

  public function avg($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT AVG(".$cols.")";
      return $this;
  }

  public function sum($cols){
      $cols = $this->escapeValue($cols);
      $this->sql .= "SELECT SUM(".$cols.")";
      return $this;
  }

  public function innerJoin($tableVal,$clos){
    $tableVal = $this->escapeValue($tableVal);
    $cols = $this->escapeValue($clos);
    $this->sql .= " INNER JOIN ".$tableVal." ON ".$cols;
    return $this;
  }

  public function leftJoin($tableVal,$clos){
    $tableVal = $this->escapeValue($tableVal);
    $cols = $this->escapeValue($clos);
    $this->sql .= " LEFT JOIN ".$tableVal." ON ".$cols;
    return $this;
  }

  public function rightJoin($tableVal,$clos){
    $tableVal = $this->escapeValue($tableVal);
    $cols = $this->escapeValue($clos);
    $this->sql .= " RIGHT JOIN ".$tableVal." ON ".$cols;
    return $this;
  }

  public function fullOuterJoin($tableVal,$clos){
    $tableVal = $this->escapeValue($tableVal);
    $cols = $this->escapeValue($clos);
    $this->sql .= " FULL OUTER JOIN ".$tableVal." ON ".$cols;
    return $this;
  }



  public function createDatabase($dataBaseName){
    $dataBaseName = $this->escapeValue($dataBaseName);
    $this->sql .= "CREATE DATABASE ".$dataBaseName;
    return $this;
  }

  public function dropDatabase($dataBaseName){
    $dataBaseName = $this->escapeValue($dataBaseName);
    $this->sql .= "DROP DATABASE ".$dataBaseName;
    return $this;
  }

  public function createTable($tableName,$cols,$engine = "InnoDB"){
    $tableVal = $this->escapeValue($tableName);
    $cols = $this->escapeValue($clos);
    $this->sql .= "CREATE TABLE ".$dataBaseName."(".$clos.") ENGINE = ".$engine;
    return $this;
  }

  public function dropTable($tableName){
    $tableName = $this->escapeValue($tableName);
    $this->sql .= "DROP TABLE ".$tableName;
    return $this;
  }

  public function alterTable($tableName){
    $tableName = $this->escapeValue($tableName);
    $this->sql .= "ALTER TABLE ".$tableName;
    return $this;
  }

  public function add($clos){
    $clos = $this->escapeValue($clos);
    $this->sql .= " ADD ".$clos;
    return $this;
  }

  public function drop($clos){
    $clos = $this->escapeValue($clos);
    $this->sql .= " DROP COLUMN ".$clos;
    return $this;
  }

  public function modifyCloumn($clos){
    $clos = $this->escapeValue($clos);
    $this->sql .= " MODIFY COLUMN ".$clos;
    return $this;
  }

  public function begin()
  {
    $this->sql = "BEGIN";
    return $this;
  }

  public function rollBack()
  {

    $this->sql = "ROLLBACK";
    return $this;
  }

  public function commit()
  {

    $this->sql = "COMMIT";
    return $this;
  }

  public function selectDB($databaseName){
    $databaseName = $this->escapeValue($databaseName);
    if(!$this->databaseLink->select_db($databaseName)){
      $this->dbMassage = 'Cannot select database: ' .$this->databaseLink->error;
    }
  }

  public function getResult()
  {
      echo $this->sql.'<br/>';
      if($result = $this->databaseLink->query($this->sql)){
         return $result;
         $result->free();
         $this->dbMassage = "Successfully";
      }else {
         $this->dbMassage = $this->databaseLink->error;
      }

  }
}
?>

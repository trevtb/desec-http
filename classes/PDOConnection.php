<?php

##############################
#Example Select
#	$return = false;
#
#		$dbc = cDBConnection::getPdoConnection();
#
#		$sql1 = 'SELECT count(*) as vorhanden FROM doc WHERE doc_orginal_url_txt = :doc_orginal_url_txt';
#		$stmt1 = $dbc->prepare($sql1);
#		$stmt1->bindParam(':doc_orginal_url_txt', $doc_orginal_url_txt);
#		$stmt1->execute();
#		$data1 = $stmt1->fetch(PDO::FETCH_OBJ);
#		if ($data1->vorhanden != 0)
#			$return = true;#
#
#		return $return;
##############################
#Example Insert
#	$dbc = cDBConnection::getPdoConnection();
#
#		$sql2 = '
#			INSERT INTO doc ( doc_cmp_id, doc_dot_id, doc_orginal_url_txt, doc_orginal_url_htm, doc_date, doc_tstamp_accepted, doc_period_date, doc_date_changed ) 
#			VALUES ( :doc_cmp_id, :doc_dot_id, :doc_orginal_url_txt, :doc_orginal_url_htm, :doc_date, :doc_tstamp_accepted, :doc_period_date, :doc_date_changed )';
#
#		$stmt2 = $dbc->prepare($sql2);
#		$stmt2->bindParam(':doc_cmp_id', $cmp_id);
#		$stmt2->bindParam(':doc_dot_id', $dot_id);
#		$stmt2->bindParam(':doc_orginal_url_txt', $doc_orginal_url_txt);
#		$stmt2->bindParam(':doc_orginal_url_htm', $doc_orginal_url_htm);
#		$stmt2->bindParam(':doc_date', $doc_date);
#		$stmt2->bindParam(':doc_tstamp_accepted', $doc_tstamp_accepted);
#		$stmt2->bindParam(':doc_period_date', $doc_period_date);
#		$stmt2->bindParam(':doc_date_changed', $doc_date_changed);
#		$stmt2->execute();
##############################

class PDOConnection extends pdo {

  private static $DB_CONNECTION = null;
	
	private static final function getNewPdoConnection() {
		self::$DB_CONNECTION = new PDO('mysql:host='.Config::$DBHOST.';dbname='.Config::$DBNAME, Config::$DBUSER, Config::$DBPASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		self::$DB_CONNECTION->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    	return self::$DB_CONNECTION;
	} //endfunction getNewPdoConnection
  
  public static final function getPdoConnection() {
		try {
			return (is_null(self::$DB_CONNECTION)) ? self::getNewPdoConnection() : self::$DB_CONNECTION;
		} catch (Exception $e) {
			echo $e->getTraceAsString();
		} //endtry
  } //endfunction getPdoConnection
}
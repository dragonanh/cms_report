<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of myPDOSessionStorage
 *
 * @author vas_huynq28
 */
class myPDOSessionStorage extends sfPDOSessionStorage {
  //put your code here

  /**
   * Extending session write function
   *
   * @param string $id Session identifier  
   * @param mixed $data Session's data
   */
  public function sessionWrite($id, $data) {
    $myContext = sfContext::getInstance();
    $myContext->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(sprintf('Write session module name "%s" | action name "%s" | data "%s" | id "%s"', $myContext->getModuleName(), $myContext->getActionName(), $data, $id))));

    // get table/column
    $db_table = $this->options['db_table'];
    $db_data_col = $this->options['db_data_col'];
    $db_id_col = $this->options['db_id_col'];
    $db_time_col = $this->options['db_time_col'];

    //get userid
    $user = sfContext::getInstance()->getUser();
    //ngoctv: them vong if de fix loi session time out
    if ($user!=null){
        $userid = ($user->isAuthenticated()) ? $user->getGuardUser()->getId() : '0';
    }else{$userid = '0';}
    
    //remove old session
    if ($userid != '0') {
      $deletesql = 'DELETE FROM ' . $db_table . ' WHERE sess_userid = ? AND ' . $db_id_col . ' != ?';

      try {
        $deletestmt = $this->db->prepare($deletesql);
        $deletestmt->bindParam(1, $userid, PDO::PARAM_INT);
        $deletestmt->bindParam(2, $id, PDO::PARAM_STR);
        $deletestmt->execute();
      } catch (PDOException $e) {
        throw new sfDatabaseException(sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage()));
      }
    }
    //update session
    $sql = 'UPDATE ' . $db_table . ' SET ' . $db_data_col . ' = ?, ' . $db_time_col . ' = ' . time() . ', sess_userid = ? WHERE ' . $db_id_col . '= ?';

    try {
      $stmt = $this->db->prepare($sql);
      $stmt->bindParam(1, $data, PDO::PARAM_STR);
      $stmt->bindParam(2, $userid, PDO::PARAM_INT);
      $stmt->bindParam(3, $id, PDO::PARAM_STR);
      $stmt->execute();
    } catch (PDOException $e) {
      throw new sfDatabaseException(sprintf('PDOException was thrown when trying to manipulate session data. Message: %s', $e->getMessage()));
    }

    return true;
  }

}

?>

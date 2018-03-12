<?php
/* this file is the workhorse which does the create, edit or delete of the object, property or relationship */

include ('db.php');
include ('config.php');

$success = false;
$fail = '{ "result":"false", "reason":"defaultReason" }';

if (isset($_POST['do'])) {
    $do = $_POST['do'];
    if (isset($_POST['oid'])) {$oid=$_POST['oid'];}
    if (isset($_POST['pid'])) {$pid=$_POST['pid'];}
    if (isset($_POST['rid'])) {$rid=$_POST['rid'];}

    if ($do=='newobject' and isset($_POST['name']) and isset($_POST['type'])){
        // check to make sure this does not already exist
        $sql = "SELECT oid FROM objects WHERE name='".$_POST['name']."' and type = '".$_POST['type']."';";
        $existing = get_single_from_sql($sql);
        if ($existing === 0) {
            // add to database
            $sql = "INSERT INTO objects (name,type) VALUES ('".$_POST['name']."','".$_POST['type']."');";
            $oid = new_data ($sql);
            echo '{ "result":"true", "object": {"oid":"'.$oid.'"} }';
        } else {
            $failReason = "Matching object name and type found.";
            $fail = preg_replace('defaultReason', $failReason, $fail);
            echo $fail;
        } //close if existing
    } // close newobject
        
    else if ($do=='editobject' and isset($_POST['name']) and isset($oid)) {
        // update database
        $sql = "UPDATE objects SET name='".$_POST['name']."' WHERE oid='".$oid."';";
        $result = update_data ($sql);
        if ($result){
            echo '{ "result":"'.$result.'", "object": {"oid":"'.$oid.'", "name":"'.$_POST['name'].'"} }';
        } else {
            echo '{ "result":"'.$result.'" }';
        }
    }
    
    else if ($do=='deleteobject' and isset($oid)) {
        // get all relationship ids (rid)
        $sql = "select rid from relationships where poid=".$oid." or coid=".$oid;
        $rid_array = get_array_from_sql($sql);
        $rid_csv =  "0,";
        foreach ($rid_array as $item){
            $rid_csv = $rid_csv.$item['rid'].',';
        }
        $rid_csv = rtrim($rid_csv,",");
        
        // delete all related relationships
        $sql = "DELETE FROM relationships WHERE poid=".$oid." or coid=".$oid;
        $result1 = update_data ($sql);
        
        // delete all related object properties
        $sql = "DELETE FROM properties WHERE (idType='o' and id=".$oid.") or (idType='r' and id in (".$rid_csv."))";
        $result2 = update_data ($sql);

        // delete the object
        $sql = "DELETE FROM objects WHERE oid=".$oid;
        $result3 = update_data ($sql);

        if ($result1 and $result2 and $result3){
            $redirect_url = "index.php";
            echo "<a href='".$redirect_url."'>object deleted</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem deleting the object</a>";
        }
    }
    

    else if ($do=='newobjectproperty' and isset($_POST['name']) and isset($_POST['value']) and isset($oid)) {
        // update database
        
// TODO modify query so the appropriate field Text vs. date vs. number gets saved with the value
        
        $sql = "INSERT INTO properties (id,name,valueText,idType) VALUES (".$oid.",'".$_POST['name']."','".$_POST['value']."','o');";
        $result = update_data ($sql);
        if ($result){
            echo "<a href='".$redirect_url."'>Property Created</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem creating the Property</a>";
        }
    } // close newobjectproperty
    
    else if ($do=='editobjectproperty' and isset($_POST['value']) and isset($oid) and isset($pid) and isset($_POST['type'])) {
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            // update database
            switch ($_POST['type']) {
                case 'text': $valueField = 'valueText'; break;
                case 'date': $valueField = 'valueDate'; break;
                case 'number': $valueField = 'valueNumber'; break;
            }
            $sql = "UPDATE properties SET ".$valueField."='".$_POST['value']."' WHERE pid=".$pid.";";
            $result = update_data ($sql);
        } //end submit
        else if (isset($_POST['submit']) and $_POST['submit']=='delete'){
            $sql = "DELETE FROM properties WHERE pid=".$pid.";";
            $result = update_data ($sql);
        } //end if delete
        else {
            $result=false;
        }
        if ($result){
            echo "<a href='".$redirect_url."'>Property Updated</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem updating the object</a>";
        }
    } // close editobjectproperty

    
    else {
        echo "No action taken";
    }  // close no action taken error
    
}  // close if is set do
?>

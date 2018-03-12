<?php
/* this file is the workhorse which does the create, edit or delete of the object, property or relationship */

include ('header.php');
include ('db.php');

$success = false;
// define variable which inserts text to redirect back to appropriate page
$redirect1 = "<br>Select the link above or you will be redirected in 1 seconds.<script>window.setTimeout(function(){window.location.href = '";
$redirect2 = "';}, 1000);</script>";

if (isset($_POST['do'])) {
    $do = $_POST['do'];
    if (isset($_POST['oid'])) {$oid=$_POST['oid'];}
    if (isset($_POST['pid'])) {$pid=$_POST['pid'];}
    if (isset($_POST['rid'])) {$rid=$_POST['rid'];}
    $redirect_url = "relationship.php?rid=".$rid;

    if ($do=='newrelationship' and isset($_POST['type']) and isset($_POST['pri_oid']) and isset($_POST['sec_oid'])){
        $type = $_POST['type'];
        $split = explode(',', $type,2);
        $role = $split[0]; //child or parent;
        $did = $split[1]; //did;
        if ($role = 'c') {
            $coid = $_POST['pri_oid'];
            $poid = $_POST['sec_oid'];
        } else if ($role = 'p') {
            $poid = $_POST['pri_oid'];
            $coid = $_POST['sec_oid'];
        } else {
            echo "Incorrect Parameters Passed";
        }
        $sql = "SELECT relationshipParentDesc, relationshipChildDesc FROM definitions WHERE did=".$did.";";
        $descriptions = get_single_from_sql($sql);
        $pdescription = $descriptions[relationshipParentDesc];
        $cdescription = $descriptions[relationshipChildDesc];
        if (checkDuplicateRelationship($poid, $coid, $rid)) {
            // add to database
            $sql = "INSERT INTO relationships (poid, parentDesc, coid, childDesc) VALUES (".$poid.",'".$pdescription."',".$coid.",'".$cdescription."');";
            $rid = new_data ($sql);
            $redirect_url = "relationship.php?rid=".$rid;
            echo "<a href='".$redirect_url."'>new relationship added with RID of ". $rid."</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "The specified objects are already directly related.";
        }
    } // close new relationship
        
    else if ($do=='editrelationship' and isset($_POST['type']) and isset($_POST['pri_oid']) and isset($_POST['sec_oid'])) {
        $redirect_url = "relationship.php?rid=".$rid;
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            $type = $_POST['type'];
            $split = explode(',', $type,2);
            $role = $split[0]; //child or parent;
            $did = $split[1]; //did;
            $poid = $_POST['pri_oid'];
            $coid = $_POST['sec_oid'];
            $sql = "SELECT relationshipParentDesc, relationshipChildDesc FROM definitions WHERE did=".$did.";";
            $descriptions = get_single_from_sql($sql);
            if ($role === 'p') {
                $pdescription = $descriptions[relationshipParentDesc];
                $cdescription = $descriptions[relationshipChildDesc];
            } else {
                $cdescription = $descriptions[relationshipParentDesc];
                $pdescription = $descriptions[relationshipChildDesc];
            }
            if (checkDuplicateRelationship($poid, $coid, $rid)) {
                // add to database
                $sql = "UPDATE relationships SET poid='".$poid."', parentDesc='".$pdescription."', coid='".$coid."', childDesc='".$cdescription."' WHERE rid='".$rid."';";
                $result = update_data ($sql);
                echo "<a href='".$redirect_url."'>Relationship Updated</a>";
            } else {
                echo "<a href='".$redirect_url."'>This relationshiop already exists or an object can be related to itself, no changes made.</a>";
            }
            echo $redirect1.$redirect_url.$redirect2;
        } else if (isset($_POST['submit']) and $_POST['submit']=='delete'){
            echo "<p>Are you sure you want to delete this object?  All of it's properties and relationships will be removed as well.</p>";
            echo "<div class='keep'><a href='".$redirect_url."'>Oops, I hit the delete button by mistake.</a></div>";
            echo "<div class='delete'><form action='relationship_crud.php' method='post'>";
            echo "<input type='hidden' name='do' value='deleterelationship'>";
            echo "<input type='hidden' name='rid' value='".$rid."'>";
            echo "<button value='submit' name='submit'>Yea, this relationship sucks, get rid of it and it's properties completely!</button></div>";
        } 
    }  // close editrelationship
    
    else if ($do=='deleterelationship' and isset($rid)) {
        // delete all related properties
        $sql = "DELETE FROM properties WHERE idType='r' and id=".$rid;
        $result1 = update_data ($sql);

        // delete the relationship
        $sql = "DELETE FROM relationships WHERE rid=".$rid;
        $result2 = update_data ($sql);

        if ($result1 and $result2){
            $redirect_url = "index.php";
            echo "<a href='".$redirect_url."'>relationship deleted</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem deleting the relationship</a>";
        }
    }

    else if ($do=='newrelationshipproperty' and isset($_POST['name']) and isset($_POST['value']) and isset($rid)) {
        // update database
        
// TODO modify query so the appropriate field Text vs. dt vs. number gets saved with the value
        
        $sql = "INSERT INTO properties (id,name,valueText,idType) VALUES (".$rid.",'".$_POST['name']."','".$_POST['value']."','r');";
        $result = update_data ($sql);
        if ($result){
            echo "<a href='".$redirect_url."'>Property Created</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem creating the Property</a>";
        }
    } // close newrelationshipproperty
    
    else if ($do=='editrelationshipproperty' and isset($_POST['value']) and isset($rid) and isset($pid) and isset($_POST['type'])) {
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            // update database
            switch ($_POST['type']) {
                case 'text': $valueField = 'valueText'; break;
                case 'dt': $valueField = 'valueDate'; break;
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
            echo "<a href='".$redirect_url."'>There was a problem updating the relationship</a>";
        }
    } // close editrelationshipproperty
    
    else {
        echo ("no 'Do' selected");
    } // close edit relationship
    
    
} else {
    echo ("'Do' not defined");
}  // close if is set do

function checkDuplicateRelationship ($poid, $coid, $rid) {
    $duplicate = true;
    // check to make sure it's not a self reference
    if ($poid === $coid) {
        $duplicate = false;
    }
    // check to make sure relationship does not already exist
    $sql = "SELECT rid FROM relationships WHERE ((poid=".$poid." and coid = ".$coid.") or (poid=".$coid." and coid = ".$poid.")) and rid != ".$rid.";";
    $existing = get_single_from_sql($sql);
    if ($existing > 0) {
        $duplicate = false;
    }
    
    return $duplicate;
}

include ('footer.php');
?>
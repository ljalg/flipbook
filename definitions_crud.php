<?php
/* this file is the workhorse which does the create, edit or delete of the object, property or relationship */

include ('header.php');
include ('db.php');
 
$success = false;
// define variable which inserts text to redirect back to appropriate page
$redirect1 = "<br>Select the link above or you will be redirected in 1 seconds.<script>window.setTimeout(function(){window.location.href = '";
$redirect2 = "';}, 1000);</script>";
$redirect_url = "definitions.php";

if (isset($_POST['do'])) {
    $do = $_POST['do'];
    if (isset($_POST['did'])) {$did=$_POST['did'];}

    if ($do=='newobjectdefinition' and isset($_POST['name']) and isset($_POST['type'])){
        // check to make sure this does not already exist
        $sql = "SELECT did FROM definitions WHERE name='".$_POST['name']."' and type='".$_POST['type']."' ;";
        $existing = get_single_from_sql($sql);
        if ($existing === 0) {
            // add to database
            $sql = "INSERT INTO definitions (name,type) VALUES ('".$_POST['name']."','".$_POST['type']."');";
            $did = new_data ($sql);
            echo "<a href='".$redirect_url."'>new definition added with DID of ". $did."</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "This Object or Relationship type already exists, please <a href='".$redirect_url."'>check again</a> to find it.";
        } //close if existing
    } // close newobject
        
    else if ($do=='editobjectdefinition' and isset($_POST['name']) and isset($did)) {
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            // update database
            $sql = "UPDATE definitions SET name='".$_POST['name']."' WHERE did='".$did."';";
            $result = update_data ($sql);
            if ($result){
                echo "<a href='".$redirect_url."'>Object Updated</a>";
                echo $redirect1.$redirect_url.$redirect2;
            } else {
                echo "<a href='".$redirect_url."'>There was a problem updating the object</a>";
            }
        } else if (isset($_POST['submit']) and $_POST['submit']=='delete'){
            echo "<p>Are you sure you want to delete this object type?  All of it's properties will be removed as well.</p>";
            echo "<div class='keep'><a href='".$redirect_url."'>Oops, I hit the delete button by mistake.</a></div>";
            echo "<div class='delete'><form action='definitions_crud.php' method='post'>";
            echo "<input type='hidden' name='do' value='deletebojectdefinition'>";
            echo "<input type='hidden' name='did' value='".$did."'>";
            echo "<button value='submit' name='submit'>Yea, this object type sucks, get rid of it & it's properties completely!</button></div>";
        } 
    } //close edit object
    
    else if ($do=='deletebojectdefinition' and isset($did)) {
        // find all related properties
        $sql = "select did from definitions where parent=".$did;
        $children_array = get_array_from_sql($sql);
        $children_csv = "0,";
        foreach ($children_array as $item){
            $children_csv = $children_csv.$item['did'].',';
        }
        $children_csv = rtrim($children_csv,",");
        $sql = "DELETE FROM definitions WHERE did in (".$children_csv.") or did=".$did;
        $result = update_data ($sql);
        if ($result){
            echo "<a href='".$redirect_url."'>object type deleted</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "<a href='".$redirect_url."'>There was a problem deleting the object type</a>";
        }
    }
    
    else if ($do=='newrelationshipdefinition' and isset($_POST['type']) and isset($_POST['pardesc']) and isset($_POST['childdesc'])) {
        // check to make sure this does not already exist
        $sql = "SELECT did FROM definitions WHERE relationshipParentDesc='".$_POST['pardesc']."' and relationshipChildDesc='".$_POST['childdesc']."' ;";
        $existing = get_single_from_sql($sql);
        if ($existing === 0) {
            // add to database
            $sql = "INSERT INTO definitions (type,relationshipParentDesc,relationshipChildDesc) VALUES ('".$_POST['type']."','".$_POST['pardesc']."','".$_POST['childdesc']."');";
            $did = new_data ($sql);
            echo "<a href='".$redirect_url."'>new relationship added with DID of ". $did."</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "This Object or Relationship type already exists, please <a href='".$redirect_url."'>check again</a> to find it.";
        } //close if existing
    }
    
    else if ($do=='editrelationshipdefinition' and isset($_POST['pardesc']) and isset($_POST['childdesc']) and $did) {
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            // update database
            $sql = "UPDATE definitions SET relationshipParentDesc='".$_POST['pardesc']."', relationshipChildDesc='".$_POST['childdesc']."'  WHERE did=".$did.";";
            $result = update_data ($sql);
            if ($result){
                echo "<a href='".$redirect_url."'>Object Updated</a>";
                echo $redirect1.$redirect_url.$redirect2;
            } else {
                echo "<a href='".$redirect_url."'>There was a problem updating the object</a>";
            }
        } else if (isset($_POST['submit']) and $_POST['submit']=='delete'){
            echo "<p>Are you sure you want to delete this relationship type?  All of it's properties will be removed as well.</p>";
            echo "<div class='keep'><a href='".$redirect_url."'>Oops, I hit the delete button by mistake.</a></div>";
            echo "<div class='delete'><form action='definitions_crud.php' method='post'>";
            echo "<input type='hidden' name='do' value='deleterelationshipdefinition'>";
            echo "<input type='hidden' name='did' value='".$did."'>";
            echo "<button value='submit' name='submit'>Yea, this relationship type sucks, get rid of it & it's properties completely!</button></div>";
        } 
    }
     
    else if ($do=='deleterelationshipdefinition' and isset($did)) {
            // find all related properties
            $sql = "select did from definitions where parent=".$did;
            $children_array = get_array_from_sql($sql);
            $children_csv = "0,";
            foreach ($children_array as $item){
                $children_csv = $children_csv.$item['did'].',';
            }
            $children_csv = rtrim($children_csv,",");
            $sql = "DELETE FROM definitions WHERE did in (".$children_csv.") or did=".$did;
            $result = update_data ($sql);
            if ($result){
                echo "<a href='".$redirect_url."'>relationship types deleted</a>";
                echo $redirect1.$redirect_url.$redirect2;
            } else {
                echo "<a href='".$redirect_url."'>There was a problem deleting the relationship type</a>";
            }
    }
    
    else if ($do=='newpropertydefinition' and isset($_POST['name']) and isset($_POST['datatype']) and isset($_POST['pdid']) and isset($_POST['type'])) {
        // check to make sure this does not already exist
        $sql = "SELECT did FROM definitions WHERE name='".$_POST['name']."' and parent='".$_POST['pdid']."' ;";
        $existing = get_single_from_sql($sql);
        if ($existing === 0) {
            // add to database
            $sql = "INSERT INTO definitions (type,name,dataType,parent) VALUES ('".$_POST['type']."','".$_POST['name']."','".$_POST['datatype']."',".$_POST['pdid'].");";
            $did = new_data ($sql);
            echo "<a href='".$redirect_url."'>new definition added with DID of ". $did."</a>";
            echo $redirect1.$redirect_url.$redirect2;
        } else {
            echo "This Property already exists for this parent, please <a href='".$redirect_url."'>check again</a> to find it.";
        } //close if existing
    } // close newobjectproperty
    
    else if ($do=='editpropertydefinition' and isset($_POST['name']) and isset($_POST['datatype']) and isset($did)) {
        if (isset($_POST['submit']) and $_POST['submit']=='submit') {
            // check to make sure this does not already exist
            $sql = "SELECT did FROM definitions WHERE name='".$_POST['name']."' and did !=".$did." and parent in (SELECT parent FROM definitions WHERE did=".$did.") ;";
            $existing = get_single_from_sql($sql);
            if ($existing === 0) {
                $sql = "UPDATE definitions SET name='".$_POST['name']."', datatype='".$_POST['datatype']."' WHERE did=".$did.";";
                $result = update_data ($sql);
            } else {$result=false;}
        } //end submit
        else if (isset($_POST['submit']) and $_POST['submit']=='delete'){
            $sql = "DELETE FROM definitions WHERE did=".$did.";";
            $result = update_data ($sql);
        } //end if delete
        else { $result=false; }
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

include ('footer.php');
?>
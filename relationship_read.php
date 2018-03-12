<?php
/* this file returns the json for relationships */

header("Content-Type: application/json; charset=UTF-8");

include ("config.php");
include ("db.php"); 

// confirm get variable do is set
if (isset($_GET['do'])) {


// done - return relationship info
    if( $_GET['do']=="relationshipinfo" and isset($_GET['rid'])){
        $rid = htmlspecialchars($_GET['rid']);
        $sql = "SELECT op.name as 'pname', op.type as 'ptype', op.oid as 'poid', r.parentDesc as 'pdesc', 
                    oc.name as 'cname', oc.type as 'ctype', oc.oid as 'coid', r.childDesc as 'cdesc'
                FROM relationships r join objects op ON r.poid = op.oid
	                join objects oc on r.coid=oc.oid
                WHERE r.rid=".$rid.";";
        $result = getJson ($sql);
        echo ($result);
        
// return relationship properties
    } else if( $_GET['do']=="properties" and isset($_GET['rid'])){
        $rid = htmlspecialchars($_GET['rid']);
        $sql = "SELECT pid as 'pid', name as 'pname', ifnull(valueText,ifnull(valueDate,ifnull(valueNumber,null))) as 'pvalue', 
                IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL ,  'dt', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS ptype
                FROM properties where ID=".$rid.
                " and idType = 'r' and name not in (".$reserved_object_properties.")";
        $result = getJson ($sql);
        echo ($result);
        
// return object relationships
    } else if( $_GET['do']=="relationships" and isset($_GET['oid'])){
        $oid = htmlspecialchars($_GET['oid']);
            // When this object is the parent - display parent description and parent object name
            // When this object is the child - display child description and child object name
            $sql = "SELECT r.rid as 'rid', r.childDesc as 'description', o.type as 'type', o.name as 'name', r.poid as 'reloid'
                FROM relationships r join objects o on r.poid=o.oid
                WHERE r.coid=".$oid."
                UNION
                SELECT r.rid as 'rid', r.parentDesc as 'description', o.type as 'type', o.name as 'name', r.coid as 'reloid'
                FROM relationships r join objects o on r.coid=o.oid
                WHERE r.poid=".$oid.";";
        $result = getJson ($sql);
        echo ($result);

//return list of unused properties for a specific relationship
    } else if( $_GET['do']=="emptyproperties" and isset($_GET['rid'])){
        $rid = htmlspecialchars($_GET['rid']);
        $sql = "SELECT name, dataType FROM definitions WHERE type = 'rp' and name not in (".$reserved_object_properties.") and 
            name not in (select name from properties where id=".$rid." and idType='r')
            and parent in (select d.did from definitions d join relationships r  
            where ((r.parentDesc=d.relationshipParentDesc and r.childDesc=d.relationshipChildDesc) or 
            (r.parentDesc=d.relationshipChildDesc and r.childDesc=d.relationshipParentDesc)) 
            and d.type='r' and r.rid=".$rid.");";
        $result = getJson ($sql);
        echo ($result);


// return list of relationship types
    } else if( $_GET['do']=="relationshipsdropdown"){
        $sql = "SELECT a.did, a.role, a.name, a.description from ( 
                SELECT did, 'p' as role, name, relationshipParentDesc AS description FROM `definitions` WHERE TYPE= 'r'
                union
                SELECT did, 'c' as role, name, relationshipChildDesc AS description FROM `definitions` WHERE TYPE= 'r'
                ) as a ORDER BY a.name asc";
        $result = getJson ($sql);
        echo ($result);
        
// return information for a specified property
    } else if( $_GET['do']=="property" and isset($_GET['pid'])){
        $pid = htmlspecialchars($_GET['pid']);
        $sql = "SELECT pid as 'pid', name as 'pname', 
                ifnull(valueText,ifnull(valueDate,ifnull(valueNumber,null))) as 'pvalue', 
                IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL ,  'dt', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS ptype
                FROM properties where pid=".$pid.
                " and name not in (".$reserved_object_properties.")";
        $result = getJson ($sql);
        echo ($result);
        
    } else {
        echo ("error, nothing specified");
    }
 
} // close check if do is set

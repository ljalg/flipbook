<?php
/* this file returns the json for objects */

header("Content-Type: application/json; charset=UTF-8");

include ("config.php");
include ("db.php"); 


// confirm get variable do is set
if (isset($_GET['do'])) {

// return search results
    if( $_GET['do']=="search" and isset($_GET['ss'])){
        $searchString = htmlspecialchars($_GET['ss']);
        $sql = "SELECT a.oid, a.oname, a.otype, a.pname, a.pvalue
           from (
           select o.oid as 'oid', o.name as 'oname', o.type as 'otype', null as 'pname', null as 'pvalue'
           from objects o
           where o.name like '%".$searchString."%'
           UNION
           select o.oid as 'oid', o.name as 'oname', o.type as 'otype', p.name as 'pname', COALESCE(p.valueText,p.valueDate,p.valueNumber,null) as 'pvalue'
           from objects o join properties p on o.oid=p.id
           where p.name not in (".$reserved_object_properties.") and p.idType='o' and
               (p.name like '%".$searchString."%' or
                p.valueText like '%".$searchString."%' or
                p.valueDate like '%".$searchString."%' or
                p.valueNumber like '%".$searchString."%')
           ) as a
           order by a.otype asc, a.oname asc";
        $result = getJson ($sql);
        echo ($result);
        
// return available properties for an object type
    } else if( $_GET['do']=="distincttypeproperties" and isset($_GET['otype'])){
        $otype = htmlspecialchars($_GET['otype']);
        $sql = "SELECT DISTINCT p.name as 'pname', 
                IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL , 'date', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS 'ptype'
                FROM objects as o join properties as p on p.id=o.oid
                WHERE p.name not in (".$reserved_object_properties.") and p.idType='o' and o.type='".$otype."'
                ORDER BY p.name;";
        $result = getJson ($sql);
        echo ($result);
        
// return all objects of a particular type
    } else if( $_GET['do']=="allobjectsoftype" and isset($_GET['otype'])){
        $otype = htmlspecialchars($_GET['otype']);
        $sql = "SELECT o.oid as 'oid', o.name as 'oname', o.type as 'otype', p.name as 'pname',  COALESCE(p.valueText,p.valueDate,p.valueNumber,null) as 'pvalue', 
                IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL ,  'date', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS 'ptype'
                FROM objects as o left join properties as p on p.id=o.oid
                WHERE (p.name not in (".$reserved_object_properties.") or p.name is null) and (p.idType='o' or p.idType is null) and o.type='".$otype."'
                ORDER BY o.name, p.name;";
        $result = getJson ($sql);
        echo ($result);

// return all objects except self name and type
    } else if( $_GET['do']=="allobjectsexceptself" and isset($_GET['oid'])){
        $oid = htmlspecialchars($_GET['oid']);
        $sql = "SELECT oid, name, type FROM objects WHERE oid != $oid ORDER BY name, type";
        $result = getJson ($sql);
        echo ($result);
        
// return all objects name and type
    } else if( $_GET['do']=="allobjects"){
        $sql = "SELECT oid, name, type FROM objects ORDER BY name, type";
        $result = getJson ($sql);
        echo ($result);

// return object name and type
    } else if( $_GET['do']=="nametype" and isset($_GET['oid'])){
        $oid = htmlspecialchars($_GET['oid']);
        $sql = "SELECT name, type FROM objects where oid=".$oid;
        $result = getJson ($sql);
        echo ($result);
        
// return object properties
    } else if( $_GET['do']=="properties" and isset($_GET['oid'])){
        $oid = htmlspecialchars($_GET['oid']);
        $sql = "SELECT pid as 'pid', name as 'pname', COALESCE(valueText,valueDate,valueNumber,null) as 'pvalue', 
        IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL ,  'date', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS ptype
        FROM properties where ID=".$oid.
                " and idType = 'o' and name not in (".$reserved_object_properties.");";
        $result = getJson ($sql);
        echo ($result);
        
//return list of unused properties for a specific object
    } else if( $_GET['do']=="emptyproperties" and isset($_GET['oid'])){
        $oid = htmlspecialchars($_GET['oid']);
        $sql = "SELECT name, dataType FROM definitions WHERE type = 'op' and name not in (".$reserved_object_properties.") and 
            name not in (select name from properties where id=".$oid." and idType='o') and 
            parent in (select d.did from definitions d join objects o on o.type=d.name 
            where d.type='o' and o.oid=".$oid.")";
        $result = getJson ($sql);
        echo ($result);

// return list of object types
    } else if( $_GET['do']=="objecttypesdropdown"){
        $sql = "SELECT name FROM `definitions` WHERE TYPE= 'o' ORDER BY name ASC ";
        $result = getJson ($sql);
        echo ($result);
        
// return information for a specified property
    } else if( $_GET['do']=="property" and isset($_GET['pid'])){
        $pid = htmlspecialchars($_GET['pid']);
        $sql = "SELECT pid as 'pid', name as 'pname', 
                COALESCE(valueText,valueDate,valueNumber,null) as 'pvalue', 
                IF( valueText IS NOT NULL ,  'text', IF( valueDate IS NOT NULL ,  'date', IF( valueNumber IS NOT NULL , 'number',  'undefined' ) ) ) AS ptype
                FROM properties where pid=".$pid.
                " and name not in (".$reserved_object_properties.")";
        $result = getJson ($sql);
        echo ($result);
        
    } else {
        echo ("error, nothing specified");
    }

} // close check if do is set

<?php
/* this file returns the json for related objects */ 

header("Content-Type: application/json; charset=UTF-8");

include ("../config.php");
include ("../db.php"); 
include ("6_degrees.php");


// confirm get variable do is set
if (isset($_GET['type'])) {
    $oid = 0;
    if (isset($_GET['oid'])) {
        $oid= $_GET['oid'];
    }

// return search results
    if( $_GET['type']=="object"){

        // diagram defaults
        $shape = "circle"; //default shape
        $shapeArray = ["circle", "triangle", "square", "star", "rectangle", "ellipse"];
        $shapeColor = "#ff0000"; //default color
        $shapeColorArray = ["#3F88C5", "#136F63", "#6457A6", "#FFBA08", "#5C2751", "#EBBAB9", "#FFF689"];
        $shapeSizeOrig = 10;
        $lineColor = "#557EAA"; //default is #557EAA
        
        //loop variables
        $sql = "SELECT o.oid as parentid, o.name as parentname, o.type as parenttype, r.parentDesc as parentdesc, oc.oid as childid, 
            oc.name as childname, oc.type as childtype
            FROM objects o left join relationships r on o.oid=r.poid left join objects oc on r.coid=oc.oid";
        // get related objects
        if ($oid > 0) {
            $related_string = related_csv ($oid);
            $sql .= " WHERE o.oid in (".$related_string.")";
        }
        $sql .= " ORDER BY o.type asc, o.oid asc";
        //echo $sql."---------";
        $result = get_array_from_sql ($sql);
        $result_json = '';
        $prev_parentid = '';
        $prev_object_type = '';
        $has_relationships = false;
        $last_has_adj = false;
        $s = 0; 
        $c = 0;
        
        $result_json = "[";
        foreach ($result as $row) {
            if ($prev_parentid != $row['parentid']){ // new object
                
                if ($has_relationships) {  // if previous object had relationships, close previous object
                    $result_json = rtrim($result_json,",");
                    $result_json .= ']},';
                    $has_relationships = false;
                }
                
                if ($prev_object_type != $row["parenttype"]) {  // check if new object is new object type
                    if ($s <= count($shapeArray)) { // set object shape from array
                        $shape = $shapeArray[$s];
                        $s++;
                        if ($s === count($shapeArray)) {
                            $s=0;
                        }
                    } 
                    if ($c <= count($shapeColorArray)) { // set object color from array
                        $shapeColor = $shapeColorArray[$c];
                        $c++;
                        if ($c == count($shapeColorArray)) {
                            $c=0;
                        }
                    }
                }
                
                $shapeSize = $shapeSizeOrig;
                if ($shape == "triangle" || "star") {
                    $shapeSize = $shapeSizeOrig*1.3;
                }
                if ($oid > 0 && $row['parentid'] == $oid) {  // if oid is defined, make that bigger
                    $shapeSize = $shapeSize*2;
                }
                
                $result_json .= populateObject($row['parentid'], $row['parentname'], $row['parenttype'],$shape,$shapeColor,$shapeSize);
                
                $shapeSize = $shapeSizeOrig;
                
                // populate first object's adjencies
                if ($row['childid'] != null) {
                    $result_json .= ',"adjacencies": [';
                    $result_json .= populateLine($row['parentid'], $row['childid'], $row['parentdesc'], $lineColor);
                    $has_relationships = true;
                    $last_has_adj = true;
                } else { if ($row['childid'] === null)
                    $result_json .= ',"adjacencies": []';
                    $last_has_adj = false;
                    //close object and prepare for next object
                    $result_json .= '},';
                }
                
                $prev_parentid = $row["parentid"];
                $prev_object_type = $row["parenttype"];
                
            } else if ($prev_parentid === $row['parentid']) {  // additional iterations of existing object
                $result_json .= populateLine($row['parentid'], $row['childid'], $row['parentdesc'], $lineColor);
            }
        }
        if ($last_has_adj) {  //if last item has adjencies (relationships) close them
            $result_json = rtrim($result_json,",");
            $result_json .= "]},";
        }
        $result_json = rtrim($result_json,",");
        $result_json .= "]"; //close entire json


        echo ($result_json);
        // use https://jsonformatter.curiousconcept.com/ to validate json
    }
    else {
        echo ("Please specify settings");
    }

} // close check if type is set
else {
    echo ("Please specify type");
}

function populateObject ($parentid, $parentname, $parenttype, $shape, $shapeColor, $shapeDimension) {
    // object block does not close, assumes there are relationships
    $string = "";
    $string .= '{"id": '.$parentid.',';
    $string .= '"name": "'.$parentname.':'.$parenttype.'",';
    $string .= '"data": {';
    $string .= '"$color": "'.$shapeColor.'",';
    $string .= '"$type": "'.$shape.'",';
    $string .= '"$dim": '.$shapeDimension.'}';
    
    return $string;
}

function populateLine ($parentid, $childid, $description, $lineColor) {
    $string = "";
    $string .= '{"nodeTo": '.$childid.',';
    $string .= '"nodeFrom": '.$parentid.',';
    $string .= '"desc": "'.$description.'",';
    $string .= '"data": {';
    $string .= '"$color": "'.$lineColor.'" }';
    $string .= '},';
    
    return $string;
}
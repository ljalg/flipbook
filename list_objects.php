<?php
include ('header.php');
?>


<h1>List of all Objects</h1>
<div id='resultList'></div>

<div class="viz" style="clear:both;">
<div id="inner-details"></div>
<div id="log"></div>
<div id="infovis"></div>
</div>


<script>
/* global $ */
$(document).ready(function() {
    //get list of object types
    $.getJSON("object_read.php?do=objecttypesdropdown",function(objecttypedata){
        // for each object type
        $.each(objecttypedata, function(objecttypekey,objecttype){
            // add section header
            $("#resultList").append("<h2>"+objecttype.name+"</h2>");
            // create table for this object
            $("#resultList").append("<table id='"+objecttype.name+"_table'>");
            var table = document.getElementById(objecttype.name+"_table");
            var row1 = table.insertRow(0);
            var cell1 = row1.insertCell(0);
            cell1.innerHTML = "<span class='tablebold'>Name</span>";
            
            //array for property list
            var property_list_array = [];
            $.getJSON("object_read.php?do=distincttypeproperties&otype="+objecttype.name,function(propertydata){
                // populate header row with list of used propertes
                if (propertydata == null) {
                    propertydata = [{"pname":"No Properites defined","ptype":"text"}];
                }
                $.each(propertydata, function(propertykey,object_properties){
                    var cell = row1.insertCell(propertykey+1);
                    cell.innerHTML = "<span class='tablebold'>"+object_properties.pname+"</span>";
                    property_list_array [0] = "<span class='tablebold'>Name</span>";
                    property_list_array [propertykey+1] = object_properties.pname;
                });
                //populate table with object propery values
                $.getJSON("object_read.php?do=allobjectsoftype&otype="+objecttype.name,function(objectdata){
                    var previous_object_name = "";
                    var row;
                    var cell;
                    
                    var cellnumber = 0;
                    // for each object
                    $.each(objectdata, function(objectkey,object){
                        // create new row and add name if new object
                        if (object.oname !== previous_object_name) {
                            row = table.insertRow();
                            cell = row.insertCell();
                            cell.innerHTML = "<a href='object.php?oid="+object.oid+"' class='tablebold'>"+object.oname+"</b></a>";
                            
                            //populate entire row of empty cells
                            for (var j = 1; j < property_list_array.length; j++){
                                    cell = row.insertCell();
                                    cell.innerHTML = "";
                            }
                        previous_object_name = object.oname;
                        }
                        
                        // find index in property list array and insert data
                        cellnumber = property_list_array.indexOf (object.pname);
                        row.deleteCell (cellnumber);
                        cell = row.insertCell(cellnumber);
                        cell.innerHTML = object.pvalue;
                    });
                });
            });
        });
    });
}); // end document ready

</script>
<?php
include ('footer.php');
?>

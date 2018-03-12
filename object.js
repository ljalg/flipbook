$(document).ready(function() {
/* global $ */
// populate display of data
    
    var oid = GetURLParameter('oid');

    // if oid is 0 new object
    if (oid > 0) {
        var url_name_type="object_read.php?do=nametype&oid="+oid;
        $.getJSON(url_name_type,function(data){
            $.each(data, function(i,object){
                $("#name").html(object.name);
                $("#type").html(object.type);
                $("#objectEditLink").html("<i class='fa fa-2x fa-pencil' id='object_edit' aria-hidden='true'></i>");
                edit_object_form(oid, object.type, object.name);
            });
        });
        
    } else {
        // if oid is undefined error
        $("#name").html("Error!");
        $("#type").html("Error!");
    }// close else if
    
    // populate properties
    var url_properties = "object_read.php?do=properties&oid="+oid;
    $.getJSON(url_properties, function(data) {
        if (!$.isEmptyObject(data)) {
            $.each(data, function(i, object) {
                display_object_property (object.pid, object.pname, object.pvalue, object.ptype);
            });
        }
        else {
            var newRow = "No properties defined<br>";
            $("#plist").append(newRow);
        } //end if data  is empty
        $("#plist").append("<i class='fa fa-plus-circle fa-2x' id='new_object_property' aria-hidden='true'></i>");
        new_object_property_form();
    });
    
    // populate relationships
    var url_relationships = "relationship_read.php?do=relationships&oid=" + oid;
    $.getJSON(url_relationships, function(data) {
        if (!$.isEmptyObject(data)) {
            $.each(data, function(i, object) {
                newRow = "<a href='relationship.php?rid="+ object.rid +"'>"+ object.description + "</a> - <a href='object.php?oid="+object.reloid+"'>" + object.name + ":" + object.type + "</a><br>";
                $("#rlist").append(newRow);
            });
        }
        else {
            var newRow = "No Relationships defined<br>";
            $("#rlist").append(newRow);
        } //end if data  is empty
        $("#rlist").append("<i class='fa fa-plus-circle fa-2x' id='new_relationship' aria-hidden='true'></i>");
        new_relationship_form ();
    });

});// close document Ready

//Edit object form
function edit_object_form (oid, type, name){
    var string_to_append = "<div id='form_edit_object' class='crud_form'>";
    string_to_append += "Object type can not be changed<br>";
    string_to_append += "<i class='fa fa-trash-o deleteicon'  id='deleteButton' aria-hidden='true'></i>";
    string_to_append += type+" : <input type='text' id='objectname' value='"+name+"' size="+name.length+" name='name'>";
    string_to_append += "<i class='fa fa-floppy-o saveicon' id='updateButton' aria-hidden='true'></i>";
    string_to_append += "</div>";

    $("#object_edit").after (string_to_append);
    $("#object_edit").on("click", function() {
        $("#form_edit_object").toggle();
    });
    
    $("#updateButton").click(function(){
        $.post("object_crud.php",{name: objectname.value,oid: oid, do:"editobject", submit:"submit"}, function(data, status){
            data = $.parseJSON(data);
            $("#name").empty();
            $("#name").html(data.object.name);
            displayMessage ("message_success", "object saved successfully");
        });
    });
    
    $("#deleteButton").click(function() {
        $( "#dialogConfirmObject" ).dialog({
            resizable: false,
            height: "auto",
            width: 400,
            title: "Delete Object?",
            modal: true,
            closeOnEscape: true,
            buttons: {
                "Yes, Delete this Object": function() {
                    $( this ).dialog( "close" );
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    }); 
  
    /*
        $.post("object_crud.php",{oid: oid, do:"deleteobject", submit:"submit"}, function(data, status){
            data = $.parseJSON(data);
            $("#name").empty();
            $("#name").html(data.object.name);
            window.location.href = "index.php";
            displayMessage ("message_fail", "object saved successfully");
        }); */
}

function display_object_property (property_id, property_name, property_value, property_type) {
    var newRow = "<div class='property'>"+property_name+" : "+property_value+" <i class='fa fa-pencil' id='objectPropertyEdit"+property_id+"' aria-hidden='true'></i></div>";
    $("#plist").append(newRow);
    edit_property_form(property_id, property_value, property_type);
}

//new property form
function new_object_property_form (oid) {
    var url="object_read.php?do=emptyproperties&oid="+oid;
    $.getJSON(url,function(data){
        var string_to_append = "<div id='div_form_new_object_property' class='crud_form'>";
        string_to_append += "<form id='form_new_object_property' action='object_crud.php' method='post'>";
        string_to_append += "Name: <select id='new_name_field' name='name'>";
        
        if (!$.isEmptyObject(data)){
            var firstdatatype = '';
            $.each(data, function(i,object){
                string_to_append += "<option value='"+object.name+"' type='"+object.dataType+"'>"+object.name+"</option>";
                if (firstdatatype == '') {
                    firstdatatype = object.dataType;
                }
            });
        } else {
            $('#new_object_property').hide();
            //string_to_append += "<option>All Properties Assigned</option>";
        } //end if data  is empty
        
        string_to_append += "</select>";
        string_to_append += "Value: <input id='new_value_field' type='text' size=10 name='value'>";
        string_to_append += "<input type='hidden' name='oid' value="+oid+">";
        string_to_append += "<input type='hidden' name='do' value='newobjectproperty'>";
        string_to_append += "<input type='submit' value='Submit'>";
        string_to_append += "</form></div>";

        
        $('#new_object_property').after (string_to_append);
        $('#new_object_property').on("click", function() {
            $('#div_form_new_object_property').toggle();
        });
        // set state based on existing value on load and based on item selected
        var nameFieldID = "#new_name_field";
        var valueFieldID = "new_value_field";
        changeFieldValidation(valueFieldID, firstdatatype, true);
        $(nameFieldID).on("change", function(){
            var selectedType = $(nameFieldID).find(':selected').attr('type');
            changeFieldValidation(valueFieldID, selectedType, true);
        });
        $.validate();
    });
}

//Edit property form
function edit_property_form (pid, pvalue, ptype){
    var url="object_read.php?do=property&pid="+pid;
    $.getJSON(url,function(data){
        var string_to_append = "<div id='form_edit_property_"+pid+"' class='crud_form'>";
        string_to_append += "<form action='object_crud.php' method='post'>";
        
        
        if (!$.isEmptyObject(data)){
            $.each(data, function(i,object){
                string_to_append += object.pname;
            });
        } else {
            $('#objectPropertyEdit"+pid').hide();
            //string_to_append += "<option>All Properties Assigned</option>";
        } //end if data  is empty
        
        string_to_append += " : <input type='text' value='"+pvalue+"' size="+pvalue.length+" name='value'>";
        string_to_append += "<input type='hidden' name='pid' value="+pid+">";
        string_to_append += "<input type='hidden' name='oid' value="+oid+">";
        string_to_append += "<input type='hidden' name='type' value='"+ptype+"'>";
        string_to_append += "<input type='hidden' name='do' value='editobjectproperty'>";
       
        string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
        string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
        string_to_append += "</form></div>";

        $("#objectPropertyEdit"+pid).after (string_to_append);
        $("#objectPropertyEdit"+pid).on("click", function() {
            $("#form_edit_property_"+pid).toggle();
        });
    });
}

//new relationship form
function new_relationship_form () {

    var string_to_append = "<div id='form_new_relationship' class='crud_form'>";
    string_to_append += "<form action='relationship_crud.php' method='post'>";
        
    var url1="relationship_read.php?do=relationshipsdropdown";   
    $.getJSON(url1,function(data){     
        string_to_append += "Type: <select name='type'>";
        
        if (!$.isEmptyObject(data)){
            $.each(data, function(i,object){
                string_to_append += "<option value='"+object.role+","+object.did+"'>"+object.description+"</option>";
            });
        } else {
            $('#new_relationship_property').hide();
            string_to_append += "<option>No Relationship Descriptions Defined</option>";
        } //end if data  is empty
        
        string_to_append += "</select>";
        
        var url2="object_read.php?do=allobjectsexceptself&oid="+oid;   
        $.getJSON(url2,function(data){     
            string_to_append += "With: <select name='sec_oid'>";
            
            if (!$.isEmptyObject(data)){
                $.each(data, function(i,object){
                    string_to_append += "<option value='"+object.oid+"'>"+object.name+" : "+object.type+"</option>";
                });
            } else {
                $('#new_relationhip_property').hide();
                string_to_append += "<option>No Relationship Descriptions Defined</option>";
            } //end if data  is empty
            
            string_to_append += "</select>";
        
        
            string_to_append += "<input type='hidden' name='do' value='newrelationship'>";
            string_to_append += "<input type='hidden' name='pri_oid' value='"+oid+"'>";
            string_to_append += "<input type='submit' value='Submit'>";
            string_to_append += "</form></div>";
    
            $('#new_relationship').after (string_to_append);
            $('#new_relationship').on("click", function() {
                $('#form_new_relationship').toggle();
            });
        });
    });
}





function display_relationship_property () {
    
}
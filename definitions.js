$(document).ready(function() {
/* global $ used to get rid of IDE errors*/
// populate display of data

    // populate both add object type of relationship type
    $("#objecttypenew").html("<i class='fa fa-plus-circle fa-2x' id='newobjecttype' aria-hidden='true'></i>");
    new_object_type_form();
    
    $("#relationshiptypenew").html("<i class='fa fa-plus-circle fa-2x' id='newrelationshiptype' aria-hidden='true'></i>");
    new_relationship_type_form();

    // populate object types and property types
    var url_properties = "definitions_read.php?do=definitionobjecttypes";
    var objectTypeId = 0;
    $.getJSON(url_properties, function(data) {
        if (!$.isEmptyObject(data)) {
            $.each(data, function(i, object) {
                // if new object type
                if (objectTypeId != object.odid) {
                    // place add object property type icon at the end of each list
                    if (objectTypeId !=0) {
                        add_object_type_property(objectTypeId);
                    }
                    objectTypeId = object.odid;
                    $("#objecttypepropertieslist").append("<h2>"+object.odname+" <i class='fa fa-pencil' id='objectTypeEdit"+object.odid+"' aria-hidden='true'></i></h2>");
                    edit_object_type_form (object.odid, object.odname);
                }
                if (object.pdname == null) {
                    $("#objecttypepropertieslist").append("No properties defined<br>");
                } else {
                    // add defined properties
                    var newRow = "<div class='property'>"+object.pdname+" : "+object.pdtype+" <i class='fa fa-pencil' id='propertyTypeEdit"+object.pdid+"' aria-hidden='true'></i></div>";
                    $("#objecttypepropertieslist").append(newRow);
                    edit_property_form(object.pdid, object.pdname, object.pdtype,object.pdformat,object.pddefault);
                }
            });
            add_object_type_property(objectTypeId);
        }
    });
    
    // populate relationships
    url_properties = "definitions_read.php?do=definitionrealationshiptypes";
    var relationshipTypeId = 0;
    $.getJSON(url_properties, function(data) {
        if (!$.isEmptyObject(data)) {
            $.each(data, function(i, object) {
                // if new object type
                if (relationshipTypeId != object.rdid) {
                    // place add object property type icon at the end of each list
                    if (relationshipTypeId !=0){
                        add_relationship_type_property(relationshipTypeId);
                    }
                    relationshipTypeId = object.rdid;
                    $("#relationshiptypepropertieslist").append("<h2>"+object.parentdesc+" : "+object.childdesc+" <i class='fa fa-pencil' id='objectTypeEdit"+object.rdid+"' aria-hidden='true'></i></h2>");
                    edit_relationship_type_form (object.rdid, object.parentdesc, object.childdesc);
                }
                if (object.pdname == null) {
                    $("#relationshiptypepropertieslist").append("No properties defined<br>");
                } else {
                    // add defined properties
                    var newRow = "<div class='property'>"+object.pdname+" : "+object.pdtype+" <i class='fa fa-pencil' id='propertyTypeEdit"+object.pdid+"' aria-hidden='true'></i></div>";
                    $("#relationshiptypepropertieslist").append(newRow);
                    edit_property_form(object.pdid, object.pdname, object.pdtype,object.pdformat,object.pddefault);
                }
            });
            add_relationship_type_property(relationshipTypeId);
        }
    });

function add_object_type_property (did){
    $("#objecttypepropertieslist").append("<i class='fa fa-plus-circle' id='new_property"+did+"' aria-hidden='true'></i>");
    new_property_form("op",did);
}

function add_relationship_type_property (did){
    $("#relationshiptypepropertieslist").append("<i class='fa fa-plus-circle' id='new_property"+did+"' aria-hidden='true'></i>");
    new_property_form("rp",did);
}

//new object type form
function new_object_type_form (){
    var string_to_append = "<div id='form_new_object_type' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><label>Name:</label> <input type='text' value='' name='name'></p>";
    string_to_append += "<input type='hidden' name='type' value='o'>";
    string_to_append += "<input type='hidden' name='do' value='newobjectdefinition'>";
   
    string_to_append += "<button type='submit' value='submit' name='submit'>Add</button>";
    string_to_append += "</form></div>";

    $("#newobjecttype").after (string_to_append);
    $("#newobjecttype").on("click", function() {
        $("#form_new_object_type").toggle();
    });
}

//edit object type form
function edit_object_type_form (odid, oname){
    var string_to_append = "<div id='form_edit_object_type_"+odid+"' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><input type='text' value='"+oname+"' size='"+oname.length+"' name='name'></p>";
    string_to_append += "<input type='hidden' name='did' value="+odid+">";
    string_to_append += "<input type='hidden' name='do' value='editobjectdefinition'>";
   
    string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
    string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
    string_to_append += "</form></div>";

    $("#objectTypeEdit"+odid).after (string_to_append);
    $("#objectTypeEdit"+odid).on("click", function() {
        $("#form_edit_object_type_"+odid).toggle();
    });
}

//new relationship type form
function new_relationship_type_form (){
    var string_to_append = "<div id='form_new_relationship_type' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><label>Parent:</label> <input type='text' value='' name='pardesc'></p>";
    string_to_append += "<p><label>Child:</label> <input type='text' value='' name='childdesc'></p>";
    string_to_append += "<input type='hidden' name='type' value='r'>";
    string_to_append += "<input type='hidden' name='do' value='newrelationshipdefinition'>";
    string_to_append += "<button type='submit' value='submit' name='submit'>Add</button>";
    string_to_append += "</form></div>";

    $("#relationshiptypenew").after (string_to_append);
    $("#relationshiptypenew").on("click", function() {
        $("#form_new_relationship_type").toggle();
    });
}

//edit relationship type form
function edit_relationship_type_form (rdid, parentdesc, childdesc){
    var string_to_append = "<div id='form_edit_relationship_type_"+rdid+"' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><label>Parent:</label> <input type='text' value='"+parentdesc+"' name='pardesc'></p>";
    string_to_append += "<p><label>Child:</label> <input type='text' value='"+childdesc+"' name='childdesc'></p>";
    string_to_append += "<input type='hidden' name='did' value='"+rdid+"'>";
    string_to_append += "<input type='hidden' name='do' value='editrelationshipdefinition'>";
   
    string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
    string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
    string_to_append += "</form></div>";

    $("#objectTypeEdit"+rdid).after (string_to_append);
    $("#objectTypeEdit"+rdid).on("click", function() {
        $("#form_edit_relationship_type_"+rdid).toggle();
    });
}


//new property form
function new_property_form (type, parentId) {
    var string_to_append = "<div id='form_new_property_"+parentId+"' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><label>Name:</label> <input type='text' value='' name='name'></p>";
    string_to_append += "<p><label>Type:</label> <select name='datatype'>";
    string_to_append += "<option value='text'>Text</option>";
    string_to_append += "<option value='number'>Number</option>";
    string_to_append += "<option value='date'>Date</option>";
    string_to_append += "</select></p>";
    string_to_append += "<p><label>Format:</label> <input type='text' value='' name='format'></p>";
    string_to_append += "<p><label>Default:</label> <input type='text' value='' name='default'></p>";
    string_to_append += "<input type='hidden' name='pdid' value="+parentId+">";
    string_to_append += "<input type='hidden' name='type' value="+type+">";
    string_to_append += "<input type='hidden' name='do' value='newpropertydefinition'>";
    string_to_append += "<button type='submit' value='submit' name='submit'>Add</button>";
    string_to_append += "</form></div>";

        $("#new_property"+parentId).after (string_to_append);
        $("#new_property"+parentId).on("click", function() {
            $("#form_new_property_"+parentId).toggle();
        });
}

//Edit property form
function edit_property_form (pdid, pdname, pdtype, pdformat, pddefault){
    var text_select="";
    var date_select = "";
    var number_select = "";
    switch(pdtype) {
        case 'text': text_select = "selected"; break;
        case 'date': date_select = "selected"; break;
        case 'number': number_select = "selected"; break;
    }

    var string_to_append = "<div id='form_edit_property_"+pdid+"' class='crud_form'>";
    string_to_append += "<form action='definitions_crud.php' method='post'>";
    string_to_append += "<p><label>Name:</label> <input type='text' value='"+pdname+"' name='name'></p>";
    string_to_append += "<p><label>Type:</label> <select name='datatype'>";
    string_to_append += "<option value='text' "+text_select +">Text</option>";
    string_to_append += "<option value='number' "+number_select +">Number</option>";
    string_to_append += "<option value='date' "+date_select +">Date</option>";
    string_to_append += "</select></p>";
    string_to_append += "<p><label>Format:</label> <input type='text' value='"+pdformat+"' name='format'></p>";
    string_to_append += "<p><label>Default:</label> <input type='text' value='"+pddefault+"' name='default'></p>";
    string_to_append += "<input type='hidden' name='did' value="+pdid+">";
    string_to_append += "<input type='hidden' name='do' value='editpropertydefinition'>";
   
    string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
    string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
    string_to_append += "</form></div>";

    $("#propertyTypeEdit"+pdid).after (string_to_append);
    $("#propertyTypeEdit"+pdid).on("click", function() {
        $("#form_edit_property_"+pdid).toggle();
        //$("#form_edit_property_"+pdid).toggleClass("light");
        //$("#content").toggleClass("fade");
    });
}

// close document Ready
});
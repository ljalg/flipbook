/* js to put in here */
/* form validation
  check database for object name */

/* get variable from URL */
function GetURLParameter(sParam) {
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) {
            return sParameterName[1];
        }
    }
}

/* display message */
function displayMessage (message_type, message_text) {
    $("#message").html(message_text);
    $("#message").addClass(message_type);
    $("#message").fadeIn("slow");
    $("#form_edit_object").toggle();
    setTimeout(function(){
        $("#message").fadeOut("slow");
    },5000);
    setTimeout(function(){
        $("#message").empty();
        $("#message").removeClass(message_type);
    },6000);
}

/* change value field to appropriate validation based on field type */
function changeFieldValidation (fieldid, fieldtype, required, defaultvalue) {
    //alert (fieldid + "-" + fieldtype + "-"+ required + "-" + defaultvalue);
    
    var fieldDef = document.getElementById(fieldid);
    if (defaultvalue != '' && defaultvalue != undefined) {
        fieldDef.value = defaultvalue;
    }
    if (required) {
        fieldDef.setAttribute('required','');
    }
    switch(fieldtype) {
        case "text":
            fieldDef.setAttribute('size','40');
            fieldDef.setAttribute('data-validation', 'length');
            fieldDef.setAttribute('data-validation-length', 'min10');
            fieldDef.setAttribute('placeholder','text');
            break;
        case "date":
            fieldDef.setAttribute('size','30');
            fieldDef.setAttribute('data-validation','date');
            fieldDef.setAttribute('data-validation-format','dd/mm/yyyy');
            fieldDef.setAttribute('placeholder','dd/mm/yyyy');
            /* fieldDef.datepicker({format: 'dd/mm/yyyy'});*/
            break;
        case "number":
            fieldDef.setAttribute('size','20');
            fieldDef.setAttribute('data-validation','number');
            fieldDef.setAttribute('placeholder','number');
            break;
        default:
            fieldDef.setAttribute('size','1');
            fieldDef.value = "no type set";
    }
}
"use strict";

function createError(text) {
    $("#errorLoc").append($("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close Error'><span aria=hidden=true>x</span></button><strong>Error!</strong> " + text + "</div>"));
}

function findApp(categoryName) {
    //Get the web app from the database
    $.ajax({
        url: "http://soapboks.co/database.php?function=get&category=" + categoryName,
        success: function(data) {
            //If nothing came up from the database
            if (data.length == 0) {
                createError("No sites are in the \"" + categoryName + "\" category.");
            } else {
                window.open("viewing.php?url=" + data);
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            createError(textStatus);
        }
    });
}
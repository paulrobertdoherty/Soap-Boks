"use strict";

function createError(text) {
    $("#errorLoc").append($("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close Error'><span aria=hidden=true>x</span></button><strong>Error!</strong> " + text + "</div>"));
}

function createSuccess(text) {
    $("#errorLoc").append($("<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria=hidden=true>x</span></button><strong>Success!</strong> " + text + "</div>"));
}

function UrlExists(url, cb){
    $.ajax({
        url: url,
        dataType: "text/html",
        type: "GET",
        complete:function(xhr){
            if (typeof cb === "function") {
               cb.apply(this, [xhr.status]);
            }
        }
    });
}

function distributeApp(categoryName) {
    //The url submitted
    var url = document.getElementById("webURL").value;
    
    //Test to see if the url is legitimate
    UrlExists(url, function(status){
        if(status != 0){
            //URL not legitimate, make error
            createError("The url returned an error of " + status + ".  Did you type it correctly and/or include http:// or https://?");
            return;
        }
    });
    
    //The number of times to distribute
    var distCount = parseInt(document.getElementById("distCount").value);
    
    //If something failed while getting the distribution count
    if (isNaN(distCount)) {
        createError("Did you put a number in when you specified the number of times to distribute?");
        return;
    }
    
    //Input the web app in to the database
    $.ajax({
        url: "database.php?function=set&category=" + categoryName + "&name=" + url + "&distCount=" + distCount,
        success: function(data) {
            createSuccess(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            createError(textStatus);
        }
    });
}
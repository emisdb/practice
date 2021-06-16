function loadDoc() {

    var settings = {
        "async": true,
        "crossDomain": true,
        "url": "http://data.gov.spb.ru/api/v1/datasets/",
        "method": "GET",
        "headers": {
       }
    }

    $.ajax(settings).done(function (response) {
        document.getElementById("demo").innerHTML = response;
    });
}

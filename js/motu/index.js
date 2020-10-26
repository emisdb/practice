// Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
function emptyLi(ii){
    var retval ="";
    for (i=0;i<ii;i++){
        retval += "<li>null</li>";
    }
    return retval;
}
btn.onclick = function() {
    var prev =0;
    var mycontent = '<ol start="1">' + '<li>mem</li> '+ '</ol>';
    var myplace = document.getElementById("myModalContent");
    myplace.innerHTML =mycontent;
    var myplace = document.getElementById("myModalFooter");
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.addEventListener('DOMContentLoaded', function(){

});

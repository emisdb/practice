var params ={
    size: 20,
    color: 'green',
    set : function(what){
        if(what=='size')
            return this.size
        else
            return this.color;
    },
    test: function(obj){
        test_func(obj)
    }
}
var callNumber=0;
function go_inc(obj)
{
    setTimeout(go_inc, 3000, obj)
    var intI = 0
    intI++; callNumber++;
    obj.innerHTML=":"+callNumber+":"+intI

}
function test_func(obj){
    obj.innerHTML=this.toString('size')
}
function httpGet(theUrl)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false ); // false for synchronous request
    xmlHttp.send( null );
    return xmlHttp.responseText;
}
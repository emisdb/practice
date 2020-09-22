var params ={
    size: 20,
    red: 'green',
    val : function(what){
        if(what=='size')
            return this.size
        else
            return this.color;
    },
    debug: {
        get : function(what){
            if(what=='size')
                return this.size
            else
                return this.color;
        },
    }
 }
 var teaser = {
     size: 20,
     red: 0,
     green: 0,
     blue: 0,
     get fontsize(){
        return this.size+'pt'
     },
         get color(){
         var red = this.red
         if (red>50) red=250
         var green = this.green
         if (green>50) green=250
         var blue = this.blue
         if (blue>50) blue=250
         return "rgb("+red*5+","+green*5+","+blue*5+")"
     },
     sit: function (obj) {
         obj.style.backgroundColor= this.color
         obj.style.fontSize=this.fontsize
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
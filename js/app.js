var params ={
    size: 20,
    color: 'green',
    set : function(what){
        if(what=='size')
            return this.size
        else
            return this.color;
    }
}
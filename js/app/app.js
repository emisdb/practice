class App {
    constructor(element, ul, selectList) {
        this.element = element;
        this.list = ul;
        this.tmplist = ul;
        this.level = 0;
        this.selectList = selectList;
        this.selection = [];
        this.currenttype = 0;
        this.currentid = 0;

        this.initialize();
    }

    clearContainer() {
        if(this.list.childElementCount>0){
            while( this.list.firstChild ){
                this.list.removeChild( this.list.firstChild );
            }
        }
    }
        initialize() {
        if(this.level>0) this.collectData();
        this.clearContainer();
        this.selectList.forEach(this.doItem,this);
    }
     doItem(item, index) {
        if(!(item[1].indexOf( this.level)<0)) {
            this.currenttype = item[3];
            this.currentid = item[0];
            if(this.currenttype == 3){
                let obItem=new SelectItem(this.currenttype,item);
                this.list.appendChild(obItem.element);
            }
            else{
                if(item[2].length > 0) {
                   let obItem=new SelectItem(0,item);
                   this.list.appendChild(obItem.element);
                   this.tmplist=obItem.element.getElementsByTagName("UL").item(0);
                }
                item[4].forEach(this.createItem,this);
            }
        }
     }
    createItem(item, index) {
        let obItem=new SelectItem(this.currenttype,item,this.currentid);
        this.tmplist.appendChild(obItem.element);
    }
   collectData(){
       var inputs = this.element.getElementsByTagName("input");
       for (var i = 0; i < inputs.length; i++) {
           console.log(inputs[i].name + ":" + inputs[i].type + ":" + inputs[i].value + ":" + inputs[i].checked);
       }
       var selects = this.element.getElementsByTagName("select");
       for (var i = 0; i < selects.length; i++) {
           console.log(selects[i].name + ":"  + ":" + selects[i].value );
       }
    }
    showmap() {
        app.clearContainer();
        let myMap = new ymaps.Map("map", {
            center: [30.25, 59.94],
            zoom: 12
        });
        myMap.controls.add('zoomControl', {right: '5px', top: '50px'});
        let arr= new ymaps.GeoObjectCollection();
        for (var i=0; i<this.hotels.length; i++) {
            let plm =new ymaps.Placemark(
                [this.hotels[i].longtitude ,this.hotels[i].latitude],
                {
                balloonContent: this.hotels[i].name,
                iconContent: this.hotels[i].name.slice(0,2),
                },
                {
                preset: "islands#circleDotIcon",
                iconColor: '#ce6767'
            });
            myMap.geoObjects.add(plm);       }

    }

}
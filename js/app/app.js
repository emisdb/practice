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

    initialize() {
        if(this.list.childElementCount>0){
            while( this.list.firstChild ){
                this.list.removeChild( this.list.firstChild );
            }
        }
       this.selectList.forEach(this.doItem,this);
    }
     doItem(item, index) {
        if(!(item[1].indexOf( this.level)<0)) {
            if(item[2].length > 0) {
               let obItem=new SelectItem(0,item);
               this.list.appendChild(obItem.element);
               this.tmplist=obItem.element.getElementsByTagName("UL").item(0);
            }
            this.currenttype = item[3];
            this.currentid = item[0];
            item[4].forEach(this.createItem,this);
        }
     }
    createItem(item, index) {
        let obItem=new SelectItem(this.currenttype,item,this.currentid);
        this.tmplist.appendChild(obItem.element);
    }
        update(){
    }
}
class App {
    constructor(element, ul, selectList) {
        this.element = element;
        this.list = ul;
        this.tmplist = ul;
        this.level = 0;
        this.selectList = selectList;

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
        if(item[0] == this.level) {
            if(item[1].length > 0) {
               let obItem=new SelectItem(0,item);
               this.list.appendChild(obItem.element);
               this.tmplist=obItem.element.getElementsByTagName("UL").item(0);
            }
            item[2].forEach(this.createItem,this);
        }
     }
    createItem(item, index) {
        let obItem=new SelectItem(1,item);
        this.tmplist.appendChild(obItem.element);
    }
        update(){
    }
}
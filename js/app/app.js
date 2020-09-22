class App {
    constructor(element, ul, level, selectList) {
        this.element = element;
        this.list = ul;
        this.level = level;
        this.selectList = selectList;

        this.initialize();
    }

    initialize() {
        this.selectList.forEach(this.createItem,this)
    }
     createItem(item, index) {
        let obItem=new SelectItem(this.level,item);
         this.list.appendChild(obItem.element);
    }
}
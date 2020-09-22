class SelectItem {
    constructor(level, item_construct){
        this.level = level;
        this.title = item_construct[0];
        this.link = item_construct[1];
        this.action = item_construct[2];
        this.handleFollow = this.handleFollow.bind(this);

        this.initialize();
    }
    initialize() {
        var icon = createElement('i', { className: 'fa fa-arrow-circle-right' });
        var link = createElement('a', { href: '#', className: 'select-item', onclick: this.handleFollow }, icon);
        var div0 = createElement('div', { className: 'col-75', },this.title);
        var div1 = createElement('div', { className: 'col-25', },link);
        var label = createElement('label', { className: 'row', },div0,div1);
        this.element = createElement('li', { className: 'li-item', },label);
    }
    handleFollow(event) {
        event.preventDefault();
        this.follow();
    }
    follow() {
        window.alert(":" + this.level + ":" + this.action + ":" + this.link);
    }

 }
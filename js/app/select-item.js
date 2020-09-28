class SelectItem {
    constructor(itemtype, item_construct){
        this.itemtype = itemtype;
        if (itemtype == 0)
            this.title = item_construct[1];
        else {
            this.title = item_construct[0];
            this.action = item_construct[1];
        }

        this.handleFollow = this.handleFollow.bind(this);

        this.initialize();
    }
    initialize() {
        switch (this.itemtype) {
            case 0:
                var h4 = createElement('h4', {}, this.title);
                var ul = createElement('ul', {});
                this.element = createElement('li', {className: 'li-item',}, h4, ul);
                break;
            case 1:
                var icon = createElement('i', {className: 'fa fa-arrow-circle-right'});
                var link = createElement('a', {href: '#', className: 'select-item', onclick: this.handleFollow}, icon);
                var div0 = createElement('div', {className: 'col-75',}, this.title);
                var div1 = createElement('div', {className: 'col-25',}, link);
                var label = createElement('label', {className: 'row',}, div0, div1);
                this.element = createElement('li', {className: 'li-item',}, label);
                break;
            case 2:
        }
    }
    handleFollow(event) {
        event.preventDefault();
        this.follow();
    }
    follow() {
        app.level = this.action;
        app.initialize();

    }

 }
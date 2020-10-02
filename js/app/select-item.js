class SelectItem {
    constructor(itemtype, item_construct,itemname = 0){
        this.itemtype = itemtype;
        this.name = itemname;
        this.value = item_construct[0];
        this.title = item_construct[2];
        this.tmp ='';

        if (itemtype != 0)
        {
            this.action = item_construct[1];
        }
        if (itemtype == 3)
        {
            this.options = item_construct[4];
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
                var opbox = createElement('input', { type: 'radio', className: 'optionbox', name: 'select'+ this.name, checked: this.action ? 1 : 0, value: this.value });
                var div0 = createElement('div', {className: 'col-75',}, this.title);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                this.element = createElement('li', {className: 'li-item',}, label);
                break;
              case 3:
                var opbox = createElement('select', {  className: 'selectbox', name: 'select'+ this.value });
                this.tmp = opbox;
                this.options.forEach((option) => {
                    var op = createElement('option',{ value: option[0], selected: option[1]}, option[2]);
                    this.tmp.appendChild(op);
                })
                var h4 = createElement('h4', {}, this.title);
                var div0 = createElement('div', {className: 'col-75',}, h4);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                this.element = createElement('li', {className: 'li-item',}, label);
                break;
            default:
                var it_type='';
                if (this.itemtype == 4 ) it_type ='number';
                else if (this.itemtype == 5 ) it_type ='time';
                else it_type ='text';
                var opbox = createElement('input', { type: it_type, className: 'inputbox', name: 'ibox'+ this.name + this.value, placeholder:(this.itemtype == 5 )?'08:00':'', });
                var div0 = createElement('div', {className: 'col-75',}, this.title);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                this.element = createElement('li', {className: 'li-item',}, label);
                break;
        }
    }
    handleFollow(event) {
        event.preventDefault();
        this.follow();
    }

    follow() {
        if(this.action == 1)
            app.showmap();
        else {
            app.level = this.action;
            app.initialize();
        }

    }

 }
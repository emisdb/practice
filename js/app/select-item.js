class SelectItem {
    constructor(itemtype, item_construct,itemname = 0){
        this.itemtype = itemtype;
        this.name = itemname;
        this.value = item_construct[0];
        this.title = item_construct[2];
        this.tmp ='';
        this.options = item_construct[4];
        this.action = 0;


        this.handleFollow = this.handleFollow.bind(this);

        this.initialize();
    }
    initialize() {
        var h4 = createElement('h4', {}, this.title);
        if(this.itemtype == 3) {
            var ul = createElement('select', {className: 'selectbox', name: 'select' + this.value});
            var div0 = createElement('div', {className: 'col-75',}, h4);
            var div1 = createElement('div', {className: 'col-25',}, ul);
            var label = createElement('label', {className: 'row',}, div0, div1);
            this.element = createElement('li', {className: 'li-item',}, label);
        }
        else {
            var ul = createElement('ul', {});
            this.element = createElement('li', {className: 'li-item',}, h4, ul);
        }
       this.options.forEach((option) => {
            let obItem=this.createItem(option);
            ul.appendChild(obItem);
        })

    }
        createItem(item) {
            switch (this.itemtype) {
            case 0:
                 return createElement('li', {className: 'li-item',}, item[2]);
                break;
            case 1:
                var icon = createElement('i', {className: 'fa fa-arrow-circle-right'});
                var link = createElement('a', {href: '#', className: 'select-item', onclick: this.handleFollow}, icon);
                this.action = item[1];
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, link);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
                break;
            case 2:
                var opbox = createElement('input', { type: 'radio', className: 'optionbox', name: 'select'+ this.value, checked: item[1] ? 1 : 0, value: item[0] });
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
                break;
              case 3:
                return createElement('option',{ value: item[0], selected: item[1]}, item[2]);
                break;
            default:
                var it_type='';
                if (this.itemtype == 4 ) it_type ='number';
                else if (this.itemtype == 5 ) it_type ='time';
                else it_type ='text';
                var opbox = createElement('input', { type: it_type, className: 'inputbox', name: 'ibox'+ this.name + this.value, placeholder:(this.itemtype == 5 )?'08:00':'', });
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
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
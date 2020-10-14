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
            ul.addEventListener('change',this.setSelectValue(this.value,ul))
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
                var link = createElement('a', {href: '#', className: 'select-item', data:[item[0],item[1]], onclick: this.handleFollow}, icon);
                this.action = item[1];
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, link);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
                break;
            case 2:
                var opbox = createElement('input', { type: 'radio',
                                                    className: 'optionbox',
                                                    name: 'select'+ this.value, checked: item[1] ? 1 : 0,
                                                    value: item[0]});
                opbox.addEventListener('change',this.setValue(this.value,item[0]))
                if(item[1]) app.selection[this.value] = item[0];
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
                break;
              case 3:
                if(item[1]) app.selection[this.value] = item[0];
                return createElement('option',{ value: item[0], selected: item[1]}, item[2]);
                break;
            default:
                var it_type='';
                if (this.itemtype == 4 ) it_type ='number';
                else if (this.itemtype == 5 ) it_type ='time';
                else it_type ='text';
                var opbox = createElement('input', { type: it_type, className: 'inputbox', name: 'ibox'+ this.name + this.value, placeholder:(this.itemtype == 5 )?'08:00':'', });
               opbox.addEventListener('change',this.setBoxValue(this.value,item[0],opbox))
                var div0 = createElement('div', {className: 'col-75',}, item[2]);
                var div1 = createElement('div', {className: 'col-25',}, opbox);
                var label = createElement('label', {className: 'row',}, div0, div1);
                return createElement('li', {className: 'li-item',}, label);
                break;
        }
    }
    setSelectValue(vari,ul) {
        var select =ul;
        var closure = function(){
            app.selection[vari] =select.value;
        }
        return closure;
    }
    setBoxValue(vari,subvari,box) {
        var select =box;
        var closure = function(){
            if(app.selection[vari] == undefined) app.selection[vari] =[];
            app.selection[vari][subvari] =select.value;
        }
        return closure;
    }
    setValue(vari, vali) {
        var closure = function(){
            app.selection[vari] =vali;
        }
        return closure;
    }
    handleFollow(event) {
        event.preventDefault();
        app.selection[this.value] = event.currentTarget.data[0];
        this.follow(event.currentTarget.data[1]);
    }

    follow(action) {
        if(action == 1)
            app.showmap();
        else {
            app.level = action;
            app.initialize();
        }

    }

 }
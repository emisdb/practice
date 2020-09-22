var sel = document.getElementById('todo-list');

var opbox0 = createElement('input', { type: 'radio', className: 'optionbox', name: 'select0', value: '0', onchange: this.handleToggle });
var opbox1 = createElement('input', { type: 'radio', className: 'optionbox', name: 'select0', value: '1', onchange: this.handleToggle });
var opbox2 = createElement('input', { type: 'radio', className: 'optionbox', name: 'select0', value: '2', onchange: this.handleToggle });
var div0 = createElement('div', { className: 'col-75', },'Уже придумал куда сходить?');
var div1 = createElement('div', { className: 'col-25', },opbox0);
var label = createElement('label', { className: 'row', },div0,div1);
var li = createElement('li', { className: 'li-item', },label);

// add opt to end of select box (sel)
sel.appendChild(li);
//sel.appendChild(opbox1);
//sel.appendChild(opbox2);
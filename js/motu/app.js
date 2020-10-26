class App {
    constructor(container) {
        this.map_container = container;
    }
    setMap(ymaps){
        this.map = new ymaps.Map(this.map_container, {
            center: [30.25, 59.943],
            zoom: 14
        });
        this.map.controls.add('zoomControl', {right: '5px', bottom: '50px'});
    }
    setHotels(hotels){
        this.hotels =hotels;
        let mm = document.querySelector("#myMenu");
        var list = "<table>";
        let arr= new ymaps.GeoObjectCollection();
        for (var i=0; i<this.hotels.length; i++) {
            const label = this.hotels[i].name.slice(0,2);
            const name = this.hotels[i].name;
            list += "<tr class='" + (i%2?"odd":"even")+ "' onmouseover='app.omover(this)' onmouseout='app.omout(this)' ><td>" + label + "</td><td><a href='#'>" + name + "</a></td></tr>";
            let plm =new ymaps.Placemark(
                [this.hotels[i].longtitude ,this.hotels[i].latitude],
                {
                balloonContent: name,
                iconContent: label,
                },
                {
                preset: "twirl#darkblueIcon",
            });
            this.hotels[i].mark =plm;
            this.map.geoObjects.add(plm);
        }
        mm.innerHTML = list + "</table>";

    }
    omover(tr){
        let e=document.querySelector("#myResult");
        e.innerHTML = this.hotels[tr.rowIndex].name;
        var geoO=this.hotels[tr.rowIndex].mark;
//        geoO.options.set("iconColor", 'ff66ff');
        geoO.options.set('preset', 'twirl#nightDotIcon');

    }
    omout(tr){
        let e=document.querySelector("#myResult");
        e.innerHTML = "";
        var geoO=this.hotels[tr.rowIndex].mark;
        geoO.options.set('preset', 'twirl#darkblueIcon');

    }

}
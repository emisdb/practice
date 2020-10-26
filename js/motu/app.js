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
            list += "<tr><td>" + label + "</td><td><a href='#'>" + name + "</a></td></tr>";
            let plm =new ymaps.Placemark(
                [this.hotels[i].longtitude ,this.hotels[i].latitude],
                {
                balloonContent: name,
                iconContent: label,
                },
                {
                preset: "islands#circleDotIcon",
                iconColor: '#ce6767'
            });
            this.map.geoObjects.add(plm);
        }
        mm.innerHTML = list + "</table>";

    }

}
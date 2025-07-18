export const mapPropertys = () => {
  let mapElement = document.getElementById("mapview");
  const POINT_IMAGE = `${ajax_var.image}/point-map.png`;
  if (mapElement) {
    let map = L.map(mapElement, { scrollWheelZoom: true }).setView(
      [-34.6037, -58.3816],
      8
    );
    // Add the MapTiler tile layer
    L.tileLayer(
      "https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=uBBj5vx85VpOncGtyuHq",
      {
        //attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles courtesy of <a href="https://www.maptiler.com/">MapTiler</a>',
        tileSize: 512,
        zoomOffset: -1,
        minZoom: 5,
        maxZoom: 20,
        crossOrigin: true,
        scrollwheel: true,
      }
    ).addTo(map);
    const propertyICON = L.icon({
      iconUrl: POINT_IMAGE,
      iconSize: [30, 30], // tamaño del ícono
      iconAnchor: [16, 32], // punto de anclaje (base del ícono)
      popupAnchor: [0, -32], // dónde se abre el popup relativo al ícono
    });
    const seenCoords = new Set();

    propertiesData.forEach((prop) => {
      if (prop.lat && prop.lng) {
        let lat = parseFloat(prop.lat);
        let lng = parseFloat(prop.lng);
        let key = `${lat},${lng}`;

        while (seenCoords.has(key)) {
          lat += (Math.random() - 0.5) * 0.0002;
          lng += (Math.random() - 0.5) * 0.0002;
          key = `${lat},${lng}`;
        }

        seenCoords.add(key);

        L.marker([lat, lng], { icon: propertyICON }).addTo(map).bindPopup(`
                        <a href="${prop.permalink}" class="card-property">
                            <div class="image">
                                <img src="${prop.image}" alt="${prop.address}" width="100%" height="100%" loading="lazy">
                                <div class="price">
                                    <p class="type">${prop.operation_type} / ${prop.type} </p>
                                    <p class="amount">${prop.price}</p>
                                 
                                </div>
                            </div>
                            <div class="body">
                                <div class="location">
                                    <p class="name">${prop.address}</p>
                                    <p class="district">${prop.location}</p>
                                </div>
                                <div class="specs">
                                    <p class="total">${prop.total_surface}</p>
                                    <p class="bathroom_amount">${prop.bathroom_amount}</p>
                                    <p>M<sup>2</sup></p>
                                    <p>Baños</p>
                                </div>
                            </div>
                        </a>`);
      }
    });
  }

  let closeBtn = document.querySelectorAll(".btn-close-map");
  let openBtn = document.querySelectorAll(".btn-open-map");
  let mapView = document.getElementById("mapview");
  let toggleBtn = document.querySelector(".btn-toggle-map");
  let btnOpenFilter = document.querySelector('.btn-open-filter');
  let closeFilter = document.querySelector('.close-filter');
  if (closeBtn.length > 0) {
    closeBtn.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        openBtn.forEach((btn) => {
          btn.classList.remove("active");
        });
        let row = document.querySelector(".row-results");
        mapView.classList.remove("show");
        mapView.parentElement.style.overflowY = "scroll";
        row.classList.remove("d-none");
        btn.classList.add("active");
      });
    });
  }
  if (openBtn.length > 0) {
    openBtn.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        closeBtn.forEach((btn) => {
          btn.classList.remove("active");
        });
        let row = document.querySelector(".row-results");
        mapView.classList.add("show");
        mapView.parentElement.style.overflowY = "hidden";
        row.classList.add("d-none");
        btn.classList.add("active");
      });
    });
  }
  if (toggleBtn) {
    toggleBtn.addEventListener("click", (e) => {
      let row = document.querySelector(".row-results");
      if(toggleBtn.classList.contains('active')){
        toggleBtn.classList.remove("active");
        mapView.classList.remove("show");
        mapView.parentElement.style.overflowY = "scroll";
        row.classList.remove("d-none");
      }else{
        toggleBtn.classList.add("active");
        mapView.classList.add("show");
        mapView.parentElement.style.overflowY = "hidden";
        row.classList.add("d-none");
      }

    });
  }
  if(btnOpenFilter){
    btnOpenFilter.addEventListener('click', e =>{
        let filters = document.querySelector('.block-filters')
        filters.classList.add('show')
    })
    closeFilter.addEventListener('click', e => {
        let filters = document.querySelector('.block-filters')
        filters.classList.remove('show')
    })
  }
};

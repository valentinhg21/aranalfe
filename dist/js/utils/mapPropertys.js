export const mapPropertys = () => {
  let mapElement = document.getElementById("mapview");
  const POINT_IMAGE = `${ajax_var.image}/point-map.png`;
  let lat = -34.6037;
  let long = -58.3816;
  let map = "";
  let zoom = 14;
  if (mapElement) {

    if(propertiesData.length > 0){
      lat = parseFloat(propertiesData[0].lat)
      long = parseFloat(propertiesData[0].lng)
    }

    map = L.map(mapElement, {
      scrollWheelZoom: true,
      zoomAnimation: true,
      zoomControl: true,
      zoomSnap: 1,
      inertia: false, // Opcional para que no haya scroll "suave"
      center: [lat, long],
      zoom: 14,
      zoomAnimationThreshold: 4
    }).setView([lat, long], zoom);
    // Add the MapTiler tile layer
    L.tileLayer(
      "https://api.maptiler.com/maps/streets/{z}/{x}/{y}.png?key=uBBj5vx85VpOncGtyuHq",
      {
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
      let body = document.querySelector('.theme')
      console.log(long)
      if(toggleBtn.classList.contains('active')){
        toggleBtn.classList.remove("active");
        mapView.classList.remove("show");
        mapView.parentElement.style.overflowY = "scroll";
        row.classList.remove("d-none");
        body.classList.remove('hidden');
      }else{
        toggleBtn.classList.add("active");
        mapView.classList.add("show");
        mapView.parentElement.style.overflowY = "hidden";
        row.classList.add("d-none");
        body.classList.add('hidden');
        map.setView([lat, long], zoom);
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

  // SELECT
  const location = document.getElementById('location-input');
  const searchSubmit = document.getElementById('search-location')
  if (location) {
    // 1. Extraer los location_id desde locations_data
    const location_ids = locations_data.map(loc => String(loc.location_id));


    // 2. Filtrar locations_theme según esos IDs
    const filteredOptions = locations_theme
      .filter(loc => location_ids.includes(String(loc.id))) // forzamos a string por si acaso
      .map(loc => ({
        value: loc.id,
        text: loc.name,
        optgroup: loc.parent_name
      }));

    // 3. Armar optgroups únicos desde los parent_name usados
    const optgroups = [...new Set(filteredOptions.map(opt => opt.optgroup))].map(name => ({
      value: name,
      label: name
    }));





    // 4. Inicializar Tom Select
    const settings = {
      plugins: ['remove_button'],
      persist: false,
      create: false,
      maxItems: null,
      options: filteredOptions,
      optgroups: optgroups,
      optgroupField: 'optgroup'
    };

    new TomSelect(location, settings);
  }

if (searchSubmit) {
  searchSubmit.addEventListener('click', e => {
    e.preventDefault();

    const items = document.querySelectorAll('.item');
    const currentParams = new URLSearchParams(window.location.search);

    // 🔁 Eliminar todos los localidad[] existentes
    [...currentParams.keys()].forEach(key => {
      if (key === 'localidad[]') currentParams.delete(key);
    });

    // ✅ Agregar nuevas localidades seleccionadas
    if (items.length > 0) {
      items.forEach(item => {
        currentParams.append("localidad[]", item.dataset.value);
      });
    }

    // 🌐 Definir baseURL
    const origin = window.location.origin;
    let baseURL = `${origin}/propiedades`;
    if (origin === "https://test.zetenta.com") {
      baseURL = `${origin}/aranalfe/propiedades`;
    }

    const finalURL = `${baseURL}/?${currentParams.toString()}`;
    window.open(finalURL, "_self");
  });
}
};

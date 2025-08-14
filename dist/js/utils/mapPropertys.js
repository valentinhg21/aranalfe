export const mapPropertys = () => {
  let mapElement = document.getElementById("mapview");
  const POINT_IMAGE = `${ajax_var.image}/point-map.png`;
  let lat = -34.6037;
  let long = -58.3816;
  let map = "";
  let zoom = 14;
  if (mapElement) {

    if (propertiesData.length > 0) {
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
      if (toggleBtn.classList.contains('active')) {
        toggleBtn.classList.remove("active");
        mapView.classList.remove("show");
        mapView.parentElement.style.overflowY = "scroll";
        row.classList.remove("d-none");
        body.classList.remove('hidden');
      } else {
        toggleBtn.classList.add("active");
        mapView.classList.add("show");
        mapView.parentElement.style.overflowY = "hidden";
        row.classList.add("d-none");
        body.classList.add('hidden');
        map.setView([lat, long], zoom);
      }

    });
  }
  if (btnOpenFilter) {
    btnOpenFilter.addEventListener('click', e => {
      let filters = document.querySelector('.block-filters')
      filters.classList.add('show')
    })
    closeFilter.addEventListener('click', e => {
      let filters = document.querySelector('.block-filters')
      filters.classList.remove('show')
    })
  }

  // SELECT - inicializar TomSelect en todos los location-input
  const locationInputs = document.querySelectorAll('.location-input');

  if (locationInputs.length) {
    // Mostrar los optgroups disponibles (debug)
    const optgroupDebug = [...new Set(locations_theme.map(loc => loc.parent_name))];
    console.log('Optgroups detectados:', optgroupDebug);

    // 1. Extraer location_ids
    const location_ids = locations_data.map(loc => String(loc.location_id));

    // 2. Forzar orden de optgroups según datos reales
    const optgroupNames = ['Capital Federal', 'Provincia de BsAs']; // asegurate que coincidan
    const optgroups = optgroupNames.map(name => ({
      value: name,
      label: name
    }));

    // 3. Opciones "Todos"
    const extraOptions = [
      {
        value: 'all_capital',
        text: 'Todos Capital Federal',
        optgroup: 'Capital Federal'
      },
      {
        value: 'all_provincia',
        text: 'Todos Provincia de BsAs',
        optgroup: 'Provincia de BsAs'
      }
    ];

    // 4. Opciones normales
    const filteredOptions = [
      ...extraOptions,
      ...locations_theme.map(loc => ({
        value: loc.id,
        text: loc.name,
        optgroup: loc.parent_name
      }))
    ];

    // 5. Inicializar TomSelect
    locationInputs.forEach(location => {
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
    });
  }

  // Botones buscar (search-location)
  const searchButtons = document.querySelectorAll('.search-location');

  searchButtons.forEach(searchSubmit => {
    searchSubmit.addEventListener('click', e => {
      e.preventDefault();

      const form = searchSubmit.closest('form') || document;
      const items = form.querySelectorAll('.item');
      const currentParams = new URLSearchParams(window.location.search);

      // Eliminar localidad[]
      currentParams.delete('localidad[]');

      if (items.length > 0) {
        items.forEach(item => {
          const val = item.dataset.value;
          console.log('Valor seleccionado:', val);

          if (val === 'all_capital') {
            const capitalItems = locations_theme.filter(loc => loc.parent_name === 'Capital Federal');
            console.log('Barrios de Capital:', capitalItems);
            capitalItems.forEach(loc => currentParams.append('localidad[]', loc.id));
          } else if (val === 'all_provincia') {
            const provinciaItems = locations_theme.filter(loc => loc.parent_name === 'Provincia de BsAs');
            console.log('Barrios de Provincia:', provinciaItems);
            provinciaItems.forEach(loc => currentParams.append('localidad[]', loc.id));
          } else {
            currentParams.append('localidad[]', val);
          }
        });
      }

      // Base URL
      const origin = window.location.origin;
      let baseURL = `${origin}/propiedades`;
      if (origin === "https://test.zetenta.com") {
        baseURL = `${origin}/aranalfe/propiedades`;
      }

      const finalURL = `${baseURL}/?${currentParams.toString()}`;
      console.log('Redireccionando a:', finalURL);
      window.location.href = finalURL;
    });
  });
  const pageResults = document.querySelector('.properties');
  if (pageResults) {
      const form = document.getElementById('filtros-form');
      const filtros = form.querySelectorAll('.button-filter');
      const limpiarBtn = document.getElementById('limpiar-filtros');

      // Reconstruir hidden inputs desde URL
      const urlParams = new URLSearchParams(window.location.search);

      filtros.forEach(filtro => {
          const param = filtro.dataset.param;
          const value = filtro.dataset.value;
          if (!param || !value) return;

          const name = param + '[]';
          const values = urlParams.getAll(name);

          if (values.includes(value)) {
              const label = filtro.closest('label');
              label.classList.add('checked');

              let hidden = form.querySelector(`input[type="hidden"][name="${name}"][value="${value}"]`);
              if (!hidden) {
                  hidden = document.createElement('input');
                  hidden.type = 'hidden';
                  hidden.name = name;
                  hidden.value = value;
                  form.appendChild(hidden);
              }
          }
      });

      // Toggle al click y actualizar hidden inputs
      filtros.forEach(filtro => {
          filtro.addEventListener('click', e => {
              e.preventDefault();
              const label = filtro.closest('label');
              label.classList.toggle('checked');

              const param = filtro.dataset.param;
              const value = filtro.dataset.value;
              if (!param || !value) return;

              const name = param + '[]';
              let hidden = form.querySelector(`input[type="hidden"][name="${name}"][value="${value}"]`);

              if (label.classList.contains('checked')) {
                  if (!hidden) {
                      hidden = document.createElement('input');
                      hidden.type = 'hidden';
                      hidden.name = name;
                      hidden.value = value;
                      form.appendChild(hidden);
                  }
              } else {
                  if (hidden) hidden.remove();
              }
          });
      });

      // Limpiar todos los filtros
      if (limpiarBtn) {
          limpiarBtn.addEventListener('click', e => {
              e.preventDefault();

              // Quitar checked visual
              filtros.forEach(filtro => {
                  filtro.closest('label').classList.remove('checked');
              });

              // Borrar todos los hidden inputs
              const hiddens = form.querySelectorAll('input[type="hidden"]');
              hiddens.forEach(input => input.remove());

              // Reset inputs de texto y radios (precio, sup, moneda, etc)
              const inputs = form.querySelectorAll('input[type="number"], input[type="radio"]');
              inputs.forEach(input => {
                  if (input.type === 'number') {
                      input.value = input.min || '';
                  } else if (input.type === 'radio') {
                      input.checked = false;
                  }
              });

              // Recargar página sin query params
              window.location.href = form.action || window.location.pathname;
          });
      }
  }
};

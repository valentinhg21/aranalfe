const inputTypeProperty = document.getElementById("input-type-property");
const inputTypeParentLocation = document.getElementById(
  "input-type-parentlocation"
);
const inputTypeLocation = document.getElementById("input-location");
const btnTypeOperationMobile = document.querySelectorAll(
  ".btn-select-operation"
);

let usuario_search_home = {
  type_operation: [1],
  type_property: [2],
  type_parent_location: [146],
  type_location: "",
};

const loadingSearch = (status) => {
  let loaderContainer = document.querySelector('.search')
  if(status){
    loaderContainer.classList.add('loading')
  }else{
    loaderContainer.classList.remove('loading')
  }
}
// FETCH APIS
async function getSearchSummaryAPI(params = {}) {
  const config = {
    api_token: 'fad0d191d200804e836be0b26626ac919fa37e8a',
    get_search_summary_url: 'https://www.tokkobroker.com/api/v1/property/get_search_summary/',
  };

  // Valores por defecto completos
  const defaultData = {
    current_localization_id: 0,
    current_localization_type: 'country',
    price_from: 1,
    price_to: 99999999999,
    operation_types: [1, 2, 3],
    property_types: Array.from({ length: 28 }, (_, i) => i + 1),
    currency: 'ANY',
    filters: [],
  };

  // Merge profundo simple para data
  const data = {
    ...defaultData,
    ...(params.data || {}),
    // En caso que params.data tenga filters, hacé merge con default
    filters: [
      ...(defaultData.filters || []),
      ...((params.data && params.data.filters) || [])
    ],
  };

  const dataJson = JSON.stringify(data);

  const query = new URLSearchParams({
    data: dataJson,
    key: config.api_token,
    format: 'json',
    lang: 'es_ar',
  });

  const url = `${config.get_search_summary_url}?${query.toString()}`;

  try {
    const response = await fetch(url, {
      method: 'GET',
      headers: { Accept: 'application/json' },
    });

    if (!response.ok) {
      console.error(`[TOKKO][SUMMARY] HTTP error: ${response.status}`);
      return [];
    }

    const json = await response.json();
    return json;
  } catch (error) {
    console.error('[TOKKO][SUMMARY] Fetch error:', error);
    return [];
  }
}

const fetchDataSearch = async (customParams = {}) => {
  try {
    
    const response = await fetch(
      `${ajax_var.url}?action=get_data_search`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(customParams),
      }
    );
    
    const result = await response.json();
   
    if (result.success) {
      return result.data;
    } else {
      console.error("Error en la respuesta:", result);
    }
  } catch (error) {
    console.error("Error en la petición:", error);
  }
};


const getDataSummary = async (customParams = {}, loading = false) => {
  if (loading) loadingSearch(true);

  const formData = new FormData();

  // Siempre enviar el action
  formData.append('action', 'get_search');

  // Agregar parámetros dinámicos
  if (customParams.operation_types) {
    customParams.operation_types.forEach(val => formData.append('operation_types[]', val));
  }
  if (customParams.property_types) {
    customParams.property_types.forEach(val => formData.append('property_types[]', val));
  }

  try {
    const response = await fetch(ajax_var.url, {
      method: 'POST',
      body: formData
    });

    const result = await response.json();

    if (loading) loadingSearch(false);

    if (result.success) {
      return result.data;
    } else {
      console.error('Error en la respuesta:', result);
    }
  } catch (error) {
    if (loading) loadingSearch(false);
    console.error('Error en la petición:', error);
  }
};

// HELPERS
const getIdsByString = (ids) => {
  return (idsArray = ids.split(",").map(Number));
};

const clickOptionsinputTypeProperty = () => {
  
  const listContainer = inputTypeProperty.nextElementSibling;
  let options = listContainer.querySelectorAll("p");
  options.forEach((option) => {
    option.addEventListener("click", (e) => {
      loadingSearch(true)
  
     
      inputTypeProperty.getAttribute("ids");
      let TypePropertyIds = inputTypeProperty.getAttribute("data-ids");
      usuario_search_home.type_property = getIdsByString(TypePropertyIds);
   
      if (usuario_search_home.type_property[0] !== 0) {
        getSearchSummaryAPI({
          data:{
          operation_types: usuario_search_home.type_operation,
          property_types: usuario_search_home.type_property,
          }
        }).then((data) => {
          
          changeValues(data.objects, listContainer, "TYPE_PROPERTY");
        });
        
      }
    });
  });
};

const clickOptionsinputParentLocation = () => {
  const listContainer = inputTypeParentLocation.nextElementSibling;
  const options = listContainer.querySelectorAll("p");

  options.forEach((option) => {
    option.addEventListener("click", () => {
      inputTypeLocation.value = "";
      inputTypeLocation.dataset.ids = "";

      const TypeParentLocationIds = inputTypeParentLocation.getAttribute("data-ids");
      usuario_search_home.type_parent_location = getIdsByString(TypeParentLocationIds);

      if (
        usuario_search_home.type_property[0] !== 0 ||
        usuario_search_home.type_parent_location[0] !== 0
      ) {
        const listLocation = document.querySelector(".list-type-location");
        const locations = listLocation.querySelectorAll("li");

        const parentNames = inputTypeParentLocation.value
          .split(",")
          .map((v) => v.trim())
          .filter((v) => v);

    
        if (locations.length > 0 && parentNames.length > 0) {
          let hayCoincidencias = false;

          locations.forEach((location) => {
         
            const parent = location.dataset.parent || "";
         
            if (location.classList.contains("no-results-location")) return;

            if (location.classList.contains("no-active")) {
              // Nunca mostrar
              location.classList.add("d-none");
              return;
            }
            console.log(parentNames.includes(parent))
            if (parentNames.includes(parent)) {
              location.classList.remove("d-none");
              hayCoincidencias = true;
                      
            } else {
              location.classList.add("d-none");
            }
          });

          const optionAll = document.querySelector(".autocomplete-custom .option-all");
          let liMensaje = document.querySelector(".autocomplete-custom .no-results-location");

          const visibles = [...locations].filter(location => {
            return (
              !location.classList.contains("d-none") &&
              !location.classList.contains("no-active") &&
              !location.classList.contains("no-results-location")
            );
          });

          if (visibles.length === 0) {
            if (optionAll) optionAll.remove();
            
            if (!liMensaje) {
              liMensaje = document.createElement("p");
              liMensaje.className = "no-results-location text-muted";
              liMensaje.textContent = "No hay ubicaciones para esta propiedad.";
              listLocation.appendChild(liMensaje);
      
            } else {
              liMensaje.classList.remove("d-none");
            }
          } else {
            if (optionAll) optionAll.classList.remove('d-none')
            if (liMensaje) liMensaje.remove();
          }

        } else {
          // Si no hay filtro, mostrar todo (excepto no-active)
          locations.forEach((location) => {
            if (
              !location.classList.contains("no-active") &&
              !location.classList.contains("no-results-location")
            ) {
              location.classList.remove("d-none");
            }
          });

          const liMensaje = document.querySelector(".autocomplete-custom .no-results-location");
          if (liMensaje) liMensaje.remove();

          const optionAll = document.querySelector(".autocomplete-custom .option-all");
          if (optionAll) optionAll.style.display = "block";
        }

        if (typeof autocomplete === "function") {
          autocomplete();
        }
      }
    });
  });
};



// BUILT
const changeValues = (data, container, method) => {
 
  let optionsPropertyTypes = document.querySelectorAll(".option-property-types");
  let optionsLocations = document.querySelectorAll(".option-location");
  let listLocationContainer = document.querySelector(".list-type-location");

  const locations = data.locations;
  

  if (method === "TYPE_OPERATION") {
   

    if(optionsLocations.length > 0){

      const parentSelect = document.querySelector('.option-property.select')
      parentSelect.click()
 
      parentSelect.parentElement.parentElement.parentElement.classList.remove('show')
      parentSelect.parentElement.parentElement.parentElement.parentElement.classList.remove('open-select')

      optionsLocations.forEach(option => {
    
        const type = option.firstElementChild.id;
        // Verificamos si existe algún property con id igual al type
        const match = locations.some(location => location.location_id == type);
        
   
        
    
   

        if (match) {
          option.classList.remove('no-active');
            setTimeout(() => {
              loadingSearch(false)
            }, 150);
        } else {
          option.classList.add('no-active');
            setTimeout(() => {
              loadingSearch(false)
            }, 150);
        }
      });
    }

  }

  if (method === "TYPE_PROPERTY") {
    if(optionsLocations.length > 0){
      const parentSelect = document.querySelector('.option-click-parent.select')
      inputTypeLocation.dataset.ids =""
      inputTypeLocation.value = ""
      setTimeout(() => {
      parentSelect.click()
      parentSelect.parentElement.parentElement.parentElement.classList.remove('show')
      parentSelect.parentElement.parentElement.parentElement.parentElement.classList.remove('open-select')
      }, 250);
  

     
      optionsLocations.forEach(option => {
   
        const type = option.firstElementChild.id;
        // Verificamos si existe algún property con id igual al type
        const match = locations.some(location => location.location_id == type);

        
        if (match) {
          option.classList.remove('no-active');
            
            setTimeout(() => {
              loadingSearch(false)
            }, 150);
        } else {
          option.classList.add('no-active');
            setTimeout(() => {
              loadingSearch(false)
            }, 150);
        }
      });
    }
  }



  selectMulti();
  autocomplete();

};

const toSlugArray = (value) => {
  return value
    .split(",")
    .map(v => v.trim())
    .filter(Boolean)
    .map(v => v.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, ''));
}



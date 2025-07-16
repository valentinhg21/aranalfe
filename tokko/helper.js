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
// FETCH APIS
// console.log(ajax_var.url)
const fetchDataSearch = async (customParams = {}) => {
  try {
    const response = await fetch(
      `${ajax_var.url}?action=get_search`,
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

const getDataSummary = async (customParams = {}) => {
  try {
    const response = await fetch(
      `${ajax_var.url}?action=get_search`,
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



// HELPERS
const getIdsByString = (ids) => {
  return (idsArray = ids.split(",").map(Number));
};

const clickOptionsinputTypeProperty = () => {
  const listContainer = inputTypeProperty.nextElementSibling;
  let options = listContainer.querySelectorAll("p");
  options.forEach((option) => {
    option.addEventListener("click", (e) => {
      // inputTypeParentLocation.value = "Ubicación";
      // inputTypeLocation.value = "";
      inputTypeProperty.getAttribute("ids");
      let TypePropertyIds = inputTypeProperty.getAttribute("data-ids");
      usuario_search_home.type_property = getIdsByString(TypePropertyIds);

      // if (usuario_search_home.type_property[0] !== 0) {
      //   fetchDataSearch({
      //     operation_types: usuario_search_home.type_operation,
      //     property_types: usuario_search_home.type_property,
      //   }).then((data) => {
      //     changeValues(data, listContainer, "TYPE_PROPERTY");
      //   });
      // }
    });
  });
};

const clickOptionsinputParentLocation = () => {
  // const listContainer = inputTypeParentLocation.nextElementSibling;
  // let options = listContainer.querySelectorAll("p");

  // options.forEach((option) => {
  //   option.addEventListener("click", (e) => {
  //     // inputTypeLocation.value = "";
  //     inputTypeParentLocation.getAttribute("ids");
  //     let TypeParentLocationIds =
  //       inputTypeParentLocation.getAttribute("data-ids");
  //     usuario_search_home.type_parent_location = getIdsByString(
  //       TypeParentLocationIds
  //     );
  //     if (
  //       usuario_search_home.type_property[0] !== 0 ||
  //       usuario_search_home.type_parent_location[0] !== 0
  //     ) {
  //       const listLocation = document.querySelector(".list-type-location");
  //       const locations = listLocation.querySelectorAll("li");

  //       if (locations.length > 0) {
  //         const parentNames = inputTypeParentLocation.value
  //           .split(",")
  //           .map((v) => v.trim())
  //           .filter((v) => v);

     
  //         if (inputTypeParentLocation.value.length > 0) {
  //           locations.forEach((location) => {
  //             const parent = location.dataset.parent;
  //             if (parentNames.includes(parent)) {
  //               location.classList.remove("d-none");
  //             } else {
  //               location.classList.add("d-none");
  //             }
  //           });
  //         } else {
  //           locations.forEach((location) => {
  //             location.classList.remove("d-none");
  //           });
  //         }
  //       }
  //     }
  //   });
  // });
};

// BUILT
// const changeValues = (data, container, method) => {
//   let optionsPropertyTypes = document.querySelector(".option-property-types");
//   let listParentContainer = document.querySelector(".list-type-parentlocation");
//   let listLocationContainer = document.querySelector(".list-type-location");
//   if (method === "TYPE_OPERATION") {
//     const property_types = data.property_types;
//     const parent_locations = data.parent_locations;
//     const locations = data.locations;
//     let propertyHTML = "";
//     let parentHTML = "";
//     let locationHTML = "";
    
//     // console.log(optionsPropertyTypes)
//     // Limpiar los contenedores antes de agregar los nuevos elementos
//     // listPropertyContainer.innerHTML = "";
//     // listParentContainer.innerHTML = "";
//     // listLocationContainer.innerHTML = "";

//     // if (property_types.length > 0) {
//     //   property_types.forEach((property) => {
//     //     propertyHTML += `<li class="options-list-select"><p id="${property.id}">${property.type}</p></li>`;
//     //   });
//     //   listPropertyContainer.innerHTML = propertyHTML;
//     // }

//     // if (parent_locations.length > 0) {
//     //   parent_locations.forEach((parent) => {
//     //     parentHTML += `<li class="options-list-select"><p id="${parent.parent_id}">${parent.parent_name}</p></li>`;
//     //   });
//     //   listParentContainer.innerHTML = parentHTML;
//     // }

//     // if (locations.length > 0) {
//     //   locations.forEach((location) => {
//     //     locationHTML += `<li class="options-list-select" data-parent="${location.parent_name}"><p id="${location.location_id}">${location.location_name}</p></li>`;
//     //   });
//     //   listLocationContainer.innerHTML = locationHTML;
//     // }
//   }

//   // if (method === "TYPE_PROPERTY") {
//   //   const parent_locations = data.parent_locations;
//   //   const locations = data.locations;
//   //   listParentContainer.innerHTML = "";
//   //   listLocationContainer.innerHTML = "";

//   //   let parentHTML = "";
//   //   let locationHTML = "";

//   //   if (parent_locations.length > 0) {

//   //     parent_locations.forEach((parent) => {
//   //       parentHTML += `<li class="options-list-select"><p id="${parent.parent_id}">${parent.parent_name}</p></li>`;
//   //     });
//   //     listParentContainer.innerHTML = parentHTML;
//   //   }

//   //   if (locations.length > 0) {
  


//   //     locations.forEach((location) => {
//   //       locationHTML += `<li class="options-list-select" data-parent="${location.parent_name}"><p id="${location.location_id}">${location.location_name}</p></li>`;
//   //     });
//   //     listLocationContainer.innerHTML = locationHTML;
//   //   }
//   // }

//   // if (method === "TYPE_PARENTLOCATION") {
//   //   const locations = data.locations;
//   //   listLocationContainer.innerHTML = "";
//   //   let locationHTML = "";
//   //   if (locations.length > 0) {
//   //     locations.forEach((location) => {
//   //       locationHTML += `<li class="options-list-select" data-parent="${location.parent_name}"><p id="${location.location_id}">${location.location_name}</p></li>`;
//   //     });
//   //     listLocationContainer.innerHTML = locationHTML;
//   //   }
//   // }

//   selectMulti();
//   autocomplete();
//   clickOptionsinputTypeProperty();
//   clickOptionsinputParentLocation();
// };

const toSlugArray = (value) => {
  return value
    .split(",")
    .map(v => v.trim())
    .filter(Boolean)
    .map(v => v.toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, ''));
}
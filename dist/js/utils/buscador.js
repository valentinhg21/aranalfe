export const buscador = (selectMulti, autocomplete) => {

  //  SELECT TIPO OPERACION

  const inputTypeOperation = document.getElementById("input-type-operation");

  const buttonSearch = document.getElementById("search-button");



  if (inputTypeOperation) {

    const listContainer = inputTypeOperation.nextElementSibling;

    let options = listContainer.querySelectorAll("p");

    options.forEach((option) => {

      option.addEventListener("click", (e) => {

        if (option.dataset.type === "1") {

          // inputTypeProperty.value = "Tipo de propiedad";

          // inputTypeParentLocation.value = "";

          // inputTypeLocation.value = "";

          usuario_search_home.type_operation = [1];

          fetchDataSearch({

            operation_types: [1],

          }).then((data) => {

            changeValues(data, listContainer, "TYPE_OPERATION");

          });

        } else {

          // inputTypeProperty.value = "Tipo de propiedad";

          // inputTypeParentLocation.value = "Ubicación";

          // inputTypeLocation.value = "";

          usuario_search_home.type_operation = [2];

          fetchDataSearch({

            operation_types: [2],

          }).then((data) => {

            changeValues(data, listContainer, "TYPE_OPERATION");

          });

        }

      });

    });

  }



  if (btnTypeOperationMobile.length > 0) {

    btnTypeOperationMobile.forEach((btn) => {

      btn.addEventListener("click", (e) => {

        btnTypeOperationMobile.forEach((btn) => {

          btn.classList.remove("active");

        });

        btn.classList.add("active");

        let currentOperation = Number(btn.getAttribute("data-type")) || 1;

        usuario_search_home.type_operation = [currentOperation];

        fetchDataSearch({

          operation_types: [currentOperation],

        }).then((data) => {

          changeValues(data, "", "TYPE_OPERATION");

        });

      });

    });

  }



  if (inputTypeProperty) {

    clickOptionsinputTypeProperty();

  }



  if (inputTypeParentLocation) {

    clickOptionsinputParentLocation();

  }



  if (buttonSearch) {

      let ERROR = true;

    buttonSearch.addEventListener("click", (e) => {

      e.preventDefault();

      let TypePropertyContainer = inputTypeProperty.parentElement;

      let TypeParentLocationContainer = inputTypeParentLocation.parentElement;

      let TypeLocationContainer = inputTypeLocation.parentElement;

    

      if (validator.isEmpty(inputTypeProperty.value)) {

        TypePropertyContainer.classList.add("error");

        inputTypeProperty.placeholder = "Seleccionar";

        ERROR = true;

      } else {

        TypePropertyContainer.classList.remove("error");

        ERROR = false;

      }

      if (validator.isEmpty(inputTypeParentLocation.value)) {

        TypeParentLocationContainer.classList.add("error");

        inputTypeParentLocation.placeholder = "Seleccionar";

        ERROR = true;

      } else {

        TypeParentLocationContainer.classList.remove("error");

        ERROR = false;

      }

      if (validator.isEmpty(inputTypeLocation.value)) {

        TypeLocationContainer.classList.add("error");

        inputTypeLocation.placeholder = "Seleccionar";

        ERROR = true;

      } else {

        TypeLocationContainer.classList.remove("error");

        ERROR = false;

      }

      if(!ERROR){

        const operationValue = usuario_search_home.type_operation[0]; // asumiendo que solo usás el primero

        const URL_TYPE_OPERATION = operationValue === 1 ? 'ventas' : (operationValue === 2 ? 'alquiler' : '');

        const URL_TYPE_PROPERTY = toSlugArray(inputTypeProperty.value).join('-');

        const URL_TYPE_PARENT = toSlugArray(inputTypeParentLocation.value).join('-');

        const URL_TYPE_LOCATION = toSlugArray(inputTypeLocation.value).join('-');

        const baseURL = `${window.location.origin}/propiedades`;

        //  const baseURL = `${window.location.origin}/aranalfe/propiedades`;

        const finalURL = `${baseURL}/${URL_TYPE_OPERATION}/${URL_TYPE_PROPERTY}/${URL_TYPE_PARENT}/${URL_TYPE_LOCATION}`;

        window.open(finalURL, '_blank');



      }

    });



 

  }

};


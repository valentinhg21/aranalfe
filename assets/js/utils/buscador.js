export const buscador = (selectMulti, autocomplete) => {
  //  SELECT TIPO OPERACION
  const inputTypeOperation = document.getElementById("input-type-operation");
  const buttonSearch = document.getElementById("search-button");

  if(document.querySelector('.search')){
    getSearchSummaryAPI({
      data: {
        operation_types: [1],
        property_types: [inputTypeProperty.dataset.ids],
      }
    }).then((data) => {
      changeValues(data.objects, "", "TYPE_OPERATION");
    });
  }

  if (inputTypeOperation) {
    const listContainer = inputTypeOperation.nextElementSibling;

    let options = listContainer.querySelectorAll("p");

    options.forEach((option) => {
      option.addEventListener("click", (e) => {
        if (option.dataset.type === "1") {
          usuario_search_home.type_operation = [1];
          getSearchSummaryAPI({
            data: {
            operation_types: [1],
            property_types: [inputTypeProperty.dataset.ids],
            }
          }).then((data) => {
            changeValues(data.objects, "", "TYPE_OPERATION");
          });
        } else {
          usuario_search_home.type_operation = [2];
          getSearchSummaryAPI({
            data: {
            operation_types: [2],
            property_types: [inputTypeProperty.dataset.ids],
            }
          }).then((data) => {
            changeValues(data.objects, listContainer, "TYPE_OPERATION");
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
        getSearchSummaryAPI({
          data: {
            operation_types: [currentOperation],
            property_types: [inputTypeProperty.dataset.ids],
          }
        }).then((data) => {
          changeValues(data.objects, "", "TYPE_OPERATION");
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
      if (!ERROR) {
        function toSlugArrayParams(str) {
          return str
            ? str
                .split(",")
                .map((s) => s.trim())
                .filter(Boolean)
            : [];
        }
        let origin = window.location.origin;
        let baseURL = `${origin}/propiedades`;
        if (origin === "https://test.zetenta.com") {
          baseURL = `${origin}/aranalfe/propiedades`;
        } else {
          baseURL = `${origin}/propiedades`;
        }
        const params = new URLSearchParams();
        let optionAllLocation =
          inputTypeLocation.parentElement.querySelector(
            ".option-all"
          )?.firstElementChild;
        const operationValue = usuario_search_home.type_operation[0];
        if (operationValue === 1 || operationValue === 2) {
          params.append("operacion", operationValue.toString());
        }
        toSlugArrayParams(inputTypeProperty.dataset.ids).forEach((id) => {
          params.append("tipo", id);
        });
        if (validator.isEmpty(inputTypeLocation.value)) {
          const elementos = document.querySelectorAll(
            `li.options-list-select[data-parent="${inputTypeParentLocation.value}"]`
          );

      
          if (elementos.length > 0) {
            elementos.forEach((el) => {
              if(!el.classList.contains('no-active') || !el.classList.contains('d-none')){
            
                params.append("localidad[]", el.firstElementChild.id);
              }
             
            });
          }
        }
        if (optionAllLocation.classList.contains("select")) {
          toSlugArrayParams(inputTypeLocation.dataset.ids).forEach((id) => {
            params.append("localidad[]", id);
          });
        } else {
          toSlugArrayParams(inputTypeLocation.dataset.ids).forEach((id) => {
            params.append("localidad[]", id);
          });
        }
        let paramsQuery = `?${params.toString()}`;
        const finalURL = `${baseURL}${paramsQuery}`;
        window.open(finalURL, "_blank");
      }
    });
  }
};

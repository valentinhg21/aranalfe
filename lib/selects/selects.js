const selectSimple = () => {
    const selects = document.querySelectorAll(".select-simple-list");
    if (selects.length > 0) {
        selects.forEach((select) => {
          const inputSelect = select.querySelector("input");
          if (inputSelect) {
            const listContainer = select.querySelector(".list-select");
            const opciones = listContainer.querySelectorAll("p");
            if (opciones.length > 8) {
              listContainer.classList.add("long");
            }
            
            opciones.forEach((op) => {
              op.addEventListener("click", (e) => {
                inputSelect.value = op.textContent;
                inputSelect.dataset.ids = op.id
                opciones.forEach((op) => {
                  op.classList.remove("select");
                   listContainer.classList.toggle("show");
                    inputSelect.parentElement.classList.add('open-select')
                });

                op.classList.add("select");

              });
            });
            
            inputSelect.parentElement.addEventListener("click", () => {
              listContainer.classList.toggle("show");
              inputSelect.parentElement.classList.add('open-select')
            });
            
            window.addEventListener("click", (e) => {
              let target = e.target;
              if (!select.contains(target)) {
                listContainer.classList.remove("show");
                inputSelect.parentElement.classList.remove('open-select')
              }
            });
      
            // let typedLetters = "";
            // window.addEventListener("keydown", (e) => {
            //   let searchTerm = e.key.toLowerCase();
            //   typedLetters += searchTerm;
            //   if (searchTerm === "backspace") {
            //     typedLetters = "";
            //   }
            //   var matchingOption = Array.from(opciones).find((option) => {
            //     var optionText = option.textContent.toLowerCase();
            //     if (typedLetters.length > 3) {
            //       return optionText.includes(typedLetters);
            //     }
            //   });
      
            //   if (matchingOption) {
            //     opciones.forEach((op) => {
            //       op.classList.remove("select");
            //     });
      
            //     matchingOption.classList.add("select");
            //     listContainer.scrollTop =
            //       matchingOption.offsetTop - listContainer.offsetTop;
            //     typedLetters = "";
            //   }
            // });
      
            // Seleccionar el primer valor de las opciones
            inputSelect.value = opciones[0].textContent;
            opciones[0].classList.add("select");
          }
        });
    }
}

const selectMulti = () => {
  const multiSelects = document.querySelectorAll(".select-multi-list");

  multiSelects.forEach((select) => {
    const inputSelect = select.querySelector("input");
    const listContainer = select.querySelector(".list-select");
    if (!inputSelect || !listContainer) return;

    const prevIds = inputSelect.dataset.ids ? inputSelect.dataset.ids.split(",") : [];
    const prevTexts = inputSelect.value ? inputSelect.value.split(",").map(s => s.trim()) : [];

    // Detectar si "Todos" estaba seleccionado antes
    const wasAllSelected = listContainer.querySelector("p#all")?.classList.contains("select");

    // Reemplazar contenedor para evitar duplicar listeners
    const newListContainer = listContainer.cloneNode(true);
    listContainer.parentNode.replaceChild(newListContainer, listContainer);

    // Eliminar opción "Todos" duplicada si ya existe
    const oldAll = newListContainer.querySelector('p#all');
    if (oldAll) oldAll.closest('li').remove();

    // Crear opción "Todos" al principio
    const allOption = document.createElement("li");
    allOption.classList.add("options-list-select", "option-all");
    allOption.innerHTML = `<p id="all">Todos</p>`;
    newListContainer.prepend(allOption);

    const newOpciones = newListContainer.querySelectorAll("p");
    const allOptionElement = newListContainer.querySelector("p#all");

    const selectedValues = new Set();
    const selectedValuesId = new Set();

    // Restaurar valores seleccionados
    newOpciones.forEach((op) => {
      if (op.id !== "all" && prevIds.includes(op.id)) {
        op.classList.add("select");
        selectedValues.add(op.textContent.trim());
        selectedValuesId.add(op.id);
      }
    });

    // Restaurar clase "select" a Todos si estaba seleccionada
    if (wasAllSelected) {
      allOptionElement.classList.add("select");
    }

    const updateInput = () => {
      const selectedArray = Array.from(selectedValues);
      const selectedArrayId = Array.from(selectedValuesId);
      inputSelect.value = selectedArray.length === 0 ? '' : selectedArray.join(", ");
      inputSelect.dataset.ids = selectedArrayId.length === 0 ? "" : selectedArrayId.join(",");
    };
    updateInput();

    // Opción "Todos"
    allOptionElement.addEventListener("click", () => {
      const isAllSelected = allOptionElement.classList.contains("select");
      newOpciones.forEach((op) => {
        if (op.id === "all") return;
        if (isAllSelected) {
          op.classList.remove("select");
          selectedValues.delete(op.textContent.trim());
          selectedValuesId.delete(op.id);
        } else {
          op.classList.add("select");
          selectedValues.add(op.textContent.trim());
          selectedValuesId.add(op.id);
        }
      });

      if (isAllSelected) {
        allOptionElement.classList.remove("select");
      } else {
        allOptionElement.classList.add("select");
      }

      updateInput();
    });

    // Opciones individuales
    newOpciones.forEach((op) => {
      if (op.id === "all") return;

      op.addEventListener("click", () => {
        const value = op.textContent.trim();
        if (op.classList.contains("select")) {
          op.classList.remove("select");
          selectedValues.delete(value);
          selectedValuesId.delete(op.id);
        } else {
          op.classList.add("select");
          selectedValues.add(value);
          selectedValuesId.add(op.id);
        }

        // Actualizar "Todos"
        const total = newListContainer.querySelectorAll("p:not(#all)").length;
        const selected = newListContainer.querySelectorAll("p.select:not(#all)").length;
        if (total === selected) {
          allOptionElement.classList.add("select");
        } else {
          allOptionElement.classList.remove("select");
        }

        updateInput();
      });
    });

    inputSelect.addEventListener("click", () => {
      newListContainer.classList.toggle("show");
    });

    window.addEventListener("click", (e) => {
      if (!select.contains(e.target)) {
        newListContainer.classList.remove("show");
      }
    });

    if (newOpciones.length > 8) {
      newListContainer.classList.add("long");
    }
  });
};


const autocomplete = () => {
  const autoComplete = document.querySelectorAll(".autocomplete-custom");

  if (autoComplete.length > 0) {
    autoComplete.forEach((complete) => {
      const inputDisplay = complete.querySelector("#input-location");
      const inputFilter = complete.querySelector("#search-location");

      if (inputDisplay && inputFilter) {
        const listContainer = complete.querySelector(".results");

        // Verificar si hay al menos un <li> visible y activo
        const hayActivos = Array.from(listContainer.querySelectorAll("li")).some(li =>
          !li.classList.contains("d-none") &&
          !li.classList.contains("no-active") &&
          li.style.display !== "none"
        );

        // Crear opción "Todos" solo si hay elementos activos
        let allLi = listContainer.querySelector(".option-all");
        if (allLi) allLi.remove();

        if (hayActivos) {
         
          allLi = document.createElement("li");
          allLi.classList.add("options-list-select", "option-all");
          allLi.innerHTML = `<p id="all">Todos</p>`;
          listContainer.insertBefore(allLi, listContainer.firstChild);
        }

        const opciones = Array.from(listContainer.querySelectorAll("p"));
        const allOption = listContainer.querySelector("p#all");

        if (opciones.length > 8) {
          listContainer.classList.add("long");
        }

        const selectedValues = new Set();
        const selectedValuesId = new Set();

        const updateDisplayInput = () => {
          inputDisplay.value = Array.from(selectedValues).join(", ");
          inputDisplay.dataset.ids = Array.from(selectedValuesId).join(",");
        };

        const showAllOptions = () => {
          opciones.forEach((op) => (op.style.display = "block"));
        };

        inputFilter.addEventListener("focus", () => {
          showAllOptions();
          listContainer.classList.add("show");
        });

        inputFilter.addEventListener("input", () => {
          const term = inputFilter.value.toLowerCase().trim();
          opciones.forEach((op) => {
            const texto = op.textContent.toLowerCase();
            op.style.display = texto.includes(term) || op.id === "all" ? "block" : "none";
          });
          listContainer.classList.add("show");
        });

        // "Todos"
        if (allOption) {
          allOption.addEventListener("click", () => {
            const isAllSelected = allOption.classList.contains("select");

            selectedValues.clear();
            selectedValuesId.clear();

            if (!isAllSelected) {
              opciones.forEach((op) => {
                if (
                  op.id === "all" ||
                  op.parentElement.classList.contains("d-none") ||
                  op.parentElement.classList.contains("no-active") ||
                  op.parentElement.style.display === "none"
                ) return;

                selectedValues.add(op.textContent.trim());
                selectedValuesId.add(op.id);
              });
            }

            allOption.classList.toggle("select", !isAllSelected);

            opciones.forEach((op) => {
              if (op.id !== "all") op.classList.remove("select");
            });

            updateDisplayInput();
            inputFilter.value = "";
            showAllOptions();
            listContainer.classList.remove("show");
          });
        }

        // Opciones individuales
        opciones.forEach((op) => {
          if (op.id === "all") return;
          op.addEventListener("click", () => {
            const valor = op.textContent.trim();

            if (selectedValues.has(valor)) {
              selectedValues.delete(valor);
              selectedValuesId.delete(op.id);
              op.classList.remove("select");
            } else {
              selectedValues.add(valor);
              selectedValuesId.add(op.id);
              op.classList.add("select");
            }

            const visibles = opciones.filter(op =>
              op.id !== "all" &&
              !op.parentElement.classList.contains("d-none") &&
              !op.parentElement.classList.contains("no-active") &&
              op.parentElement.style.display !== "none"
            );
            const seleccionadas = visibles.filter(op =>
              op.classList.contains("select")
            );

            if (allOption) {
              allOption.classList.toggle("select", visibles.length === seleccionadas.length);
            }

            updateDisplayInput();
            inputFilter.value = "";
            showAllOptions();
            listContainer.classList.remove("show");
          });
        });

        window.addEventListener("click", (e) => {
          if (!complete.contains(e.target)) {
            listContainer.classList.remove("show");
            showAllOptions();
          }
        });

        inputDisplay.addEventListener("click", () => {
          showAllOptions();
          listContainer.classList.add("show");
          inputFilter.focus();
        });
      }
    });
  }
};





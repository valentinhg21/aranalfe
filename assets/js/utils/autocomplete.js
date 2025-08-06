export const autocomplete = () => {
  const autoComplete = document.querySelectorAll(".autocomplete-custom");

  if (autoComplete.length > 0) {
    autoComplete.forEach((complete) => {
      const inputDisplay = complete.querySelector("#input-location");
      const inputFilter = complete.querySelector("#search-location");
      if (inputDisplay && inputFilter) {
        const listContainer = complete.querySelector(".results");
        const opciones = Array.from(listContainer.querySelectorAll("p"));

        if (opciones.length > 8) {
          listContainer.classList.add("long");
        }

        const selectedValues = new Set();

        const updateDisplayInput = () => {
          inputDisplay.value = Array.from(selectedValues).join(", ");
        };

        const showAllOptions = () => {
          opciones.forEach((op) => (op.style.display = "block"));
        };

        // Mostrar lista al hacer foco en el input de búsqueda
        inputFilter.addEventListener("focus", () => {
          showAllOptions();
          listContainer.classList.add("show");
        });

        // Filtrar opciones mientras escribe
        inputFilter.addEventListener("input", () => {
          const term = inputFilter.value.toLowerCase().trim();

          opciones.forEach((op) => {
            const texto = op.textContent.toLowerCase();
            op.style.display = texto.includes(term) ? "block" : "none";
          });

          listContainer.classList.add("show");
        });

        // Selección
        opciones.forEach((op) => {
          op.addEventListener("click", () => {
            const valor = op.textContent.trim();

            if (selectedValues.has(valor)) {
              selectedValues.delete(valor);
              op.classList.remove("select");
            } else {
              selectedValues.add(valor);
              op.classList.add("select");
            }

            updateDisplayInput();
            inputFilter.value = ""; // limpiar input de búsqueda
            showAllOptions(); // restaurar visibilidad
            listContainer.classList.remove("show");
          });
        });

        // Cerrar lista al hacer clic fuera
        window.addEventListener("click", (e) => {
          if (!complete.contains(e.target)) {
            listContainer.classList.remove("show");
            showAllOptions();
          }
        });
          inputDisplay.addEventListener("click", () => {
            showAllOptions();
            listContainer.classList.add("show");
            inputFilter.focus(); // opcional
        });
      }
    });
  }
};


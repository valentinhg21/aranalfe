export const selectMulti = () => {
  const multiSelects = document.querySelectorAll(".select-multi-list");

  if (multiSelects.length > 0) {
    multiSelects.forEach((select) => {
      const inputSelect = select.querySelector("input");
 
      if (inputSelect) {
        const listContainer = select.querySelector(".list-select");
        const opciones = listContainer.querySelectorAll("p");
     
        const selectedValues = new Set();

        if (opciones.length > 8) {
          listContainer.classList.add("long");
        }

        // Seleccionar el primer valor por defecto
        const firstValue = opciones[0].textContent.trim();
        opciones[0].classList.add("select");
        selectedValues.add(firstValue);
        inputSelect.value = firstValue;

        opciones.forEach((op) => {
          op.addEventListener("click", (e) => {
            const value = op.textContent.trim();

            if (op.classList.contains("select")) {
              op.classList.remove("select");
              selectedValues.delete(value);
            } else {
              op.classList.add("select");
              selectedValues.add(value);
            }

            const selectedArray = Array.from(selectedValues);
            inputSelect.value =
              selectedArray.length === 0
                ? ''
                : selectedArray.join(", ");
          });
        });

        inputSelect.addEventListener("click", () => {
          listContainer.classList.toggle("show");
        
        });

        window.addEventListener("click", (e) => {
          if (!select.contains(e.target)) {
            listContainer.classList.remove("show");
          }
        });
      }
    });
  }
};

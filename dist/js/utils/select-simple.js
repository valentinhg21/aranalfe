export const selectSimple = () => {
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
                listContainer.classList.remove("show");
                opciones.forEach((op) => {
                  op.classList.remove("select");
                });
                op.classList.add("select");
              });
            });
            
            inputSelect.addEventListener("click", () => {
              listContainer.classList.toggle("show");
            });
            
            window.addEventListener("click", (e) => {
              let target = e.target;
              if (!select.contains(target)) {
                listContainer.classList.remove("show");
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
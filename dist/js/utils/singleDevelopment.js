export const singleDevelopment = () => {
  // GALERIA
  const gallery = document.getElementById("galeriaDevelopment");
  const btnPlanos = document.querySelectorAll(".btn-plano");
  const selectPisos = document.querySelectorAll(".select-piso");
  const planos = document.querySelectorAll('.plano');
  const btnClose = document.querySelector('.btn-close');
  const btnUnidades = document.querySelectorAll('.btn-unidad');
  const planosSplide = document.querySelectorAll('.splide-planos')
  if (gallery) {
    new Splide(gallery, {
      type: "slide",
      perPage: 1,
      gap: "1rem",
      arrows: true,
      pagination: false,
    }).mount();

    Fancybox.bind("[data-fancybox='galeria']", {
      Toolbar: {
        display: {
          left: ["infobar"],
          middle: [],
          right: ["slideshow", "download", "thumbs", "close"],
        },
      },
    });
  }
  if (btnPlanos.length > 0) {
    btnPlanos.forEach((btn) => {
        btn.addEventListener('click', e => {
            let modal = document.querySelector('.modal')
            modal.classList.add('show')
        })
 
    });
  }
  if(planos.length > 0){
    Fancybox.bind("[data-fancybox='piso-galeria']", {
      Toolbar: {
        display: {
          left: ["infobar"],
          middle: [],
          right: ["slideshow", "download", "thumbs", "close"],
        },
      },
    });
  }
  selectPisos.forEach((piso) => {
    
    planos[0].classList.remove('d-none')
    piso.addEventListener("click", (e) => {
        planos.forEach(plano => {
            plano.classList.add('d-none')
        });
        
        let optionPiso = piso.firstElementChild.dataset.type
        let errorMsg = document.querySelector('.error-msg')
        let image = document.getElementById(`${optionPiso}`)
        if(image){
            image.classList.remove('d-none')
            errorMsg.classList.add('d-none')
        }else{
            errorMsg.classList.remove('d-none')
        }
    
    });
  });
  if(btnClose){
    btnClose.addEventListener('click', e => {
        let modal = btnClose.parentElement.parentElement
        modal.classList.remove('show')
    })
  }

  if(btnUnidades.length > 0){
    const tabPanel = document.querySelectorAll('.tab-panel')
    btnUnidades.forEach(btn => {
        btn.addEventListener('click', e => {
            btnUnidades.forEach(btn => {
                btn.classList.remove('active')
            });
            tabPanel.forEach(panel => {
                panel.classList.remove('active')
            });
            let id = btn.firstElementChild.dataset.panel
            let panel = document.getElementById(id);
         
            if(panel){
                panel.classList.add('active')
                btn.classList.add('active')
            }
        })
    });
  }

  if(planosSplide.length > 0){
   
    planosSplide.forEach((planos) => {
      new Splide(planos, {
        type: "slide",
        perPage: 1,
        gap: "1rem",
        arrows: true,
        pagination: false,
      }).mount();
    })
  }

  const section_five = document.querySelector('.section-5');
  if(section_five){
      const selectContainer = document.querySelector('.select-unidades');
  const input = document.getElementById('select-unidades-input');
  const listSelect = selectContainer.querySelector('.list-select');
  const options = listSelect.querySelectorAll('li.options-list-select p');

  // Abrir/cerrar lista al click en input o icono
  selectContainer.querySelector('.field-container-input__icon').addEventListener('click', toggleList);
  input.addEventListener('click', toggleList);

  function toggleList() {
    listSelect.classList.toggle('open');
  }

  // Seteo inicial del input
  input.value = 'Ver todos';

  // Al seleccionar una opción
  options.forEach(option => {
    option.addEventListener('click', () => {
      const panelId = option.getAttribute('data-panel');
      const text = option.textContent.trim();

      // Actualiza input
      input.value = text;

      // Cierra la lista
      listSelect.classList.remove('open');

      // Cambia tab activa
      activarTab(panelId);
    });
  });

  function activarTab(id) {
    // Tabs buttons
    document.querySelectorAll('.tabs-nav .btn-unidad').forEach(btn => {
      const p = btn.querySelector('p');
      if (p.getAttribute('data-panel') === id) {
        btn.classList.add('active');
      } else {
        btn.classList.remove('active');
      }
    });

    // Tabs content
    document.querySelectorAll('.tab.tab-panel').forEach(tab => {
      if (tab.id === id) {
        tab.classList.add('active');
      } else {
        tab.classList.remove('active');
      }
    });
  }
  }
 

};

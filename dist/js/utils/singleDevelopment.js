export const singleDevelopment = () => {
  // GALERIA
  const gallery = document.getElementById("galeriaDevelopment");
  const btnPlanos = document.querySelectorAll(".btn-plano");
  const selectPisos = document.querySelectorAll(".select-piso");
  const planos = document.querySelectorAll('.plano');
  const btnClose = document.querySelector('.btn-close');
  const btnUnidades = document.querySelectorAll('.btn-unidad');
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
            console.log(id)
            if(panel){
                panel.classList.add('active')
                btn.classList.add('active')
            }
        })
    });
  }

};

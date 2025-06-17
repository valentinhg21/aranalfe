export const singleProperty = () => {
let btnViews = document.querySelectorAll('.btn-view');
let btnImage = document.querySelector('.btn-open-image')
let btnVideo = document.querySelector('.btn-open-video')
let btnMap = document.querySelector('.btn-open-single-map');
let containerVideoMap = document.querySelectorAll('.single-video-map');
let heroSplide = document.getElementById('hero-splide');
let btnOpenRequest = document.getElementById('openFormRequest');
let closeOpenRequest = document.getElementById('closeFormRequest')
if(heroSplide){
  new Splide("#hero-splide", {
    type: "slide",
    perPage: 3,
    gap: "1rem",
    arrows: false,
    pagination: false,
    drag: false,       // 🔒 desactiva el swipe
    keyboard: false,   // 🔒 desactiva teclado
    breakpoints: {
      878.5: {
        gap: "0rem",
        perPage: 1,
        arrows: true,
        pagination: false,
        drag: true,     // ✅ activa swipe en mobile
        keyboard: true, // ✅ activa teclado en mobile
      },
    },
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


if(btnViews.length > 0){
  btnViews.forEach(btn => {
    btn.addEventListener('click', e => {
      btnViews.forEach(btn => {
          btn.classList.remove('active')
      });
      btn.classList.add('active')
    })
  
  });
}

if(btnImage){
  btnImage.addEventListener('click', () =>{
   containerVideoMap.forEach(container => {
        container.classList.remove('show')
    });
  })
}
if(btnVideo){
  btnVideo.addEventListener('click', () =>{
    let video = document.querySelector('.single-video')
    containerVideoMap.forEach(container => {
        container.classList.remove('show')
    });
    video.classList.add('show')
  })
}
if(btnMap){
  btnMap.addEventListener('click', () =>{
   let map = document.querySelector('.single-map')
   containerVideoMap.forEach(container => {
        container.classList.remove('show')
   });

   map.classList.add('show')


  })
}

if(btnOpenRequest){
  let form = document.getElementById('containerFormRequest')
  btnOpenRequest.addEventListener('click', e => {
    form.classList.add('show')
  })

  if(closeOpenRequest){
    closeOpenRequest.addEventListener('click', e => {
      form.classList.remove('show')
    })
  }
}


};

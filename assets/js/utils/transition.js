export const transition = () => {
   const controller = new ScrollMagic.Controller();
   const elements = document.querySelectorAll('[data-transition]')
   elements.forEach(element => {
        element.style.opacity = "0";
    
        var scene = new ScrollMagic.Scene({
            triggerElement: element, // Elemento que activa la escena
            triggerHook: 1.5, // Cuando el 50% del elemento está en el área de visualización
            reverse: false // No revertir la animación al desplazarse hacia arriba
        })
        .addTo(controller)
        .on('enter', function() {
            setTimeout(function() {
                element.classList.add(element.dataset.transition);
                element.style.opacity = "1";
          
            }, parseInt(element.dataset.delay));
        });
  
   });
}
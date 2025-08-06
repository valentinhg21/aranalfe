
export const navbar = () => {
    const header = document.getElementById('main-header')
    const menu = document.querySelector('.menu-links-container')
    const hamburguer = document.querySelector('.hamburger')
    const drop = document.querySelectorAll('.niveles-1')
    hamburguer.addEventListener('click', e => {
        header.classList.toggle('active');
        hamburguer.classList.toggle('is-active')
    })
    drop.forEach(element => {
        element.addEventListener('mouseenter', e => {
            drop.forEach(el => {
                el.classList.remove('active')
            });
            element.classList.add('active')
        })
        element.addEventListener('mouseleave', e => {
            setTimeout(() => {
                element.classList.remove('active')
            }, 500);
            
        })
    });

    window.addEventListener('click', e => {
        const isInsideDrop = [...drop].some(el => el.contains(e.target));

        if (!isInsideDrop) {
            drop.forEach(el => el.classList.remove('active'));
        }
    });
}
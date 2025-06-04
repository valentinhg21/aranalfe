
export const navbar = () => {
    const header = document.getElementById('main-header')
    const menu = document.querySelector('.menu-links-container')
    const hamburguer = document.querySelector('.hamburger')

    hamburguer.addEventListener('click', e => {
        header.classList.toggle('active');
        hamburguer.classList.toggle('is-active')
    })

}
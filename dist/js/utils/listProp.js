export const listProp = () => {
    const currencyBtn = document.querySelectorAll('.btn-currency-price');
    if(currencyBtn.length > 0){
        currencyBtn.forEach(btn => {
            btn.addEventListener('click', e => {
                currencyBtn.forEach(btn => {
                    btn.parentElement.classList.remove('checked')
                });
                btn.parentElement.classList.add('checked')
            })
            
        });
    }
}
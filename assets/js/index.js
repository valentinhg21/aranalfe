
import { navbar } from './utils/header.js';
import { select } from './utils/select.js';
// import { selectSimple } from './utils/select-simple.js';
// import { selectMulti } from './utils/select-multi.js';
// import { autocomplete } from './utils/autocomplete.js';
import { buscador } from './utils/buscador.js';
import { transition } from './utils/transition.js';
import { CountUp } from '../../lib/countUp/countUp.min.js';
import { scrollCountUp } from './utils/scrollCountUp.js';
import { newsletter } from './utils/newsletter.js';

window.addEventListener('DOMContentLoaded', () => {
    try {
        transition();
        navbar();
        select();
        selectSimple();
        selectMulti();
        autocomplete();
        buscador();
        scrollCountUp(CountUp);
        newsletter();
    } catch (error) {
        console.error(error)
    }
});
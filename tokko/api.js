const TOKKO_URL = 'developments_url';
const TOKEN = ajax_var.token;

const DEVELOPMENTS_URL = 'https://www.tokkobroker.com/api/v1/development/';


// AJAX 
async function getDevelopments(params = {}) {


    const fullParams = {
        ...params,
        key: TOKEN,
        format: 'json',
        lang: 'es_ar'
    };

    const URL = DEVELOPMENTS_URL + '?' + new URLSearchParams(fullParams).toString();

    try {
        const response = await fetch(URL);
        const data = await response.json();

        if (data.objects && Array.isArray(data.objects)) {
            return data.objects;
        } else {
            console.error('Respuesta inválida de Tokko:', data);
            return [];
        }
    } catch (error) {
        console.error('Error al obtener los desarrollos:', error);
        return [];
    }
}





// HELPER
function getConstructionStatus(status = 4) {
    const map = {
        2: 'En pozo',
        4: 'En construcción',
        6: 'Terminado'
    };

    return map[status] || 'En pozo';
}

function getFullLocation(string = "") {
    const parts = string.split(' | ');
    parts.shift(); // Elimina el primer elemento

    const cleaned = parts.map(part => {
        return part.trim() === 'Centro (Capital Federal)' ? 'Centro/Microcentro' : part;
    });

    return cleaned.join(', ');
}

function returnUrl() {
    const isSSL = window.location.protocol === 'https:';
    const base = (isSSL ? 'https://' : 'http://') + window.location.host;
    let domain = '';

    if (base === 'https://test.zetenta.com') {
        domain = '/aranalfe';
    } else {
        domain = '';
    }

    return domain;
}


function slugify(text) {
    // Limpiar y normalizar caracteres Unicode
    text = text.trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

    // Reemplazar caracteres no permitidos por vacío
    text = text.replace(/[^a-zA-Z0-9\/_|+ -]/g, '');

    // Pasar a minúsculas y recortar guiones al principio y final
    text = text.toLowerCase().replace(/^-+|-+$/g, '');

    // Reemplazar grupos de / _ | + espacios y guiones por un solo guion
    text = text.replace(/[\/_|+ -]+/g, '-');

    return text;
}

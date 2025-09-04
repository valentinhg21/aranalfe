export const filter = () => {
    let pageProp = document.querySelector('.properties')
    if (pageProp) {
        filterLogic()
    }
}

const filterLogic = () => {
    // SELECT - inicializar TomSelect en todos los location-input
    const locationInputs = document.querySelectorAll('.location-input');
    let extraOptions = [];
    if (locationInputs.length) {
        // Mostrar los optgroups disponibles (debug)
        const optgroupDebug = [...new Set(locations_data.map(loc => loc.parent_name))];
        // console.log('Optgroups detectados:', optgroupDebug);

        // 1. Extraer parent_names y eliminar duplicados.
        const parentNames = [...new Set(locations_data.map(loc => String(loc.parent_name)))];

        // 2. Forzar orden de optgroups según datos reales
        const optgroupNames = parentNames;
        const optgroups = optgroupNames.map(name => ({
            value: name,
            label: name
        }));

        // 3. Crear las opciones de "Todos" dinámicamente

        optgroupNames.forEach(name => {
            extraOptions.push({
                value: `all_${name.toLowerCase().replace(/\s/g, '_')}`,
                text: `Todos ${name}`,
                optgroup: name
            });
        });

        // 4. Opciones normales
        const filteredOptions = [
            ...extraOptions,
            ...locations_data.map(loc => ({
                value: loc.location_id,
                text: loc.location_name,
                optgroup: loc.parent_name
            }))
        ];

        // 5. Inicializar TomSelect
        locationInputs.forEach(location => {
            const settings = {
                plugins: ['remove_button'],
                persist: false,
                create: false,
                maxItems: null,
                options: filteredOptions,
                optgroups: optgroups,
                optgroupField: 'optgroup'
            };
            new TomSelect(location, settings);
        });
    }




    // --- Botones buscar (search-location / aplicar filtros) ---
    const searchButtons = document.querySelectorAll('.search-location');
    searchButtons.forEach(searchSubmit => {
        searchSubmit.addEventListener('click', e => {
            e.preventDefault();

            const searchForm = searchSubmit.closest('form') || document.getElementById('filtros-form');
            const filtrosForm = document.getElementById('filtros-form');
            const currentParams = new URLSearchParams();

            // --- 1. Agregar todos los hidden inputs de filtros-form ---
            if (filtrosForm) {
                const hiddenInputs = filtrosForm.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    if (input.name && input.value) currentParams.append(input.name, input.value);
                });

                // --- Agregar inputs numéricos ---
                const numberInputs = filtrosForm.querySelectorAll('input[type="number"]');
                numberInputs.forEach(input => {
                    if (input.name && input.value) currentParams.set(input.name, input.value);
                });

                // --- Moneda ---
                const currencyInput = filtrosForm.querySelector('input[name="moneda"]:checked');
                if (currencyInput) currentParams.set('moneda', currencyInput.value);
            }

            // --- 2. Agregar localidades desde filter-tag ---
            const filterTags = document.querySelectorAll('.filter-tag');
            filterTags.forEach(tag => {
                const id = tag.dataset.id;
                if (id && !currentParams.getAll('localidad[]').includes(id)) {
                    currentParams.append('localidad[]', id);
                }
            });

            // --- 3. Agregar localidades desde TomSelect (si no están ya en filter-tags) ---
            if (searchForm) {
                const items = searchForm.querySelectorAll('.item');
                const existingLocalidades = currentParams.getAll('localidad[]');

                items.forEach(item => {
                    const val = item.dataset.value;

                    if (val.startsWith('all_')) {
                        const selectedExtraOption = extraOptions.find(option => option.value === val);
                        if (selectedExtraOption) {
                            const parentName = selectedExtraOption.optgroup;
                            const filteredItems = locations_data.filter(loc => loc.parent_name === parentName);
                            filteredItems.forEach(loc => {
                                if (!existingLocalidades.includes(loc.location_id)) {
                                    currentParams.append('localidad[]', loc.location_id);
                                }
                            });
                        }
                    } else {
                        if (!existingLocalidades.includes(val)) {
                            currentParams.append('localidad[]', val);
                        }
                    }
                });
            }

            // --- 4. Construir URL final ---
            const origin = window.location.origin;
            let baseURL = `${origin}/propiedades`;
            if (origin === "https://test.zetenta.com") baseURL = `${origin}/aranalfe/propiedades`;

            const finalURL = `${baseURL}/?${currentParams.toString()}`;
            window.location.href = finalURL;
        });
    });



    // --- Filtros ---
    const pageResults = document.querySelector('.properties');
    if (pageResults) {
        const form = document.getElementById('filtros-form');
        const filtros = form.querySelectorAll('.button-filter');
        const limpiarBtn = document.getElementById('limpiar-filtros');

        // Reconstruir hidden inputs desde URL
        const urlParams = new URLSearchParams(window.location.search);
    
        filtros.forEach(filtro => {
            const param = filtro.dataset.param;
            const value = filtro.dataset.value;
            if (!param || !value) return;

            const name = param + '[]';
            let values = urlParams.getAll(name);

            // --- Agregar también los índices [0], [1], etc. ---
            for (const [key, val] of urlParams.entries()) {
                if (key.startsWith(param + '[') && !values.includes(val)) {
                    values.push(val);
                }
            }

            if (values.includes(value)) {
                const label = filtro.closest('label');
                label.classList.add('checked');
                let hidden = form.querySelector(`input[type="hidden"][name="${name}"][value="${value}"]`);
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = name;
                    hidden.value = value;
                    form.appendChild(hidden);
                }
            }
        });

        // Toggle al click y actualizar hidden inputs (sin recargar la página)
        filtros.forEach(filtro => {
            filtro.addEventListener('click', e => {
                e.preventDefault();
                const label = filtro.closest('label');
                label.classList.toggle('checked');

                const param = filtro.dataset.param;
                const value = filtro.dataset.value;
                if (!param || !value) return;

                const name = param + '[]';
                let hidden = form.querySelector(`input[type="hidden"][name="${name}"][value="${value}"]`);

                if (label.classList.contains('checked')) {
                    if (!hidden) {
                        hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = name;
                        hidden.value = value;
                        form.appendChild(hidden);
                    }
                } else {
                    if (hidden) hidden.remove();
                }
            });
        });

        // Limpiar todos los filtros
        if (limpiarBtn) {
            limpiarBtn.addEventListener('click', e => {
                e.preventDefault();

                filtros.forEach(filtro => filtro.closest('label').classList.remove('checked'));

                const hiddens = form.querySelectorAll('input[type="hidden"]');
                hiddens.forEach(input => input.remove());

                const inputs = form.querySelectorAll('input[type="number"], input[type="radio"]');
                inputs.forEach(input => {
                    if (input.type === 'number') input.value = input.min || '';
                    else if (input.type === 'radio') input.checked = false;
                });

                window.location.href = form.action || window.location.pathname;
            });
        }
    }


    // --- Botón aplicar filtros ---
    const applyBtn = document.getElementById('apply');
    if (applyBtn) {
        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const filtrosForm = document.getElementById('filtros-form');
            const currentParams = new URLSearchParams();

            // --- 1. Agregar todos los hidden inputs de filtros-form ---
            if (filtrosForm) {
                const hiddenInputs = filtrosForm.querySelectorAll('input[type="hidden"]');
                hiddenInputs.forEach(input => {
                    if (input.name && input.value) currentParams.append(input.name, input.value);
                });

                // --- Agregar inputs numéricos ---
                const numberInputs = filtrosForm.querySelectorAll('input[type="number"]');
                numberInputs.forEach(input => {
                    if (input.name && input.value) currentParams.set(input.name, input.value);
                });

                // --- Agregar moneda ---
                const currencyInput = filtrosForm.querySelector('input[name="moneda"]:checked');
                if (currencyInput) currentParams.set('moneda', currencyInput.value);
            }

            // --- 2. Agregar localidades desde filter-tag ---
            const filterTags = document.querySelectorAll('.filter-tag');
            filterTags.forEach(tag => {
                const id = tag.dataset.id;
                if (id && !currentParams.getAll('localidad[]').includes(id)) {
                    currentParams.append('localidad[]', id);
                }
            });

            // --- 3. Agregar localidades desde TomSelect si no están en filter-tag ---
            const searchForm = document.querySelector('.form-location');
            if (searchForm) {
                const items = searchForm.querySelectorAll('.item');
                const existingLocalidades = currentParams.getAll('localidad[]');

                items.forEach(item => {
                    const val = item.dataset.value;

                    if (val.startsWith('all_')) {
                        const selectedExtraOption = extraOptions.find(option => option.value === val);
                        if (selectedExtraOption) {
                            const parentName = selectedExtraOption.optgroup;
                            const filteredItems = locations_data.filter(loc => loc.parent_name === parentName);
                            filteredItems.forEach(loc => {
                                if (!existingLocalidades.includes(loc.location_id)) {
                                    currentParams.append('localidad[]', loc.location_id);
                                }
                            });
                        }
                    } else {
                        if (!existingLocalidades.includes(val)) {
                            currentParams.append('localidad[]', val);
                        }
                    }
                });
            }

            // --- 4. Construir URL final ---
            const origin = window.location.origin;
            let baseURL = `${origin}/propiedades`;
            if (origin === "https://test.zetenta.com") baseURL = `${origin}/aranalfe/propiedades`;

            const finalURL = `${baseURL}/?${currentParams.toString()}`;
            window.location.href = finalURL;
        });
    }

    document.querySelectorAll('.btn-currency-price').forEach(radio => {
        radio.addEventListener('click', function () {
            if (this.checked && this.dataset.waschecked === "true") {
                // Si estaba marcado y hago click de nuevo → desmarcar
                this.checked = false;
                this.dataset.waschecked = "false";

                // Quitar clase del label
                this.parentElement.classList.remove('checked');

                // 🔹 Eliminar de la URL actual (params)
                const url = new URL(window.location.href);
                url.searchParams.delete('moneda');
                window.history.replaceState({}, '', url); // actualiza sin recargar
            } else {
                // Destildar el resto
                document.querySelectorAll('.btn-currency-price').forEach(r => {
                    r.dataset.waschecked = "false";
                    r.parentElement.classList.remove('checked');
                });

                // Marcar el actual
                this.dataset.waschecked = "true";
                this.parentElement.classList.add('checked');
            }
        });
    });


}


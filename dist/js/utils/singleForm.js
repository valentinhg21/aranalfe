const loading = (status) => {
  const loadingForm = document.querySelector(".loading-form");
  if (loadingForm) {
    if (status) {
      loadingForm.classList.add("show");
    } else {
      loadingForm.classList.remove("show");
    }
  }
};

const showPopup = (title, msg, icon) => {
  Swal.fire({
    title: title,
    text: msg,
    icon: icon,
  });
};
// FUNCION PARA MANDAR EL FETCH
const sendForm = async (dataForm, form, event) => {
  const AJAX_URL = ajax_var.url;
  // UTMS
  if(form.dataset.origen !== 'Newsletter'){
    let origen = form.dataset.origen;
    dataForm.append("origen", origen);
   
  }
  const urlParams = new URLSearchParams(window.location.search);
  const utmSource = urlParams.get('utm_source');
  const utmMedium = urlParams.get('utm_medium');
  let refName = 'Directo';
  if (utmSource === 'google' && utmMedium === 'cpc') {
    refName = 'Google ADS';
  } else if (document.referrer.includes('google.com')) {
    refName = 'Google Organico';
  } else if (document.referrer.includes('facebook.com')) {
    refName = 'Facebook';
  } else if (document.referrer.includes('instagram.com')) {
    refName = 'Instagram';
  }

  dataForm.append("fuente", refName);
  dataForm.append("action", 'enviar_consulta_tokko')
  
  try {
    const response = await fetch(AJAX_URL, {
      method: "POST",
      body: dataForm,
    });

    if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
    const data = await response.json();
    
    if(data.status === 'OK'){
      showPopup("Enviado", data.message, 'success');
      loading(false);
      form.reset();
      dataLayer.push({'event': `${event}`});
    }else{
      showPopup("Error", data.message, 'error');
      loading(false);
    }
  } catch (error) {
    console.error("Error al enviar:", error);
    showPopup("Error", 'Error del servidor intentalo en otro momento.', 'error');
    loading(false);
  }
};

export const singleForm = () => {
  const formSingleProperty = document.getElementById("singlePropertyForm");
  const formSingleDevelopment = document.getElementById("singleFormDevelopment");
  const singleForm = document.getElementById('singleForm');
  const btn = document.querySelector(".btn-newsletter");

  // FORMULARIO PROPIEDADES
  if (formSingleProperty) {
    const fullname = document.getElementById("fullname");
    const telephone = document.getElementById("telephone");
    const email = document.getElementById("email");
    const message = document.getElementById("message");
    const propertyName = document.getElementById('propertyName')
    const property_id = formSingleProperty.dataset.property;
    const propertyOp = document.getElementById('propertyOperation')
    const propertyType = document.getElementById('propertyType');
    const propertyAddress = document.getElementById('propertyAddress');
    const propertyBarrio = document.getElementById('propertyBarrio');
    const propertyPrice = document.getElementById('propertyPrice')
    const propertyAlquiler = document.getElementById('propertyPriceAlquiler')
    const propertyMetros = document.getElementById('propertyMetros')
    const formDataProperty = new FormData();
    
    formSingleProperty.addEventListener("submit", (e) => {
      e.preventDefault();
      // Testear que no haya campos vacios
      if (
        validator.isEmpty(fullname.value) ||
        validator.isEmpty(email.value) ||
        validator.isEmpty(telephone.value) ||
        validator.isEmpty(message.value)
      ) {

        showPopup('Error', 'Completar todos los campos', 'error');
        fullname.classList.add("error");
        telephone.classList.add("error");
        email.classList.add("error");
        message.classList.add("error");

        return;
      }

      if (!validator.isEmail(email.value)) {
        showPopup('Error', 'ingresar un email válido e intentá nuevamente.', 'error');
        email.classList.add("error");

        return;
      }

      fullname.classList.remove("error");
      telephone.classList.remove("error");
      email.classList.remove("error");
      message.classList.remove("error");

      // preparando data
      formDataProperty.append("fullname", fullname.value);
      formDataProperty.append("telephone", telephone.value);
      formDataProperty.append("email", email.value);
      formDataProperty.append("message", message.value);
      formDataProperty.append("tags", formSingleProperty.dataset.tags);
      formDataProperty.append("property_id", property_id);
      formDataProperty.append("property_name", propertyName.value);
      formDataProperty.append("property_operation", propertyOp.value);
      formDataProperty.append("property_type", propertyType.value);
      formDataProperty.append("property_address", propertyAddress.value);
      formDataProperty.append("property_barrio", propertyBarrio.value);
      formDataProperty.append("property_price", propertyPrice.value);
      formDataProperty.append("property_price_alquiler", propertyAlquiler.value);
      formDataProperty.append("property_metros", propertyMetros.value);
      loading(true);
      sendForm(formDataProperty, formSingleProperty, formSingleProperty.dataset.event);
    });
  }
  // FORMULARIO EMPRENDIMIENTO
  if (formSingleDevelopment) {
    const fullname = document.getElementById("fullname");
    const telephone = document.getElementById("telephone");
    const email = document.getElementById("email");
    const message = document.getElementById("message");
    const property_id = formSingleDevelopment.dataset.property;
    const propertyName = document.getElementById('propertyName')
    const propertyOp = document.getElementById('propertyOperation')
    const propertyType = document.getElementById('propertyType')
    const developmentName = document.getElementById('developmentName')
    const formDataDevelop = new FormData();

    formSingleDevelopment.addEventListener("submit", (e) => {
      e.preventDefault();
      // Testear que no haya campos vacios
      if (
        validator.isEmpty(fullname.value) ||
        validator.isEmpty(email.value) ||
        validator.isEmpty(telephone.value) ||
        validator.isEmpty(message.value)
      ) {

        showPopup('Error', 'Completar todos los campos', 'error');
        fullname.classList.add("error");
        telephone.classList.add("error");
        email.classList.add("error");
        message.classList.add("error");

        return;
      }

      if (!validator.isEmail(email.value)) {
        showPopup('Error', 'ingresar un email válido e intentá nuevamente.', 'error');
        email.classList.add("error");

        return;
      }

      fullname.classList.remove("error");
      telephone.classList.remove("error");
      email.classList.remove("error");
      message.classList.remove("error");

      // preparando data
      formDataDevelop.append("fullname", fullname.value);
      formDataDevelop.append("telephone", telephone.value);
      formDataDevelop.append("email", email.value);
      formDataDevelop.append("message", message.value);
      formDataDevelop.append("tags", formSingleDevelopment.dataset.tags);
      formDataDevelop.append("development_id", property_id);
      formDataDevelop.append("property_operation", propertyOp.value);
      formDataDevelop.append("property_type", propertyType.value);
      formDataDevelop.append("property_name", propertyName.value);
      formDataDevelop.append("development_name", developmentName.value);
      formDataDevelop.append("property_id", property_id);
      loading(true);
      sendForm(formDataDevelop, formSingleDevelopment, formSingleDevelopment.dataset.event);
    });
  }
  // FORMULARIO TASACIONES Y CONTACTO
  if (singleForm) {
    const fullname = document.getElementById("fullname");
    const telephone = document.getElementById("telephone");
    const email = document.getElementById("email");
    const message = document.getElementById("message");
    const formDataSingle = new FormData();

    singleForm.addEventListener("submit", (e) => {
      e.preventDefault();
      // Testear que no haya campos vacios
      if (
        validator.isEmpty(fullname.value) ||
        validator.isEmpty(email.value) ||
        validator.isEmpty(telephone.value) ||
        validator.isEmpty(message.value)
      ) {

        showPopup('Error', 'Completar todos los campos', 'error');
        fullname.classList.add("error");
        telephone.classList.add("error");
        email.classList.add("error");
        message.classList.add("error");

        return;
      }

      if (!validator.isEmail(email.value)) {
        showPopup('Error', 'ingresar un email válido e intentá nuevamente.', 'error');
        email.classList.add("error");

        return;
      }

      fullname.classList.remove("error");
      telephone.classList.remove("error");
      email.classList.remove("error");
      message.classList.remove("error");

      // preparando data
      formDataSingle.append("fullname", fullname.value);
      formDataSingle.append("telephone", telephone.value);
      formDataSingle.append("email", email.value);
      formDataSingle.append("message", message.value);
      formDataSingle.append("tags", singleForm.dataset.tags);
      loading(true);
      sendForm(formDataSingle, singleForm, singleForm.dataset.event);
    });
  }
  // FORMULARIO NEWSLETTER
  if(btn){
    let checkWhatsapp = document.getElementById("whatsapp-newsletter");
    let inputNewsletter = document.getElementById("email-newsletter");
    const newsletterData = new FormData();
    const form = document.getElementById('newsletterForm')
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      if (checkWhatsapp.checked) {
        // Enviar numero
        if (validator.isNumeric(inputNewsletter.value)) {
          newsletterData.append("fullname", "");
          newsletterData.append("telephone", inputNewsletter.value);
          newsletterData.append("message", 'Si prefiero que me contacten via whatsapp');
          newsletterData.append("tags", 'Newsletter');
          newsletterData.append('origen', 'Suscripción WhatsApp')
          sendForm(newsletterData, form, form.dataset.event);


        } else {
          showPopup("Error", "Por favor, ingresar un número válido e intentá nuevamente.", "error");
        }
      } else {
        if (validator.isEmail(inputNewsletter.value)) {
          newsletterData.append("fullname", "");
          newsletterData.append("email", inputNewsletter.value);
          newsletterData.append("message", 'No prefiero que me contacten via whatsapp');
          newsletterData.append("tags", 'Newsletter');
          newsletterData.append('origen', 'Suscripción Newsletter')
          sendForm(newsletterData, form, form.dataset.event);
        } else {
          showPopup("Hubo un error al suscribirte", "Por favor, ingresar un email válido e intentá nuevamente.", "error");
        }
      }
    });

    checkWhatsapp.addEventListener("click", () => {
      if (checkWhatsapp.checked) {
        inputNewsletter.placeholder = "Ingresar tu número";
      } else {
        inputNewsletter.placeholder = "Ingresar tu email";
      }
    });
  }
};
<?php get_header(); ?>

    <div class="single page-404">
        <div class="container">
            <div class="content tinymce-custom">
                <h1>ERROR: 404</h1>
                <h2>Pagina no encontrada</h2>
                <p>Lo sentimos, la página que estás buscando no está disponible. Por favor, vuelve a la pantalla de inicio.</p>
                <div class="button__container d-flex justify-center">
                    <a href="<?php echo home_url();?>" class="btn btn-black-to-red m-auto">
                        Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>
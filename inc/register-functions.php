<?php 

function insert_textarea($acf, $tag = 'p'){
    $cont = 1;
    $text_area_arr = explode("\n", $acf);
    $text_area_arr = array_map('trim', $text_area_arr);
    foreach ($text_area_arr as &$valor) {
        if($valor != ''){
            echo('<' . $tag . '>' . $valor . '</' . $tag . '>');
        }
    }
}

function insert_custom_image($url, $w = '100%', $h = '100%', $alt = ''){
    if($url){
        $imageHTML = "<img loading='lazy' src='{$url}' width='{$w}' height='{$h} alt='{$alt}' />";
        echo $imageHTML;
    }
}

function insert_custom_image_json($url, $w = '100%', $h = '100%', $alt = ''){
    if($url){
        return $imageHTML = "<img loading='lazy' src='{$url}' width='{$w}' height='{$h} alt='{$alt}' />";
     
    }
}

function insert_default_image(){
    $root = IMAGE_DEFAULT;
    $imageHTML = "<img loading='lazy' src='{$root}' width='750' height='500' alt='En la imagen se muestra imagen de default' />";
    return $imageHTML;
}


function insert_button($acf, $mt, $classBtn, $icon = ''){
    if($mt === ''){
        $mt = 3;
    }
    if($classBtn === ''){
        $classBtn = '';
    }

    if ( $acf ):
        $link_url = esc_url($acf['url']);
        $link_title = esc_html($acf['title']);
        $link_target = esc_attr($acf['target'] ? $acf['target'] : '_self');
        $buttonHTML = "<div class='button__container mt-sm-{$mt} mt-2'>";
        $buttonHTML .= "<a class='btn {$classBtn}' href='{$link_url}' target='{$link_target}'>{$icon}{$link_title}</a>";
        $buttonHTML .= "</div>";
        echo($buttonHTML);
    endif;

}

function insert_image($acf, $size = false){
    if(!$size || $size === ''){
        $size = 1024;
    }
    if ( $acf ):
    $url = esc_url($acf['url']);
    $alt = esc_attr($acf['alt']);
    $size_type = 'large';
    $width = $acf['sizes'][ $size_type . '-width' ];
    $height = $acf['sizes'][ $size_type . '-height' ];
    $imageHTML = "<img loading='lazy' width='{$width}'" . img_responsive($acf['id'],"thumb-{$size}","{$size}px") . " 'height='{$height}' alt='{$alt}' />";
    echo $imageHTML;
    endif;
}

function insert_acf($acf, $tag, $class = ''){
    if($class === ''){
        $class = "";
    }else{
        $class = "class='{$class}'";
    }
    if ( $acf ):
        if($tag === ''):
            $HTML = $acf;
            echo $HTML;
        else:
            $HTML = "<{$tag} $class>";
            $HTML .= $acf;
            $HTML .= "</$tag>";
            echo $HTML;
        endif;
    endif;
}



function sanitizeString($cadena){
        $originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúúûýýþÿ';
        $modificadas = 'AAAAAAACEEEEIIIIDNOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuuyyby';
        $cadena = utf8_decode($cadena);
        $cadena = strtr($cadena, utf8_decode($originales), $modificadas);
        $cadena = str_replace(" ", "-", $cadena);
        return strtolower(utf8_encode($cadena));
}


function animation($animation = "fade-in-bottom", $delay = 400){
    $attr = "data-transition='$animation' data-delay='$delay' style='opacity: 0'";

    echo $attr;
}


function limit_description_length($description, $length = 80, $link_text = 'Ver más') {
    if (strlen($description) > $length) {
        $description = substr($description, 0, $length);
        $description .= "...<span>{$link_text}</span>";
    }
    return $description;
}


function render_svg($relative_path) {
    $full_path = get_template_directory() . $relative_path;
    if (file_exists($full_path)) {
        echo file_get_contents($full_path);
    } else {
        echo "<!-- SVG no encontrado: {$relative_path} -->";
    }
}
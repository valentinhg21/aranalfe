<?php 



class Walker_Zetenta_Menu extends Walker_Nav_menu {
    function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "\n$indent<ul class=\"sub-menu level-$depth\"><div class='body-links'>\n";
    }
    
    function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</div></ul>\n";
    }
    
    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
    
        // Contar la profundidad total del menú
        $total_depth = 0;
        $parent = $item->menu_item_parent;
        while ($parent != 0) {
            $total_depth++;
            $parent_item = get_post($parent);
            $parent = $parent_item->post_parent;
        }
    
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        // Agrega la clase 'custom-menu-item' al li principal del dropdown
        if (in_array('menu-item-has-children', $item->classes, true)) {
            $classes[] = ($depth === 1) ? 'link__container' : 'dropdown dropdown-lvl-' . $depth;
            $classes[] = ($depth === 2) ? 'link__container' : 'dropdown dropdown-lvl-' . $depth;
        } else {
            $classes[] = 'menu-item-' . $item->ID . ' dropdown dropdown-lvl-' . $depth;
     
        }
    
        // Agregar clase al elemento <li> del primer nivel
        if ($depth === 0) {
            if ($args->walker->has_children) {
                $classes[] = 'niveles-' . ($total_depth + 1); // +1 para incluir el nivel del elemento actual
            }
 
        }
    
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

        if ($item->object == 'product_cat') {
            $destacar = get_field( 'destacar', $item->ID);
            $classDestacar = $destacar ? 'cat-feature' : '';
       
            $class_names = $class_names ? ' class="'. esc_attr( $class_names ) . ' ' .  $classDestacar .'"' : '';
        }else{
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        }


        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
    
        $output .= $indent . '<li' . $id . $class_names . '>';
    
        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : 'javascript:void(0)';
    
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
    
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $description = $item->description;




        
        if ($args->walker->has_children) {
            if ($depth !== 0) {
                $item_output .= '<div class="drop-btn"><a href="' . esc_url($item->url) . '">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a><i class="fa-solid fa-chevron-right"></i></div>';
            } else {
                $item_output .= '<div class="drop-btn"><a href="' . esc_url($item->url) . '" class="dropdown-button">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a><i class="fa-solid fa-chevron-down"></i></div>';
            }
        } else {
            $item_output .= '<a' . $attributes . '>' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a>';
        }
        $item_output .= $args->after;
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
    }
}






class Walker_Zetenta_Menu_Right extends Walker_Nav_menu {
    public function start_lvl(&$output, $depth = 0, $args = array()) {
        // Ajusta la clase 'sub-menu' y la clase 'menu-depth-{depth}' al submenú
        $indent = str_repeat("\t", $depth);
        $submenu_class = ($depth > 0) ? 'dropdown-child menu-depth-' . $depth : 'dropdown-menu';
        $output .= "\n$indent<div class=\"$submenu_class\"><ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        // Ajusta la salida del elemento según el nivel de profundidad
        $indent = ($depth > 0 ? str_repeat("\t", $depth) : '');
        $classes = array();
        $classes[] = 'link__container menu-item-' . $item->ID;
        // Agrega la clase 'menu-depth-{depth}' al elemento
        $classes[] = 'link__container menu-depth-' . $depth;

        // Agrega la clase 'custom-menu-item' al li principal del dropdown
        if (in_array('menu-item-has-children', $item->classes, true)) {
            $classes[] = ($depth === 1) ? 'link__container link-child' : 'link__container dropdown-right';
        }

        $output .= $indent . '<li id="menu-item-' . $item->ID . '" class="' . implode(' ', $classes) . '">';

        // Ajusta el enlace según el nivel de profundidad
        $atts = array(
            'title'  => !empty($item->title) ? $item->title : '',
            'target' => !empty($item->target) ? $item->target : '',
            'rel'    => !empty($item->xfn) ? $item->xfn : '',
            'href' => (!empty($item->url) && $item->url !== '#') ? esc_url($item->url) : 'javascript:void(0)'

        );

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }
        }

        $item_output = $args->before;

        if ($args->walker->has_children) {
            if ($depth !== 1) {
                $void = $item->url == '#' ? 'javascript:void(0)' : esc_url($item->url);
                $item_output .= '<div class="drop-btn"><a href="' . $void . '">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a><i class="fa-solid fa-chevron-down"></i></div>';
            } else {
                $void = $item->url == '#' ? 'javascript:void(0)' : esc_url($item->url);
                $item_output .= '<a href="' . $void . '" class="dropdown-button">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '<i class="fa-solid fa-arrow-right-long"></i></a>';
            }
        } else {
            $item_output .= '<a' . $attributes . '>' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a>';
        }

        $item_output .= $args->after;
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}






class Walker_Zetenta_Menu_Footer extends Walker_Nav_menu {

    public function start_lvl(&$output, $depth = 0, $args = array()) {
        // Ajusta la clase 'sub-menu' y la clase 'menu-depth-{depth}' al submenú
        $indent = str_repeat("\t", $depth);
        $submenu_class = ($depth > 0) ? 'dropdown-child menu-depth-' . $depth : 'dropdown-menu';
        $output .= "\n$indent<div class=\"$submenu_class\"><ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        // Ajusta la salida del elemento según el nivel de profundidad
        $indent = ($depth > 0 ? str_repeat("\t", $depth) : '');
        $classes = array();
        $classes[] = 'link__container menu-item-' . $item->ID;
        // Agrega la clase 'menu-depth-{depth}' al elemento
        $classes[] = 'link__container menu-depth-' . $depth;

        // Agrega la clase 'custom-menu-item' al li principal del dropdown
        if (in_array('menu-item-has-children', $item->classes, true)) {
            $classes[] = ($depth === 1) ? 'link__container link-child' : 'link__container dropdown';
        }

        $output .= $indent . '<li id="menu-item-' . $item->ID . '" class="' . implode(' ', $classes) . '">';

        // Ajusta el enlace según el nivel de profundidad
        $atts = array(
            'title'  => !empty($item->title) ? $item->title : '',
            'target' => !empty($item->target) ? $item->target : '',
            'rel'    => !empty($item->xfn) ? $item->xfn : '',
            'href' => (!empty($item->url) && $item->url !== '#') ? esc_url($item->url) : 'javascript:void(0)'

        );

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
            }
        }

        $item_output = $args->before;

        if ($args->walker->has_children) {
            if ($depth !== 1) {
                $void = $item->url == '#' ? 'javascript:void(0)' : esc_url($item->url);

                $item_output .= '<div class="drop-btn"><a href="' . $void . '">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a><i class="fa-solid fa-chevron-down"></i></div>';
            
            } else {
                $void = $item->url == '#' ? 'javascript:void(0)' : esc_url($item->url);
                $item_output .= '<a href="' . $void . '" class="dropdown-button">' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '<i class="fa-solid fa-arrow-right-long"></i></a>';
            }
        } else {
            $item_output .= '<a' . $attributes . '>' . $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after . '</a>';
        }

        $item_output .= $args->after;
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

}



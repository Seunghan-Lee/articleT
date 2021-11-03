<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Walker_Nav_Menu;

class Header extends Composer
{
    protected static $views = [
        'partials.header',
    ];
    public function with()
    {
        return [
          'tnb' => $this->tnb(),
          'gnb' => $this->gnb(),
        ];
    }

    public function tnb() {
      return wp_nav_menu([ 
        'menu' => 'top', 'container' => false, 'echo' => false, 'depth' => 1,
        'menu_class' => 'header__top-nav coldiv__s1',
        'walker' => new top_nav_menu(),
      ]);
    }

    public function gnb() {
      return wp_nav_menu([ 
        'menu' => 'main', 'container' => false, 'echo' => false, 'depth' => 2,
        'menu_class' => 'header__global-nav',
        'walker' => new global_nav_menu(),
      ]);
    }
}

class top_nav_menu extends Walker_Nav_Menu
{
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      global $wp_query;
      $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

      // Depth-dependent classes.
      $depth_classes = array(
          ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
          ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
          ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
          'menu-item-depth-' . $depth
      );
      $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

      // Passed classes.
      $classes = empty( $item->classes ) ? array() : (array) $item->classes;
      $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

      // Build HTML.
      $output .= <<<EOD
        $indent<li class="header__top-nav-item $depth_class_names $class_names">
EOD;

      // Link attributes.
      $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
      $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
      $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
      $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
      $attributes .= ' class="menu-link flex gap-x-[4px] items-center ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

      // icon
      // var_dump($item->ID);
      $iconUrl = get_field('icon', $item->ID);
      $iconBgcolor = get_field('bg_color', $item->ID);
      $icon = <<<EOD
        <div class="header__top-nav-icon" style="--bgColor: $iconBgcolor">
          <img class="w-[10px] h-[10px]" src="$iconUrl" />
        </div>
EOD;

      // Build HTML output and pass through the proper filter.
      $item_output = sprintf( '%1$s<a%2$s>%7$s%3$s%4$s%5$s</a>%6$s',
          $args->before,
          $attributes,
          $args->link_before,
          apply_filters( 'the_title', $item->title, $item->ID ),
          $args->link_after,
          $args->after,
          $icon
      );
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}


class global_nav_menu extends Walker_Nav_Menu
{

  /**
   * Starts the list before the elements are added.
   *
   * Adds classes to the unordered list sub-menus.
   *
   * @param string $output Passed by reference. Used to append additional content.
   * @param int    $depth  Depth of menu item. Used for padding.
   * @param array  $args   An array of arguments. @see wp_nav_menu()
   */
  public function start_lvl(&$output, $depth = 0, $args = array())
  {
      // Depth-dependent classes.
      $indent = ($depth > 0  ? str_repeat("\t", $depth) : ''); // code indent
      $display_depth = ($depth + 1); // because it counts the first submenu as 0
      $classes = array(
          'sub-menu',
          ($display_depth % 2  ? 'menu-odd' : 'menu-even'),
          ($display_depth >=2 ? 'sub-sub-menu' : ''),
          'menu-depth-' . $display_depth
      );
      
      // 자식을 가지고있는 alpinejs가 적용되야하는 element에 클래스 적용
      $advance_class_names = $depth === 0 ? ' header__global-subnav ' : '';
      $class_names = implode($advance_class_names, $classes);

      // depth data
      $dapth_alpinejs = $depth === 0 ? <<<EOD
            x-show="menuitemOver"
            x-cloak
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        EOD : '';

      // Build HTML for output.
      $output .= "\n" . $indent . '<ul class="' . $class_names . '"'. $dapth_alpinejs .'>' . "\n";
  }
  
  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
      global $wp_query;
      $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

      // Depth-dependent classes.
      $depth_classes = array(
          ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
          ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
          ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
          'menu-item-depth-' . $depth
      );
      $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

      // Passed classes.
      $classes = empty( $item->classes ) ? array() : (array) $item->classes;

      // 최상위 부모 메뉴 아이템 클래스 추가
      $advance_class_names = $depth === 0 ? ' header__global-nav-item-parent ' : '';
      $class_names = esc_attr( implode( $advance_class_names, apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

      $alpinejs = '';
      if (array_search('menu-item-has-children',$classes)) {
        $alpinejs = <<<EOD
          x-data="{menuitemOver: false}"
          @mouseover="menuitemOver = true"
          @mouseleave="menuitemOver = false"
EOD;
      }
      // Build HTML.
      $output .= <<<EOD
        $indent<li class="$depth_class_names $class_names" $alpinejs>
EOD;

      // Link attributes.
      $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
      $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
      $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
      $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
      $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link header__global-nav-item-'.$depth : 'main-menu-link header__global-nav-item' ) . '"';
      
      // Build HTML output and pass through the proper filter.
      $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
          $args->before,
          $attributes,
          $args->link_before,
          apply_filters( 'the_title', $item->title, $item->ID ),
          $args->link_after,
          $args->after,
      );
      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }
}
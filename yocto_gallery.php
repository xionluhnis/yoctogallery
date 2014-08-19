<?php

/**
 * The file description. *
 * @package Pico
 * @subpackage YoctoGallery
 * @version 0.1
 * @author Alexandre Kaspar <xion.luhnis@gmail.com>
 *
 */
class Yocto_Gallery{

  private $sections;

  public function __construct() {
    $this->sections = array(
      'index' => array(),
      'gallery' => array(),
      'page' => array()
    );
  }

  public function get_page_data(&$data, $page_meta){
    $tpl = $page_meta['template'];
    if(empty($tpl)) $tpl = 'index';
    $data['template'] = $tpl;
    // TODO add image information
    if($tpl === 'gallery'){

    }
  }

  public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page){
    $base_url = $current_page['url'];
    // create tree
    $this->sections = array();
    foreach($pages as $page){
      $url = $page['url'];
      if($url === $base_url) continue; // skip current page
      // only process pages that expand the url
      if(self::startsWith($url, $base_url)){
        // check level in file tree
        while(self::endsWith($url, '/')) $url = substr($url, 0, -1);
        if(strpos($url, '/', strlen($base_url) + 1) === FALSE) {
          $tpl = $page['template'];
          // add page to specific section
          if(empty($this->sections[$tpl]))
            $this->sections[$tpl] = array($page);
          else
            $this->sections[$tpl][] = $page;
        }
      }
    }
  }

  public function before_render(&$twig_vars, &$twig, &$template) {
    foreach($this->sections as $tpl => $val){
      $twig_vars['sections'][$tpl] = $val;
    }
    ksort($twig_vars['sections']);

    // parent_url
    $url = $twig_vars['current_page']['url'];
    $parent_url = '..';
    if(!self::endsWith($url, '/') && !self::endsWith($url, '/index')){
      $parent_url = substr($url, 0, strrpos($url, '/')) . '/';
    } else {
      if(self::endsWith($url, '/index')){
        $url = substr($url, 0, -5);
      }
      if(strcmp($url, $twig_vars['base_url'] . '/') === 0){
        $parent_url = '/';
      } else {
        $url = substr($url, 0, -1);
        $parent_url = substr($url, 0, strrpos($url, '/')) . '/';
      }
    }
    $twig_vars['parent_url'] = $parent_url;
  }

  private static function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }

  private static function endsWith($haystack, $needle){
    return substr($haystack, -strlen($needle)) === $needle;
  }

}

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
  }

  public function get_page_data(&$data, $page_meta){
    $data['template'] = $page_meta['template'] || 'index';
    // TODO add image information
    if($page_meta['template'] === 'gallery'){

    }
  }

  public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page){
    $base_url = $current_page['url'];
    // create tree
    $this->tree = array();
    foreach($pages as $page){
      $url = $page['url'];
      if($url === $base_url) continue; // skip current page
      // only process pages that expand the url
      if(self::startsWith($page['url'], $base_url)){
        // check level in file tree
        while(self::endsWith($url, '/')) $url = substr($url, 0, -1);
        if(strpos($url, '/', strlen($base_url) + 1) === FALSE) {
          $tpl = $page['template'];
          // add page to specific section
          if(!isset($this->sections[$tpl]))
            $this->sections[$tpl] = array($page);
          else
            $this->sections[$tpl][] = $page;
        }
      }
    }
  }

  public function before_render(&$twig_vars, &$twig, &$template) {
    $twig_vars['tree'] = $this->tree;
  }

  private static function startsWith($haystack, $needle){
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }

  private static function endsWith($haystack, $needle){
    return substr($haystack, -strlen($needle)) === $needle;
  }

}

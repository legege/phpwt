<?php
/*
 * PHPWT - PHP Website Tookit
 * Copyright (C) 2005-2008  Georges-Etienne Legendre
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

include_once 'PageFactory.class.php';
include_once 'Util.class.php';

/**
 * This class is a singleton.
 */
class Toolkit {
  private $lang;

  /**
   * @return the unique instance of this class.
   */
  public static function getInstance() {
    static $instance;

    if (!isset($instance)) {
      $c = __CLASS__;
      $instance = new $c;
    }
    return $instance;
  }

  /**
   * @param language The language to set
   */
  public function init($lang) {
    $this->lang = $lang;
  }

  /**
   * @return the language
   */
  public function getLanguage() {
    return $this->lang;
  }

  /**
   * @return the current URI
   */
  public static function currentURI() {
    if (!$_GET['uri']) {
      $uri = '/';
    }
    else {
      $uri = $_GET['uri'];
    }

    // Remove the trailing slash
    $uri = preg_replace("#/(.*)(([^/])|/)$#", "/$1$3", $uri);

    // Remove invalid characters
    $uri = preg_replace("/[']+/","-", $uri);

    return addslashes($uri);
  }

  /**
   * Construct an internal URL for the given page.
   *
   * @param id The page id.
   * @param lang The language (if not specified, the current language).
   */
  public static function pageURL($id, $lang = '') {
    if ($lang == '' && count($GLOBALS['language_array']) > 1) {
      $lang = self::getInstance()->getLanguage();
    }

    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-mappings/page[@id = '$id']", $lang);
    if ($nodes->length > 0) {
      $node = $nodes->item($nodes->length - 1);
      $uri = $node->getAttribute('uri');
      $link = $node->getAttribute('link');

      if (strlen($uri) > 0) {
        return self::resourceURL($uri, $lang);
      }
      else if (strlen($link) > 0) {
        return $link;
      }
    }
    return self::resourceURL(Toolkit::currentURI(), $lang);
  }

  /**
   * Construct an internal URL for the given URI.
   *
   * @param uri The uri.
   * @param lang The language (if not specified, the current language).
   */
  public static function linkURL($uri, $lang = '') {
    if ($lang == '' && count($GLOBALS['language_array']) > 1) {
      $lang = self::getInstance()->getLanguage();
    }

    return self::resourceURL($uri, $lang);
  }

  /**
   * Construct an internal URL for a resource.
   *
   * @param uri The uri
   * @param prefix A prefix for the URL.
   */
  public static function resourceURL($uri, $prefix = '') {
    if (substr($uri, 0, 1) != '/') {
      $uri = '/'.$uri;
    }

    if (strlen($prefix) > 0 && substr($prefix, 0, 1) != '/') {
      $prefix = '/'.$prefix;
    }

    return Util::getWebRoot().$prefix.$uri;
  }

}

?>
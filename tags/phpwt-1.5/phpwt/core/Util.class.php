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

/**
 * This class contains utility methods.
 */
class Util {

  /**
   *
   */
  public static function getRootPath() {
    return $GLOBALS['app_root'];
  }

  /**
   *
   */
  public static function getWebRoot() {
    $web_root = dirname($_SERVER['SCRIPT_NAME']);
    if ($web_root == '/') {
      return '';
    }
    return $web_root;
  }

  /**
   *
   */
  public static function getRealPath($relativePath) {
    if (substr($relativePath, 0, 1) != '/') {
      $relativePath = '/'.$relativePath;
    }
    return self::getRootPath().$relativePath;
  }

  /**
   * @param value The string to indent
   * @param size The indent size.
   * @param char The indent char.
   * @return The indented value.
   */
  public static function indent($value, $size = 2, $char = ' ') {
    $pad = '';
    while(strlen($pad) < $size) {
      $pad .= $char;
    }
    return $pad.preg_replace("/\n([^\n])/", "\n$pad$1", $value);
  }

  /**
   * @param uri The uri (e.g. /a/b/c)
   * @return The parent uri (e.g /a/b)
   */
  public static function getParentURI($uri) {
    $result = preg_replace("/(.*)\/[^\/]*$/", "$1", $uri);
    if (strlen($result) > 0) {
      return $result;
    }
    return '/';
  }

  /**
   * @return the best language according to the browser language settings.
   */
  public static function getBrowserLanguage() {
    $browserLang = strtok($_SERVER['HTTP_ACCEPT_LANGUAGE'], ','); //language codes are comma delimited
    while ($browserLang) {
      foreach($GLOBALS['language_array'] as $lang) {
        if (strstr($browserLang, $lang)) {
          return $lang;
        }
      }
      $browserLang = strtok(','); //next token
    }
    return $GLOBALS['language_array'][0];
  }


  /**
   * Detect the language
   *
   * @return the detected language.
   */
  public static function detectLanguage($uri) {
    // Is there a language given as a parameter?
    if ($_GET['language']) {
      $source = 'param';
      $lang = $_GET['language'];
    }
    else {
      // Is there only one page existing for the current URI? If yes, select the language associated with.
      $uriLang = PageFactory::getInstance()->checkPageLanguage($uri);
      if (!is_null($uriLang)) {
        $source = 'pagedef';
        $lang = $uriLang;
      }
      else {
        // If there a cookie?
        if ($_COOKIE['language'] && in_array($_COOKIE['language'], $GLOBALS['language_array'])) {
          $source = 'cookie';
          $lang = $_COOKIE['language'];
        }
        else {
          // Check the browser language
          $source = 'browser';
          $lang = Util::getBrowserLanguage();
        }
      }
    }
    return $lang;
  }
}
?>

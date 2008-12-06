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

include_once 'Toolkit.class.php';
include_once 'PageFactory.class.php';
include_once 'Util.class.php';

class FrontController {

  /**
   * Dispatch the current request to the right page (action + template).
   */
  public static function dispatch() {
    if (headers_sent()) {
      return;
    }

    $uri = Toolkit::currentURI();
    $lang = Util::detectLanguage($uri);

    // Set a cookie for the language.
    $path = Util::getWebRoot();
    $path = ($path == '' ? '/' : $path);
    setcookie('language', $lang, time() + 60 * 60 * 24 * 30, $path);

    // Initialize the toolkit.
    Toolkit::getInstance()->init($lang);

    // Try to find the page, or a parent controller page
    $i = 0;
    do {
      $page = PageFactory::getInstance()->getPageByURI($uri);
      $uri = Util::getParentURI($uri);
    }
    while(is_null($page) && $i++ < 10);

    if (is_null($page) || !is_null($page) && !$page->isController() && $i > 0) {
      // Trigger a 404...
      $page = PageFactory::getInstance()->getErrorPage('404');
      if (is_null($page)) {
        // Still no page! Trigger a 500...
        header('HTTP/1.1 500 Internal Server Error');
        print 'Fatal Error: The error name "404" is not defined or the page it referes doesn\'t exist.';
        exit();
      }
      header('HTTP/1.1 404 Not Found');
    }
    else {
      header('HTTP/1.1 200 OK');
    }

    $defaultTemplate = $page->getDefaultTemplate();
    if (!is_null($defaultTemplate)) {
      $templateName = $defaultTemplate->getName();
    }

    $data = array();

    // Call the action
    $action = $page->getActionFile();
    if (!is_null($action) && strlen($action) > 0) {
      $file = Util::getRealPath($action);

      $result = self::includeAction($file);
      if (is_array($result)) {
        if (array_key_exists('template', $result)) {
          $templateName = $result['template'];
        }

        if (array_key_exists('result', $result)) {
          $data = $result['result'];
        }
      }
    }

    // Find the template object
    $template = $page->getTemplate($templateName);
    if (is_null($template)) {
      $template = $defaultTemplate;
    }

    // Render the template
    if (!is_null($template)) {
      $template->render($data);
    }
  }

  /**
   * Include the action.
   *
   * @param file The action file
   */
  private static function includeAction($file) {
    if (file_exists($file)) {
      return include($file);
    }
  }
}

?>
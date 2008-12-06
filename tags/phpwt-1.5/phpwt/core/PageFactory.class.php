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

include_once 'Config.class.php';
include_once 'Page.class.php';
include_once 'Template.class.php';

/**
 * This class is a singleton.
 */
class PageFactory {
  private $cache = array();

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
   * @param name The name of the error page to get.
   * @return the error page for the given name.
   */
  public function getErrorPage($name) {
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/error-pages/error[@name = '$name']");
    $node = $nodes->item(0);
    if (!is_null($node)) {
      $id = $node->getAttribute('id');
      return $this->getPage($id);
    }
  }

  /**
   * @param uri The page uri.
   * @return a page.
   */
  public function getPageByURI($uri) {
    return $this->getPage(self::uriToPageID($uri));
  }

  public function checkPageLanguage($uri) {
    $nodes = Config::getInstance()->doXPathQuery("%BASE_WS_ALL%/page-mappings/page[@uri = '$uri']/../.. | %BASE_WS_ALL%/page-mappings/page/alias[@uri = '$uri']/../..");
    if ($nodes->length == 1) {
      $node = $nodes->item(0);
      $lang = $node->getAttribute('lang');
      if (strlen($lang) > 0) {
        return $lang;
      }
    }
    return null;
  }

  /**
   * @param id The page id.
   * @return a page.
   */
  public function getPage($id) {
    if (!is_null($this->cache['pages'][$id])) {
      return $this->cache['pages'][$id];
    }

    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-definitions/page[@id = '$id']");
    $node = $nodes->item(0);
    if (!is_null($node)) {
      $page = $this->createPage($node);
    }
    else {
      // Check if there are aliases for this page.
      $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-mappings/page/uri[@uri = '$uri']/parent::*");
      $node = $nodes->item(0);
      if (!is_null($node)) {
        $uri = $node->getAttribute('uri');
        $page = $this->getPage($uri);
      }
    }

    $this->cache['pages'][$uri] = $page;
    return $page;
  }

  /**
   * @param uri The uri.
   * @return true if this uri is an alias.
   */
  public function isAlias($uri) {
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-aliases/aliases/alias[@uri = '$uri']");
    return !is_null($nodes->item(0));
  }

  /**
   * @param node A node that represents a page.
   * @return a Page object
   */
  private function createPage($node) {
    $id = $node->getAttribute('id');
    $action = $node->getAttribute('action');
    $iscontroller = $node->getAttribute('iscontroller');

    $page = new Page($id);
    $page->setActionFile($action);
    $page->setIsController($iscontroller);

    foreach($node->childNodes as $childNode) {
      if ($childNode->tagName == 'property') {
        $name = $childNode->getAttribute('name');
        $value = $childNode->getAttribute('value');
        $page->setProperty($name, $value);
      }
    }

    // TEMPLATES
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/template-mappings/page-templates[@id = '$id']/template");

    // Resolve the inheritance
    $extends_resolve = array();
    foreach($nodes as $node) {
      $template = $this->createTemplate($node, $page);
      $page->addTemplate($template);

      if (!is_null($template->getParentName())) {
        array_push($extends_resolve, $template);
      }
    }

    foreach($extends_resolve as $template) {
      $this->resolveTemplateInheritance($template, $page);
    }

    // Which is the default template?
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/template-mappings/page-templates[@id = '$id']");
    foreach($nodes as $node) {
      $default = $node->getAttribute('default');

      // Set the default template
      if (!is_null($default)) {
        $page->setDefaultTemplate($default);
      }
    }

    // ALIASES
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-mappings/page[@id = '$id']");
    foreach($nodes as $node) {
      // Set the URI alias
      $uri = $node->getAttribute('uri');
      if (!is_null($uri)) {
        $page->setURI($uri);
      }
      
      foreach($node->childNodes as $childNode) {
        if ($childNode->tagName == 'uri') {
          $page->addAlias($childNode->getAttribute('path'));
        }
      }

    }

    return $page;
  }

  /**
   * @param node A node that represents a template.
   * @param page The page's owner of this template.
   * @return a template object
   */
  private function createTemplate($node, $page = null) {
    $name = $node->getAttribute('name');
    $parentName = $node->getAttribute('extends');
    $baseFile = $node->getAttribute('base');

    if ($parentName == $name) {
      unset($parentName);
    }

    if (strlen($parentName) > 0) {
      unset($baseFile);
    }

    $template = new Template($name, $parentName);
    $template->setBaseFile($baseFile);
    $template->setPage($page);
    foreach($node->childNodes as $childNode) {
      if (get_class($childNode) == 'DOMElement') {
        if ($childNode->tagName == 'section') {
          $sectionName = $childNode->getAttribute('name');
          if ($childNode->hasAttribute('file')) {
            $file = $childNode->getAttribute('file');
          }
          else {
            $file = null;
          }
          $template->addSection($sectionName, $file);

          foreach($childNode->childNodes as $paramNode) {
            if (get_class($paramNode) == 'DOMElement') {
              if ($paramNode->tagName == 'property') {
                $key = $paramNode->getAttribute('name');
                $value = $paramNode->getAttribute('value');
              }
              else {
                $key = $paramNode->tagName;
                $value = $paramNode->textContent;
              }
              $template->addSectionProperty($sectionName, $key, $value);
            }
          }
        }
      }
    }
    return $template;
  }

  /**
   * Resolves the extend hierarchy for this template. This change the sections
   * of this template.
   *
   * @param template The template to extend
   * @param page The page of this template.
   */
  private function resolveTemplateInheritance($template, $page) {
    $globalTemplates = $this->getGlobalTemplates();

    // This is the "extends" attribute in the XML file.
    $parentName = $template->getParentName();

    $global = false;
    while ($parentName != null) {
      // If we are not already looking in global-templates
      if (!$global) {
        $parentTemplate = $page->getTemplate($parentName);
        if (is_null($parentTemplate)) {
          $global = true;
        }
      }

      //We didn't find the parent in page's templates, we should look in global-templates.
      if ($global) {
        $parentTemplate = $globalTemplates[$parentName];
      }

      if (!is_null($parentTemplate)) {
        // Merge the 2 sections array. Priority to the current template
        $template->mergeForInheritance($parentTemplate);

        $parentName = $parentTemplate->getParentName();
      }
      else {
        unset($parentName);
      }
    }
  }

  /**
   * @return a list of global templates.
   */
  private function getGlobalTemplates() {
    if (!is_null($this->cache['global-templates'])) {
      return $this->cache['global-templates'];
    }

    $templates = array();

    $nodes = Config::getInstance()->doXPathQuery("%BASE%/template-mappings/global-templates/template");
    foreach($nodes as $node) {
      $template = $this->createTemplate($node);
      $templates[$template->getName()] = $template;
    }

    $this->cache['global-templates'] = $templates;
    return $templates;
  }

  /**
   * @param id The ID.
   * @param language The language.
   * @return the page URI.
   */
  public static function pageIDToURI($id, $language = null) {
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-mappings/page[@id = '$id' and @uri]", $language);
    if ($nodes->length > 0) {
      $node = $nodes->item($nodes->length - 1);
      $uri = $node->getAttribute('uri');
      return $uri;
    }
    return null;
  }

  /**
   * @param uri The URI.
   * @return the page ID.
   */
  public static function uriToPageID($uri) {
    // Find the page ID in page-mappings
    $nodes = Config::getInstance()->doXPathQuery("%BASE%/page-mappings/page/alias[@uri = '$uri']/parent::* | %BASE%/page-mappings/page[@uri = '$uri']");
    $node = $nodes->item(0);
    if (!is_null($node)) {
      return $node->getAttribute('id');
    }
    else {
      // Try to find it in other languages
      $nodes = Config::getInstance()->doXPathQuery("%BASE_OTHER%/page-mappings/page/alias[@uri = '$uri']/parent::* | %BASE_OTHER%/page-mappings/page[@uri = '$uri']");
      $node = $nodes->item(0);
      if (!is_null($node)) {
        return $node->getAttribute('id');
      }
    }
    return null;
  }
}

?>
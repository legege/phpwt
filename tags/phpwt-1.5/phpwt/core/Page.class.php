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

/**
 * This class represents a page.
 */
class Page {
  // Page definition
  private $id;
  private $uri;
  private $actionFile;
  private $properties = array();
  private $iscontroller = false;

  // Page templates
  private $templates = array();
  private $defaultTemplateName;

  // Page aliases
  private $aliases = array();

  /**
   * Public constructor
   *
   * @param id The ID for this page
   */
  public function __construct($id) {
    $this->id = $id;
  }

  /**
   * @return the ID for this page
   */
  public function getID() {
    return $this->id;
  }

  /**
   * @param iscontroller Set if this page is a controller and should handle children uri.
   */
  public function setIsController($iscontroller) {
    $this->iscontroller = $iscontroller;
  }

  /**
   * @return if the page is a controller or not.
   */
  public function isController() {
    return $this->iscontroller;
  }

  /**
   * @param actionFile The action file
   */
  public function setActionFile($actionFile) {
    $this->actionFile = $actionFile;
  }

  /**
   * @return the action file.
   */
  public function getActionFile() {
    return $this->actionFile;
  }


  /**
   * @param key The key for the property to set
   * @param value The value
   */
  public function setProperty($key, $value) {
    $this->properties[strtolower($key)] = $value;
  }

  /**
   * @param key The key for the property to get
   * @return the value
   */
  public function getProperty($key) {
    return $this->properties[strtolower($key)];
  }

  /**
   * @param template The Template to add.
   */
  public function addTemplate($template) {
    $this->templates[$template->getName()] = $template;
  }

  /**
   * @param name The name of the template to get.
   * @return the template of the given name.
   */
  public function getTemplate($name) {
    if (array_key_exists($name, $this->templates)) {
      return $this->templates[$name];
    }
  }

  /**
   * @return all templates of this page.
   */
  public function getTemplates() {
    return $this->templates;
  }

  /**
   * @param name The default template name
   */
  public function setDefaultTemplate($name) {
    if (array_key_exists($name, $this->templates)) {
      $this->defaultTemplateName = $name;
    }
  }

  /**
   * @return the default template name
   */
  public function getDefaultTemplate() {
    if (!is_null($this->defaultTemplateName)) {
      return $this->templates[$this->defaultTemplateName];
    }
  }

  /**
   * @param uri The alias URI to add.
   */
  public function addAlias($uri) {
    array_push($this->aliases, $uri);
  }

  /**
   * @param uri The URI
   */
  public function setURI($uri) {
    $this->uri = $uri;
  }
}
?>

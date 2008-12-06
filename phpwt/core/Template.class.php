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

class Template {
  private $name;
  private $parentName;
  private $baseFile;
  private $sections = array();
  private $pageOwner;
  private $currentSectionName;

  /**
   * Public constructor
   *
   * @param name The name for this template.
   * @param parentName The parent template's name.
   * @param baseFile The base file for this template.
   */
  public function __construct($name, $parentName = '') {
    $this->name = $name;
    $this->parentName = $parentName;
  }

  /**
   * @return Returns the name.
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @return Returns the parent template's name.
   */
  public function getParentName() {
    return $this->parentName;
  }

  /**
   * @return Returns the base file for this template.
   */
  public function getBaseFile() {
    return $this->baseFile;
  }

  /**
   * @param baseFile The base file to set.
   */
  public function setBaseFile($baseFile) {
    $this->baseFile = $baseFile;
  }


  /**
   * @return Returns the page owner of this template.
   */
  public function getPage() {
    return $this->pageOwner;
  }

  /**
   * @param pageOwner The page owner for this template to set.
   */
  public function setPage($pageOwner) {
    $this->pageOwner = $pageOwner;
  }

  /**
   * Add a section definition (file).
   *
   * @param sectionName The section name.
   * @param file The definition of this section.
   */
  public function addSection($sectionName, $file) {
    $this->sections[$sectionName]['file'] = $file;
  }

  /**
   * @param sectionName The section name.
   * @return Returns the section definition (file) for the given key.
   */
  public function getSectionFile($sectionName) {
    if (array_key_exists($sectionName, $this->sections)) {
      return $this->sections[$sectionName]['file'];
    }
  }

  /**
   * Add a property for a section.
   *
   * @param sectionName The section name.
   * @param key The key for this property
   * @param value The value
   */
  public function addSectionProperty($sectionName, $key, $value) {
    if (array_key_exists($sectionName, $this->sections)) {
      $this->sections[$sectionName]['properties'][$key] = $value;
    }
  }

  /**
   * Add a property for a section.
   *
   * @param sectionName The section name.
   * @param key The key for this property
   * @return Returns the value
   */
  public function getSectionProperty($sectionName, $key) {
    if (array_key_exists($sectionName, $this->sections)) {
      return $this->sections[$sectionName]['properties'][$key];
    }
  }

  /**
   * Merge the parent template with this one.
   *
   * @param parentTemplate The parent template.
   */
  public function mergeForInheritance($parentTemplate) {
    $this->baseFile = $parentTemplate->baseFile;

    foreach ($parentTemplate->sections as $sectionName => $sectionDef) {
      if (array_key_exists($sectionName, $this->sections)) {


        // Merge the "file"
        if (is_null($this->sections[$sectionName]['file'])) {
          $this->sections[$sectionName]['file'] = $sectionDef['file'];
        }

        // Merge the values
        if (is_array($sectionDef['values'])) {
          foreach($sectionDef['values'] as $key => $value) {
            if (is_null($this->sections[$sectionName]['values'][$key])) {
              $this->sections[$sectionName]['values'][$key] = $value;
            }
          }
        }
      }
      else {
        $this->sections[$sectionName] = $sectionDef;
      }
    }
  }

  /**
   * Render the template.
   */
  public function render($actionData) {
    global $currentActionData;
    $currentActionData = $actionData;

    include_once 'TemplateFunctions.inc.php';

    $this->import(Util::getRealPath($this->baseFile));
    unset($this->currentActionData);
  }

  /**
   * @return Returns the current section name.
   */
  public function getCurrentSectionName() {
    return $this->currentSectionName;
  }

  /**
   * @return Returns the current section file.
   */
  public function getCurrentSectionFile() {
    return $this->getSectionFile($this->getCurrentSectionName());
  }

  /**
   * Import a section.
   *
   * @param sectionName The section name for the section to import.
   */
  protected function includeSection($sectionName) {
    $file = $this->getSectionFile($sectionName);
    if (!is_null($file)) {
      $tmpSectionName = $this->currentSectionName;
      $this->currentSectionName = $sectionName;
      $this->import(Util::getRealPath($file));
      $this->currentSectionName = $tmpSectionName;
    }
  }

  /**
   * @param sectionName The sectionName.
   * @return Returns true if the section is defined.
   */
  protected function isSectionDefined($sectionName) {
    $file = $this->getSectionFile($sectionName);
    return $file != null && $file != '';
  }

  /**
   * Imports a file.
   *
   * @param file The file to import.
   */
  private function import($file) {
    if (file_exists($file) && is_file($file)) {
      global $currentTemplate;
      $currentTemplate = $this;
      include $file;
      echo "\n";
    }
  }
}

?>
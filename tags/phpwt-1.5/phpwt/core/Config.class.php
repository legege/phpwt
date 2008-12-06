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

include_once 'Util.class.php';

/**
 * Retrives information from the config file.
 * This class is a singleton.
 */
class Config {
  protected $dom;
  protected $xp;

  /**
   * Default protected constructor.
   */
  protected function __construct() {
    $xml = new DomDocument();
    $xml->load($GLOBALS['toolkit_config']);

    $xsl = new DomDocument();
    $xsl->load(dirname(__FILE__).'/config.xslt');

    $proc = new XsltProcessor();
    $proc->importStylesheet($xsl);
    $this->dom = $proc->transformToDoc($xml);

    $this->xp = new domxpath($this->dom);
  }

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
   * @param name The name of the resource file.
   */
  public function getResourceFile($name) {
    $nodes = $this->doXPathQuery("%BASE%/resource[@name = '$name']");
    $node = $nodes->item(0);
    if (!is_null($node)) {
      return Util::getRealPath($node->getAttribute('file'));
    }
  }

  /**
   * Execute a XPath query on the XML Config file. The website tag with
   * the lang attribute has the priority.
   *
   * @param query The XPath Query to execute on the XML Config file.
   * @param lang
   * @return the result.
   */
  public function doXPathQuery($query, $lang = null) {
    if (is_null($lang)) {
      $lang = Toolkit::getInstance()->getLanguage();
    }

    $query = str_replace("%BASE%", "/toolkit/website[@lang='$lang' or not(@lang)]", $query);
    $query = str_replace("%BASE_OTHER%", "/toolkit/website[@lang!='$lang' and @lang]", $query);
    $query = str_replace("%BASE_WS_ALL%", "/toolkit/website", $query);
    $nodes = $this->xp->query($query);
    return $nodes;
  }
}

?>
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
include_once 'PageFactory.class.php';

/**
 * This class is a singleton.
 */
class PageHierarchy {

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
   * @param id The page id.
   * @return an array list of root pages with special fields.
   */
  public function getAncestorOrSelfPages($id) {
    // Is there a mapping for this page?
    $page = PageFactory::getInstance()->getPage($id);
    if (is_null($page)) {
      return array();
    }

    $id = $page->getID();

    // Get nodes
	  $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy//page[@id = '$id']/ancestor-or-self::page");

    $result = array();
    foreach($nodes as $node) {
      $pageID = $node->getAttribute('id');
      $page = PageFactory::getInstance()->getPage($pageID);

      // Is there a mapping for this page?
      if (!is_null($page)) {
        $entry = array();
        $entry['visibility'] = $node->getAttribute('visibility');
        $entry['page'] = $page;
        array_push($result, $entry);
      }
    }

    return $result;
  }

  /**
   * @param currentPageID The current page id (used to detect the current selection).
   * @return an array list of root pages with special fields.
   */
  public function getRootPages($currentPageID) {    
    return $this->getHierarchy($currentPageID, 0);
  }

  /**
   * @param currentPageID The current page id (used to detect the current selection).
   * @param maxDepth The maximum depth
   * @param parentPageID The parent page id for this hierarchy.
   * @param includeParentPage Should the parent page be included?
   * @param includeParentSiblings Should the parent siblings be included? Only work if parent is included.
   * @param depth The current depth (internal use only).
   * @return the page hierarchy.
   */
  public function getHierarchy($currentPageID, $maxDepth = -1, $parentPageID = '', $includeParentPage = false, $includeParentSiblings = false, $depth = 0) {
    if (strlen($parentPageID) > 0) {
      // Is there a mapping for this page?
      $parentPage = PageFactory::getInstance()->getPage($parentPageID);
      if (is_null($parentPage)) {
        return array();
      }
      $parentPageID = $parentPage->getID();

      $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy//page[@id = '$parentPageID']/page");
    }
    else {
      $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy/page");
    }

    $result = array();
    foreach($nodes as $node) {
      $pageID = $node->getAttribute('id');

      if (!PageFactory::getInstance()->isAlias($pageID)) {
        $page = PageFactory::getInstance()->getPage($pageID);

        // Is there a mapping for this page?
        if (!is_null($page)) {
          $entry = array();
          $entry['visibility'] = $node->getAttribute('visibility');
          $entry['page'] = $page;
          $entry['selected'] = ($currentPageID == $pageID);
          $entry['child-selected'] = $this->isChild($currentPageID, $pageID);

          if ($maxDepth == -1 || $depth < $maxDepth) {
            $entry['child'] = $this->getHierarchy($currentPageID, $maxDepth, $pageID, false, $depth + 1);
          }

          array_push($result, $entry);
        }
      }
    }

    if ($includeParentPage && strlen($parentPageID) > 0) {
      $previous_result = $result;
      $result = array();

      $siblings = $this->getPageSiblings($parentPageID);
      foreach ($siblings as $sibling_entry) {
        if ($sibling_entry['page']->getID() == $parentPageID) {
          $sibling_entry['selected'] = ($currentPageID == $parentPageID);
          $sibling_entry['child-selected'] = true;
          $sibling_entry['child'] = $previous_result;

          array_push($result, $sibling_entry);
        } else if ($includeParentSiblings) {
          array_push($result, $sibling_entry);
        }
      }
    }

    return $result;
  }

  /**
   * @param id The page id.
   * @return an array list or sibling pages for the page having the given id.
   */
  public function getPageSiblings($id) {
    // Is there a mapping for this page?
    $page = PageFactory::getInstance()->getPage($id);
    if (is_null($page)) {
      return array();
    }

    $id = $page->getID();

    // Get sibling nodes
    $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy//page[@id = '$id']/parent::node()/child::page");
    if ($nodes->length == 0) {
      $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy/page");
    }

    $result = array();
    foreach ($nodes as $node) {
      $pageID = $node->getAttribute('id');
      $page = PageFactory::getInstance()->getPage($pageID);

      // Is there a mapping for this page?
      if (!is_null($page)) {
        $entry = array();
        $entry['visibility'] = $node->getAttribute('visibility');
        $entry['page'] = $page;
        $entry['selected'] = ($id == $pageID);
        array_push($result, $entry);
      }
    }

    return $result;
  }

  /**
   * @return true if childId is a child of parentId.
   */
  private function isChild($id, $parentID) {
    $nodes =  Config::getInstance()->doXPathQuery("%BASE%/hierarchy//page[@id = '$parentID']//page[@id = '$id']");
    return !is_null($nodes->item(0));
  }
}

?>

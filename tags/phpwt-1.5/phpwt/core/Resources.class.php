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

/**
 * This class read values from a resource file.
 */
class Resources {
  private static $res = array();

  /**
   * @param key The key
   * @param name The name of the resource file.
   * @return the string associated with the key in the specified resource.
   */
  public static function getString($key, $name) {
    if (self::$res[$name] == null) {
      self::$res[$name] = @parse_ini_file(Config::getInstance()->getResourceFile($name));
    }

    $value = self::$res[$name][$key];
    return ($value == null) ? $key : $value;
  }
}

?>
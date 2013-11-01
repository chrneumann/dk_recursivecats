<?php
/**
 *
 *    This file is part of Recursive Categories.
 *
 *    Copyright (c) 2013, Christian Neumann <cneumann@datenkarussell.de>
 *    All rights reserved.
 *
 *    Recursive Categories is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    Recursive Categories is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with Recursive Categories.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Extends the oxseoencoderarticle model.
 * 
 * Returns correct URIs for articles below the active category.
 */
class dk_recursivecats_oxseoencoderarticle
extends dk_recursivecats_oxseoencoderarticle_parent
{
  /**
   * Returns active category if available
   *
   * @param oxArticle $oArticle product
   * @param int       $iLang    language id
   *
   * @return oxCategory | null
   */
  protected function _getCategory( $oArticle, $iLang )
  {
    if ($oArticle->oxarticles__oxcatnid) {
      $oCat = oxNew('oxcategory');
      $oCat->load($oArticle->oxarticles__oxcatnid->value);
      return $oCat;
    }
    return parent::_getCategory($oArticle, $iLang);
  }
}
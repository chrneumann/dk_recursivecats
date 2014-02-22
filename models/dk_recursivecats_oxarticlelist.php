<?php
/**
 *
 *    This file is part of Recursive Categories and contains code from OXID 
 *    eShop Community Edition 4.7.7.
 *
 *    Copyright (c) 2013, Christian Neumann <cneumann@datenkarussell.de>
 *    Copyright (c) 2003-2013, OXID eSales AG
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
 * Extends the oxarticlelist model with recursive article loaders.
 */
class dk_recursivecats_oxarticlelist
extends dk_recursivecats_oxarticlelist_parent
{
  /**
   * Loads articles for the given Categories
   *
   * @param array  $aCatIds        Category tree IDs
   * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
   * @param int    $iLimit         Limit
	 * @param bool   $blRecursive If true, include articles ob subcategories.
   *
   * @return integer total Count of Articles in this Category
   */
	public function loadCategoriesArticles( $aCatIds, $aSessionFilter,
                                          $iLimit = null )
  {
    $sArticleFields = $this->getBaseObject()->getSelectFields();
    $sArticleFields .= ", `oxcatnid`";

    $sSelect = $this->_getCategoriesSelect( $sArticleFields, $aCatIds, $aSessionFilter );
		
		$sCntSelect = $this->_getCategoriesCountSelect( $aCatIds, $aSessionFilter );
    $iArticleCount = oxDb::getDb()->getOne( $sCntSelect );

    if ($iLimit = (int) $iLimit) {
      $sSelect .= " LIMIT $iLimit";
    }

    $this->selectString( $sSelect );

    return $iArticleCount;
  }

  /**
   * Creates SQL Statement to load Articles from multiple categories, etc.
   *
   * @param string $sFields        Fields which are loaded e.g. "oxid" or "*" etc.
   * @param string $aCatId         Category tree IDs
   * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
   *
   * @return string SQL
   */
  protected function _getCategoriesSelect( $sFields, $aCatIds, $aSessionFilter )
  {
    $sArticleTable = getViewName( 'oxarticles' );
    $sO2CView      = getViewName( 'oxobject2category' );

    // ----------------------------------
    // sorting
    $sSorting = '';
    if ( $this->_sCustomSorting ) {
      $sSorting = " {$this->_sCustomSorting} , ";
    }

    // ----------------------------------
    // filtering ?
    $sFilterSql = '';
    $iLang = oxRegistry::getLang()->getBaseLanguage();
    if ( $aSessionFilter && isset( $aSessionFilter[$sCatId][$iLang] ) ) {
      $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
    }

    $oDb = oxDb::getDb();

		$sCategories = "and (0";
		foreach ( $aCatIds as $sCatId ) {
			$sCategories .= " or oc.oxcatnid = ".$oDb->quote($sCatId);
		}
		$sCategories .= ") ";

    $sSelect = "SELECT $sFields FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE ".$this->getBaseObject()->getSqlActiveSnippet()." and $sArticleTable.oxparentid = ''
                    $sCategories $sFilterSql GROUP BY `oxid` ORDER BY $sSorting oc.oxpos, oc.oxobjectid ";
    return $sSelect;
  }


  /**
   * Creates SQL Statement to load Articles Count for multiple categories, etc.
   *
   * @param string $aCatIds        Category tree IDs
   * @param array  $aSessionFilter Like array ( catid => array( attrid => value,...))
   *
   * @return string SQL
   */
  protected function _getCategoriesCountSelect( $aCatIds, $aSessionFilter )
  {
    $sArticleTable = getViewName( 'oxarticles' );
    $sO2CView      = getViewName( 'oxobject2category' );


    // ----------------------------------
    // filtering ?
    $sFilterSql = '';
    $iLang = oxRegistry::getLang()->getBaseLanguage();
    if ( $aSessionFilter && isset( $aSessionFilter[$sCatId][$iLang] ) ) {
      $sFilterSql = $this->_getFilterSql($sCatId, $aSessionFilter[$sCatId][$iLang]);
    }

    $oDb = oxDb::getDb();

		$sCategories = "and (0";
		foreach ( $aCatIds as $sCatId ) {
			$sCategories .= " or oc.oxcatnid = ".$oDb->quote($sCatId);
		}
		$sCategories .= ") ";

    $sSelect = "SELECT COUNT(*) FROM (SELECT `oxobjectid` FROM $sO2CView as oc left join $sArticleTable
                    ON $sArticleTable.oxid = oc.oxobjectid
                    WHERE ".$this->getBaseObject()->getSqlActiveSnippet()." and $sArticleTable.oxparentid = ''
                    $sCategories $sFilterSql GROUP BY `oxobjectid`) AS Z ";

    return $sSelect;
  }
}

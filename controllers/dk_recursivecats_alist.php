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
 * Override alist controller to recursively list articles.
 */
class dk_recursivecats_alist extends dk_recursivecats_alist_parent
{
    /**
     * Loads and returns article list of active category and its subcategories.
     *
     * @param string $oCategory category object
     *
     * @return array
     */
    protected function _loadArticles($oCategory)
    {
        $myConfig = $this->getConfig();

        $iNrofCatArticles = (int) $myConfig->getConfigParam('iNrofCatArticles');
        $iNrofCatArticles = $iNrofCatArticles?$iNrofCatArticles:1;

        // load only articles which we show on screen
        $oArtList = oxNew('oxarticlelist');
        $oArtList->setSqlLimit($iNrofCatArticles * $this->_getRequestPageNr(), $iNrofCatArticles);
        $oArtList->setCustomSorting($this->getSortingSql($this->getSortIdent()));

        if ($oCategory->isPriceCategory()) {
            $dPriceFrom = $oCategory->oxcategories__oxpricefrom->value;
            $dPriceTo   = $oCategory->oxcategories__oxpriceto->value;

            $this->_iAllArtCnt = $oArtList->loadPriceArticles($dPriceFrom, $dPriceTo, $oCategory);
        } else {
            $aSessionFilter = oxSession::getVariable('session_attrfilter');

            $sActCat = $oCategory->oxcategories__oxid->value;
            $aCatIds = array();
            foreach ($oCategory->getSubCats() as $oSubCat) {
                $aCatIds[] = $oSubCat->oxcategories__oxid->value;
            }
            $aCatIds[] = $sActCat;
            $this->_iAllArtCnt = $oArtList->loadCategoriesArticles($aCatIds, $aSessionFilter);
        }

        $this->_iCntPages = round($this->_iAllArtCnt/$iNrofCatArticles + 0.49);

        return $oArtList;
    }
}

<?php
class Flipmedia_CategoryLink_Block_Link
    extends Mage_Core_Block_Html_Link
    implements Mage_Widget_Block_Interface
{
    /**
     * Entity model name which must be used to retrieve entity specific data.
     * @var null|Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected $_entityResource = null;

    /**
     * Prepared href attribute
     *
     * @var string
     */
    protected $_href;

    /**
     * Prepare url using passed id and return it
     * or return false if path was not found.
     *
     * @return string|false
     */
    public function getHref()
    {
        if (!$this->_href) {

            if($this->hasStoreId()) {
                $store = Mage::app()->getStore($this->getStoreId());
            } else {
                $store = Mage::app()->getStore();
            }

            $idPath = explode('/', $this->_getData('id_path'));

            if (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'product') {

                /** @var $helper Mage_Catalog_Helper_Product */
                $helper = $this->_getFactory()->getHelper('catalog/product');
                $productId = $idPath[1];
                $categoryId = isset($idPath[2]) ? $idPath[2] : null;

                $this->_href = $helper->getFullProductUrl($productId, $categoryId);

            } elseif (isset($idPath[0]) && isset($idPath[1]) && $idPath[0] == 'category') {
                $categoryId = $idPath[1];
                if ($categoryId) {
                    /** @var $helper Mage_Catalog_Helper_Category */
                    $helper = $this->_getFactory()->getHelper('catalog/category');
                    $category = Mage::getModel('catalog/category')->load($categoryId);
                    $this->_href = $helper->getCategoryUrl($category);
                }
            }
        }

        if ($this->_href) {
            if (strpos($this->_href, "___store") === false) {
                $symbol = (strpos($this->_href, "?") === false) ? "?" : "&";
                $this->_href = $this->_href . $symbol . "___store=" . $store->getCode();
            }
        } else {
            return false;
        }

        return $this->_href;
    }

    /**
     * Render block HTML - return link
     * or return empty string if url can't be prepared
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($href = $this->getHref()) {
            return $href;
        }
        return '#';
    }
}

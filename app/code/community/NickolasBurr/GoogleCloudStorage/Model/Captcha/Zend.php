<?php
/**
 * Zend.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License, which
 * is bundled with this package in the file LICENSE.txt.
 *
 * It is also available on the Internet at the following URL:
 * https://docs.nickolasburr.com/magento/extensions/1.x/magegcs/LICENSE.txt
 *
 * @package        NickolasBurr_GoogleCloudStorage
 * @copyright      Copyright (C) 2018 Nickolas Burr <nickolasburr@gmail.com>
 * @license        MIT License
 */

class NickolasBurr_GoogleCloudStorage_Model_Captcha_Zend extends Mage_Captcha_Model_Zend
{
    /**
     * Upload generated captcha image to GCS bucket.
     *
     * @param string $id
     * @param string $word
     */
    protected function _generateImage($id, $word)
    {
        parent::_generateImage($id, $word);

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage_Database $helper */
        $helper = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_CORE_FILE_STORAGE_DATABASE);

        if ($helper->checkBucketUsage()) {
            /** @var string $path */
            $path = $this->getImgDir() . $this->getId() . $this->getSuffix();

            /* Save captcha image file. */
            $helper->saveFile($path);
        }
    }
}

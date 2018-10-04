<?php
/**
 * Storage.php
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

class NickolasBurr_GoogleCloudStorage_Model_Adminhtml_System_Config_Source_Storage_Media_Storage
    extends Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage
{
    /**
     * Get array of configuration options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = array(
            'value' => NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS,
            'label' => Mage::helper('magegcs')->__('Google Cloud Storage')
        );

        return $options;
    }
}

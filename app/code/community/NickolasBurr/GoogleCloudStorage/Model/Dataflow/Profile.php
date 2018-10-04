<?php
/**
 * Profile.php
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

class NickolasBurr_GoogleCloudStorage_Model_Dataflow_Profile extends Mage_Dataflow_Model_Profile
{
    /**
     * Workaround to disable Mage_Dataflow from uploading CSVs to GCS.
     *
     * @see Thai_S3_Model_Dataflow_Profile::_afterSave
     */
    protected function _afterSave()
    {
        /** @var NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage $helper */
        $helper = Mage::helper('core/file_storage');

        /** @var array $storageList */
        $storageList = $helper->getInternalStorageList();

        $helper->setInternalStorageList(
            \array_merge(
                $storageList,
                array(NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS)
            )
        );

        parent::_afterSave();

        /* Restore storage list to original state. */
        $helper->setInternalStorageList($storageList);
    }
}

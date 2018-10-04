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

class NickolasBurr_GoogleCloudStorage_Helper_Core_File_Storage extends Mage_Core_Helper_File_Storage
{
    /**
     * Workaround to disable Mage_Dataflow from uploading CSVs to GCS.
     *
     * @return array
     * @see Thai_S3_Helper_Core_File_Storage::getInternalStorageList
     */
    public function getInternalStorageList()
    {
        return $this->_internalStorageList;
    }

    /**
     * Workaround to disable Mage_Dataflow from uploading CSVs to GCS.
     *
     * @param array $internalStorageList
     * @see Thai_S3_Helper_Core_File_Storage::setInternalStorageList
     */
    public function setInternalStorageList(array $internalStorageList)
    {
        $this->_internalStorageList = $internalStorageList;
    }
}

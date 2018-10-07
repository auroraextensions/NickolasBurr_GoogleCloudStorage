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

class NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage extends Mage_Core_Model_File_Storage
{
    /** @property string $_eventPrefix */
    protected $_eventPrefix = 'magegcs_core_file_storage';

    /**
     * Retrieve storage model.
     *
     * @param int|null $storageCode
     * @param array $params
     * @return Mage_Core_Model_File_Storage
     * @see Mage_Core_Model_File_Storage::getStorageModel
     */
    public function getStorageModel($storageCode = null, $params = array())
    {
        /** @var Mage_Core_Model_File_Storage|bool $storageModel */
        $storageModel = parent::getStorageModel($storageCode, $params);

        if (!$storageModel) {
            $storageCode = $storageCode !== null ? $storageCode : Mage::helper('core/file_storage')->getCurrentStorageCode();

            switch ($storageCode) {
                case NickolasBurr_GoogleCloudStorage_Helper_Dict::STORAGE_MEDIA_GCS:
                    /** @var NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Bucket $storageModel */
                    $storageModel = Mage::getModel(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_MODEL_CORE_FILE_STORAGE_BUCKET);
                    break;
                default:
                    return false;
            }

            if (isset($params['init']) && $params['init']) {
                $storageModel->init();
            }
        }

        return $storageModel;
    }
}

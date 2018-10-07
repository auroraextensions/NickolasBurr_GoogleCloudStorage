<?php
/**
 * File.php
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

class NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_File extends Mage_Core_Model_File_Storage_File
{
    /** @property string $_eventPrefix */
    protected $_eventPrefix = 'magegcs_core_file_storage_file';

    /** @property array $_errors */
    protected $_errors = array();

    /**
     * Get initialized class instance.
     */
    public function init()
    {
        return $this;
    }

    /**
     * Save file object to GCS bucket.
     *
     * @param mixed $file
     * @param bool $overwrite
     * @return bool
     */
    public function saveFile($file, $overwrite = false)
    {
        if (parent::saveFile($file, $overwrite)) {
            /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
            $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

            try {
                $filePath = (isset($file['directory']) && !empty($file['directory']))
                    ? $file['directory'] . DS . $file['filename']
                    : $file['filename'];

                /** @var string $content */
                $content = $file['content'];

                /* Upload file object to GCS bucket. */
                $storage->uploadToBucket(
                    $content,
                    array(
                        'name'          => Mage::helper('core/file_storage_database')->getMediaRelativePath($filePath),
                        'predefinedAcl' => $storage->getBucketAcl(),
                    )
                );

                if (!$storage->objectExists($filePath)) {
                    Mage::throwException(Mage::helper('core')->__('Unable to save file to bucket: %s', $filePath));
                }

                return true;
            } catch (Mage_Core_Exception $e) {
                $this->_errors[] = $e->getMessage();
                Mage::logException($e);
            }
        }

        return false;
    }
}

<?php
/**
 * Database.php
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

class NickolasBurr_GoogleCloudStorage_Model_Core_File_Storage_Directory_Database extends Mage_Core_Model_File_Storage_Directory_Database
{
    /**
     * Get subdirectories under $directory.
     *
     * @param string $directory
     * @return array
     * @see Mage_Core_Model_File_Storage_Directory_Database::getSubdirectories
     */
    public function getSubdirectories($directory)
    {
        /** @var string $directory */
        $directory = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);

        try {
            return $this->_getResource()->getSubdirectories($directory);
        } catch (\Exception $e) {
            return array();
        }
    }
}

<?php
/**
 * Bucket.php
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

class NickolasBurr_GoogleCloudStorage_Model_Resource_Core_File_Storage_Bucket
{
    /**
     * Delete directory from GCS bucket.
     *
     * @param string $dirname
     */
    public function deleteFolder($dirname = '')
    {
        /* Trim trailing slash from $dirname. */
        $dirname = \rtrim($dirname, '/');

        if (!\strlen($dirname)) {
            return;
        }

        /* Append slash back to $dirname. */
        $dirname .= '/';

        /** @var NickolasBurr_GoogleCloudStorage_Helper_Storage $storage */
        $storage = Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE);

        /* Delete all objects with $dirname prefix. */
        $storage->deleteAllObjects(array('prefix' => $dirname));
    }
}

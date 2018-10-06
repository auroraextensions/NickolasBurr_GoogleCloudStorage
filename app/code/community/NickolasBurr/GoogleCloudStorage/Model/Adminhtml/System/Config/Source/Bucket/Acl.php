<?php
/**
 * Acl.php
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

class NickolasBurr_GoogleCloudStorage_Model_Adminhtml_System_Config_Source_Bucket_Acl
{
    /**
     * Get array of predefined ACLs.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::helper(NickolasBurr_GoogleCloudStorage_Helper_Dict::XML_PATH_HELPER_STORAGE)->getAcls();
    }
}

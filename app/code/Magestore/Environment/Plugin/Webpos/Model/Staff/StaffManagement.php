<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Environment\Plugin\Webpos\Model\Staff;


class StaffManagement
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * StaffManagement constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magestore\Webpos\Model\Staff\StaffManagement $subject
     * @param \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface $loginResult
     * @param \Magestore\Appadmin\Api\Data\Staff\StaffInterface $staff
     * @return \Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface $loginResult
     */
    public function afterLogin($subject, $loginResult, $staff)
    {
        $isSharingAccount = $this->scopeConfig->getValue('magestore_environment/webpos/sharing_acount');
        if (!$isSharingAccount) {
            return $loginResult;
        }
        if (is_subclass_of($loginResult, '\Magestore\Webpos\Api\Data\Staff\Login\LoginResultInterface')) {
            if ($loginResult->getSharingAccount() != 1) {
                $loginResult->setSharingAccount(1);
                $locations = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('Magestore\Webpos\Api\Location\LocationRepositoryInterface')
                    ->getListLocationWithStaff($loginResult->getStaffId());
                if (!$locations) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('No POS available'));
                }
                $loginResult->setLocations($locations);
            }
        }
        return $loginResult;
    }
}
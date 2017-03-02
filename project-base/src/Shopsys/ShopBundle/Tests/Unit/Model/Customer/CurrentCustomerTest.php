<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Customer\BillingAddress;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurrentCustomerTest extends PHPUnit_Framework_TestCase
{
    public function testGetPricingGroupForUnregisteredCustomerReturnsDefaultPricingGroup()
    {
        $expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);

        $tokenStorageMock = $this->getMock(TokenStorage::class, [], [], '', false);
        $pricingGroupSettingFacadeMock = $this->getPricingGroupSettingFacadeMockReturningDefaultPricingGroup($expectedPricingGroup);

        $currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupSettingFacadeMock);

        $pricingGroup = $currentCustomer->getPricingGroup();
        $this->assertSame($expectedPricingGroup, $pricingGroup);
    }

    public function testGetPricingGroupForRegisteredCustomerReturnsHisPricingGroup()
    {
        $expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);
        $user = $this->getUserWithPricingGroup($expectedPricingGroup);

        $tokenStorageMock = $this->getTokenStorageMockForUser($user);
        $pricingGroupFacadeMock = $this->getMock(PricingGroupSettingFacade::class, [], [], '', false);

        $currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupFacadeMock);

        $pricingGroup = $currentCustomer->getPricingGroup();
        $this->assertSame($expectedPricingGroup, $pricingGroup);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $defaultPricingGroup
     * @return \PHPUnit_Framework_MockObject_MockObject|\Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private function getPricingGroupSettingFacadeMockReturningDefaultPricingGroup(PricingGroup $defaultPricingGroup)
    {
        $pricingGroupSettingFacadeMock = $this->getMockBuilder(PricingGroupSettingFacade::class)
            ->setMethods(['getDefaultPricingGroupByCurrentDomain'])
            ->disableOriginalConstructor()
            ->getMock();

        $pricingGroupSettingFacadeMock
            ->method('getDefaultPricingGroupByCurrentDomain')
            ->willReturn($defaultPricingGroup);

        return $pricingGroupSettingFacadeMock;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Customer\User
     */
    private function getUserWithPricingGroup(PricingGroup $pricingGroup)
    {
        $billingAddress = $this->getMock(BillingAddress::class, [], [], '', false);
        $userData = new UserData();
        $userData->pricingGroup = $pricingGroup;

        return new User($userData, $billingAddress, null);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Customer\User $user
     * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private function getTokenStorageMockForUser(User $user)
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->setMethods(['getUser'])
            ->getMockForAbstractClass();
        $tokenMock->method('getUser')->willReturn($user);

        $tokenStorageMock = $this->getMock(TokenStorage::class, ['getToken'], [], '', false);
        $tokenStorageMock->method('getToken')->willReturn($tokenMock);

        return $tokenStorageMock;
    }
}
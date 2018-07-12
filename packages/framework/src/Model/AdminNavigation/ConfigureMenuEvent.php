<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

class ConfigureMenuEvent extends Event
{
    const CONFIGURE_ROOT = 'shopsys.menu.configure_root';
    const CONFIGURE_DASHBOARD = 'shopsys.menu.configure_dashboard';
    const CONFIGURE_ORDERS = 'shopsys.menu.configure_orders';
    const CONFIGURE_CUSTOMERS = 'shopsys.menu.configure_customers';
    const CONFIGURE_PRODUCTS = 'shopsys.menu.configure_products';
    const CONFIGURE_PRICING = 'shopsys.menu.configure_pricing';
    const CONFIGURE_MARKETING = 'shopsys.menu.configure_marketing';
    const CONFIGURE_ADMINISTRATORS = 'shopsys.menu.configure_administrators';
    const CONFIGURE_SETTINGS = 'shopsys.menu.configure_settings';

    /**
     * @var \Knp\Menu\FactoryInterface
     */
    private $menuFactory;

    /**
     * @var \Knp\Menu\ItemInterface
     */
    private $menu;

    public function __construct(FactoryInterface $menuFactory, ItemInterface $menu)
    {
        $this->menuFactory = $menuFactory;
        $this->menu = $menu;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getMenuFactory(): FactoryInterface
    {
        return $this->menuFactory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu(): ItemInterface
    {
        return $this->menu;
    }
}

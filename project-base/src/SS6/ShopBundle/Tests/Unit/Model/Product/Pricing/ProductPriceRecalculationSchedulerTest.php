<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductPriceRecalculationSchedulerTest extends PHPUnit_Framework_TestCase {

	public function testProductCanBeScheduledForImmediateRecalculation() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
		$products = $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation();

		$this->assertCount(1, $products);
		$this->assertSame($productMock, array_pop($products));
	}

	public function testImmediateRecalculationScheduleCanBeCleaned() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
		$productPriceRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
		$products = $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation();

		$this->assertCount(0, $products);
	}

	public function testAllProductsCanBeScheduledForDelayedRecalculation() {
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$productsIterator = [$productMock];
		$productRepositoryMock = $this->getMock(
			ProductRepository::class,
			['markAllProductsForPriceRecalculation', 'getProductsForPriceRecalculationIterator'],
			[],
			'',
			false
		);
		$productRepositoryMock->expects($this->once())->method('markAllProductsForPriceRecalculation');
		$productRepositoryMock
			->expects($this->once())
			->method('getProductsForPriceRecalculationIterator')
			->willReturn($productsIterator);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

		$this->assertCount(0, $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation());
		$this->assertSame($productsIterator, $productPriceRecalculationScheduler->getProductsIteratorForRecalculation());
	}

}

<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Test class for \Magento\Weee\Model\Observer
 */
namespace Magento\Tax\Test\Unit\Model;

use \Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the methods that rely on the ScopeConfigInterface object to provide their return values
     * @param array $expected
     * @param bool $displayBothPrices
     * @param bool $priceIncludesTax
     * @param bool $displayPriceExcludingTax
     * @dataProvider dataProviderUpdateProductOptions
     */
    public function testUpdateProductOptions(
        $expected,
        $displayBothPrices,
        $priceIncludesTax,
        $displayPriceExcludingTax
    ) {

        $frameworkObject= new \Magento\Framework\Object();
        $frameworkObject->setAdditionalOptions([]);

        $product=$this->getMock('Magento\Catalog\Model\Product', [], [], '', false);

        $registry=$this->getMock('Magento\Framework\Registry', [], [], '', false);
        $registry->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->will($this->returnValue($product));

        $taxData=$this->getMock('Magento\Tax\Helper\Data', [], [], '', false);
        $taxData->expects($this->any())
            ->method('getCalculationAlgorithm')
            ->will($this->returnValue('TOTAL_BASE_CALCULATION'));

        $taxData->expects($this->any())
            ->method('displayBothPrices')
            ->will($this->returnValue($displayBothPrices));

        $taxData->expects($this->any())
            ->method('priceIncludesTax')
            ->will($this->returnValue($priceIncludesTax));

        $taxData->expects($this->any())
            ->method('displayPriceExcludingTax')
            ->will($this->returnValue($displayPriceExcludingTax));

        $eventObject=$this->getMock('Magento\Framework\Event', ['getResponseObject'], [], '', false);
        $eventObject->expects($this->any())
            ->method('getResponseObject')
            ->will($this->returnValue($frameworkObject));

        $observerObject=$this->getMock('Magento\Framework\Event\Observer', [], [], '', false);

        $observerObject->expects($this->any())
            ->method('getEvent')
            ->will($this->returnValue($eventObject));

         $objectManager = new ObjectManager($this);
         $taxObserverObject = $objectManager->getObject(
             'Magento\Tax\Model\Observer',
             [
                 'taxData' => $taxData,
                 'registry' => $registry,
             ]
         );

         $taxObserverObject->updateProductOptions($observerObject);

         $this->assertEquals($expected, $frameworkObject->getAdditionalOptions());
    }

    /**
     * @return array
     */
    public function dataProviderUpdateProductOptions()
    {
        return [
            [
                'expected' => [
                    'calculationAlgorithm' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.finalPrice.value) '.
                        '{ %> +<%= data.finalPrice.formatted %> (Excl. tax: <%= data.basePrice.formatted %>)<% } %>',
                ],
                'displayBothPrices' => true,
                'priceIncludesTax' => false,
                'displayPriceExcludingTax' => false,
            ],
            [
                'expected' => [
                    'calculationAlgorithm' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '.
                        '{ %> +<%= data.basePrice.formatted %><% } %>',
                ],
                'displayBothPrices' => false,
                'priceIncludesTax' => true,
                'displayPriceExcludingTax' => true,
            ],
            [
                'expected' => [
                    'calculationAlgorithm' => 'TOTAL_BASE_CALCULATION',
                ],
                'displayBothPrices' => false,
                'priceIncludesTax' => false,
                'displayPriceExcludingTax' => false,
            ],
        ];
    }
}

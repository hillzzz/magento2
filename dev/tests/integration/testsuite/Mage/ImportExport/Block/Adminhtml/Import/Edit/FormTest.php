<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for block Mage_ImportExport_Block_Adminhtml_Import_Edit_FormTest
 */
class Mage_ImportExport_Block_Adminhtml_Import_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test content of form after _prepareForm
     */
    public function testPrepareForm()
    {
        $formBlock = new Mage_ImportExport_Block_Adminhtml_Import_Edit_Form();
        $prepareForm = new ReflectionMethod(
            'Mage_ImportExport_Block_Adminhtml_Import_Edit_Form',
            '_prepareForm'
        );
        $prepareForm->setAccessible(true);
        $prepareForm->invoke($formBlock);

        // check form
        $form = $formBlock->getForm();
        $this->assertInstanceOf('Varien_Data_Form', $form, 'Incorrect import form class.');
        $this->assertTrue($form->getUseContainer(), 'Form should use container.');

        // check form fieldsets
        $formFieldsetIds = array(
            'base_fieldset',
            'behavior_v1_fieldset',
            'behavior_v2_customer_fieldset',
            'import_format_version_fieldset',
            'customer_entity_fieldset',
            'upload_file_fieldset'
        );
        $formFieldsets = array();
        $formElements = $form->getElements();
        foreach ($formElements as $element) {
            /** @var $element Varien_Data_Form_Element_Abstract */
            if (in_array($element->getId(), $formFieldsetIds)) {
                $formFieldsets[] = $element;
            }
        }
        $this->assertCount(count($formFieldsetIds), $formFieldsets);
        foreach ($formFieldsets as $fieldset) {
            $this->assertInstanceOf('Varien_Data_Form_Element_Fieldset', $fieldset, 'Incorrect fieldset class.');
        }
    }
}
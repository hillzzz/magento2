<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Email\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Adminhtml email template model
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BackendTemplate extends Template
{
    /**
     * @var \Magento\Config\Model\Config\Structure
     */
    private $_structure;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\View\DesignInterface $design
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\App\Emulation $appEmulation
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\FileSystem $viewFileSystem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Email\Model\Template\FilterFactory $emailFilterFactory
     * @param \Magento\Email\Model\Template\Config $emailConfig
     * @param \Magento\Config\Model\Config\Structure $structure
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\FileSystem $viewFileSystem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Email\Model\Template\FilterFactory $emailFilterFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Config\Model\Config\Structure $structure,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $design,
            $appEmulation,
            $storeManager,
            $filesystem,
            $assetRepo,
            $viewFileSystem,
            $scopeConfig,
            $emailFilterFactory,
            $emailConfig,
            $data
        );
        $this->_structure = $structure;
    }

    /**
     * Collect all system config paths where current template is used as default
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedAsDefault()
    {
        $templateCode = $this->getOrigTemplateCode();
        if (!$templateCode) {
            return [];
        }

        $configData = $this->_scopeConfig->getValue(null, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $paths = $this->_findEmailTemplateUsages($templateCode, $configData, '');
        return $paths;
    }

    /**
     * Find nodes which are using $templateCode value
     *
     * @param string $code
     * @param array $data
     * @param string $path
     * @return array
     */
    protected function _findEmailTemplateUsages($code, array $data, $path)
    {
        $output = [];
        foreach ($data as $key => $value) {
            $configPath = $path ? $path . '/' . $key : $key;
            if (is_array($value)) {
                $output = array_merge($output, $this->_findEmailTemplateUsages($code, $value, $configPath));
            } else {
                if ($value == $code) {
                    $output[] = ['path' => $configPath];
                }
            }
        }
        return $output;
    }

    /**
     * Collect all system config paths where current template is currently used
     *
     * @return array
     */
    public function getSystemConfigPathsWhereUsedCurrently()
    {
        $templateId = $this->getId();
        if (!$templateId) {
            return [];
        }

        $templatePaths = $this->_structure->getFieldPathsByAttribute(
            'source_model',
            'Magento\Config\Model\Config\Source\Email\Template'
        );

        if (!count($templatePaths)) {
            return [];
        }

        $configData = $this->_getResource()->getSystemConfigByPathsAndTemplateId($templatePaths, $templateId);
        if (!$configData) {
            return [];
        }

        return $configData;
    }
}

<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Model\Indexer\Product\Price;

use Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Search\Request\IndexScopeResolverInterface;
use Magento\Customer\Model\Indexer\MultiDimensional\CustomerGroupDataProvider;
use Magento\Store\Model\Indexer\MultiDimensional\WebsiteDataProvider;

class PriceTableResolver implements IndexScopeResolverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var IndexScopeResolver
     */
    private $indexScopeResolver;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexScopeResolver $indexScopeResolver
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        IndexScopeResolver $indexScopeResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->indexScopeResolver = $indexScopeResolver;
    }

    /**
     * Return price table name based on dimension
     * @param string $index
     * @param array $dimensions
     * @return string
     */
    public function resolve($index, array $dimensions)
    {
        $dimensions = $this->getMixDimensions($dimensions);
        return $this->indexScopeResolver->resolve($index, $dimensions);
    }

    private function getMixDimensions($dimensions): array
    {
        $existDimensions = [];
        foreach ($dimensions as $key => $dimension) {
            $existDimensions[$dimension->getName()] = $dimension;
        }

        switch ($this->scopeConfig->getValue(ModeSwitcher::XML_PATH_PRICE_DIMENSIONS_MODE)) {
            case ModeSwitcher::INPUT_KEY_WEBSITE:
                $return = [
                    $existDimensions[WebsiteDataProvider::DIMENSION_NAME]
                ];
                break;
            case ModeSwitcher::INPUT_KEY_CUSTOMER_GROUP:
                $return = [
                    $existDimensions[CustomerGroupDataProvider::DIMENSION_NAME]
                ];
                break;
            case ModeSwitcher::INPUT_KEY_WEBSITE_AND_CUSTOMER_GROUP:
                $return = [
                    $existDimensions[WebsiteDataProvider::DIMENSION_NAME],
                    $existDimensions[CustomerGroupDataProvider::DIMENSION_NAME]
                ];
                break;
            default:
                $return = [];
        }
        return $return;
    }
}

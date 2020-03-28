<?php

namespace Switchm\SmartApi\Queries\Services;

use Switchm\SmartApi\Queries\Dao\Rdb\ProductDao;

class ProductService
{
    /**
     * @var ProductDao
     */
    private $productDao;

    /**
     * ProductService constructor.
     * @param ProductDao $productDao
     */
    public function __construct(ProductDao $productDao)
    {
        $this->productDao = $productDao;
    }

    /**
     * @param array $productIds
     * @param array $companyIds
     * @return array
     */
    public function getCompanyIds(array $productIds, array $companyIds): array
    {
        if (!empty($companyIds)) {
            return $companyIds;
        }

        if (empty($productIds)) {
            return [];
        }

        $records = $this->productDao->findCompanyIds($productIds);

        return array_column($records, 'company_id');
    }
}

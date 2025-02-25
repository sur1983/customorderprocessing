<?php
/**
 * Copyright © Surender Kumar Suthar, Inc. All rights reserved.
 */

namespace Vendor\CustomOrderProcessing\Ui\Component;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\CollectionFactory;

/**
 * Class DataProvider
 * Provides data for the Order Status Log UI grid.
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog\Collection
     */
    protected $collection;

    /**
     * DataProvider constructor.
     *
     * @param CollectionFactory $collectionFactory Collection factory for order status logs
     * @param string $name Component name
     * @param string $primaryFieldName Primary field name for the grid
     * @param string $requestFieldName Request field name
     * @param array<string, mixed> $meta Metadata array for UI component
     * @param array<string, mixed> $data Data array for UI component
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data for the UI grid.
     *
     * @return array<string, mixed> Processed grid data
     */
    public function getData(): array
    {
        return $this->collection->toArray();
    }
}

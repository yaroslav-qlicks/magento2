<?php
/*
 * Copyright Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mollie\Payment\Service\Order\Lines\Generator;

use Magento\Sales\Api\Data\OrderInterface;
use Mollie\Payment\Helper\General;

class MageWorxRewardPoints implements GeneratorInterface
{
    /**
     * @var General
     */
    private $mollieHelper;

    public function __construct(
        General $mollieHelper
    ) {
        $this->mollieHelper = $mollieHelper;
    }

    public function process(OrderInterface $order, array $orderLines): array
    {
        $amount = $order->getMwRwrdpointsAmnt();
        if (!$amount) {
            return $orderLines;
        }

        $forceBaseCurrency = (bool)$this->mollieHelper->useBaseCurrency($order->getStoreId());
        $currency = $forceBaseCurrency ? $order->getBaseCurrencyCode() : $order->getOrderCurrencyCode();

        if (abs($amount) < 0.01) {
            return $orderLines;
        }

        $orderLines[] = [
            'type' => 'surcharge',
            'name' => 'Reward Points',
            'quantity' => 1,
            'unitPrice' => $this->mollieHelper->getAmountArray($currency, -$amount),
            'totalAmount' => $this->mollieHelper->getAmountArray($currency, -$amount),
            'vatRate' => 0,
            'vatAmount' => $this->mollieHelper->getAmountArray($currency, 0.0),
        ];

        return $orderLines;
    }
}

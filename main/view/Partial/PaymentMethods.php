<?php

namespace View\Partial;

class PaymentMethods {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods
     * @param string|int $selectedPaymentMethodId
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        array $paymentMethods,
        $selectedPaymentMethodId
    ) {
        $paymentMethods = array_merge(
            array_filter($paymentMethods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
                return $paymentMethod->discount;
            }),
            array_filter($paymentMethods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
                return !$paymentMethod->discount;
            })
        );

        $paymentMethodsByDiscount = call_user_func(function() use($paymentMethods) {
            $paymentMethodsByDiscount = [];
            /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $previousPaymentMethod */
            $previousPaymentMethod = null;
            /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $previousCashPaymentMethod */
            $previousCashPaymentMethod = null;
            $index = 0;
            $cashPaymentIndex = null;

            foreach ($paymentMethods as $paymentMethod) {
                if (!$paymentMethod->discount || !$previousPaymentMethod || !$previousPaymentMethod->discount || $paymentMethod->discount->value !== $previousPaymentMethod->discount->value || $paymentMethod->discount->unit !== $previousPaymentMethod->discount->unit) {
                    $index++;
                }

                if (
                    in_array($paymentMethod->id, ['1', '2']) &&
                    (
                        !$previousCashPaymentMethod ||
                        (!$paymentMethod->discount && !$previousCashPaymentMethod->discount) ||
                        ($paymentMethod->discount && $previousCashPaymentMethod->discount && $paymentMethod->discount->value === $previousCashPaymentMethod->discount->value && $paymentMethod->discount->unit === $previousCashPaymentMethod->discount->unit)
                    )
                ) {
                    if ($cashPaymentIndex === null) {
                        $cashPaymentIndex = $index;
                    }

                    $paymentMethodsByDiscount[$cashPaymentIndex]['При получении'][$paymentMethod->id] = $previousCashPaymentMethod = clone $paymentMethod;
                } else {
                    $paymentMethodsByDiscount[$index][][$paymentMethod->id] = clone $paymentMethod;
                }

                $previousPaymentMethod = $paymentMethod;
            }

            if (isset($paymentMethodsByDiscount[$cashPaymentIndex]['При получении']) && count($paymentMethodsByDiscount[$cashPaymentIndex]['При получении']) > 1) {
                foreach ($paymentMethodsByDiscount[$cashPaymentIndex]['При получении'] as $paymentMethod) {
                    if ('1' == $paymentMethod->id) {
                        $paymentMethod->name = 'наличными';
                    } else if ('2' == $paymentMethod->id) {
                        $paymentMethod->name = 'банковской картой';
                    }
                }
            }

            return $paymentMethodsByDiscount;
        });

        return [
            'paymentMethodGroups' => array_values(array_map(function($paymentMethodsByType) use($selectedPaymentMethodId, $helper) {
                /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $firstPaymentMethod */
                $tmp = reset($paymentMethodsByType);
                $firstPaymentMethod = reset($tmp);

                return [
                    'discount' => $firstPaymentMethod->discount ? [
                        'action' => $firstPaymentMethod->discount->action,
                        'sum' => $firstPaymentMethod->discount->sum,
                        'value' => $helper->formatPrice($firstPaymentMethod->discount->value),
                        'unit' => $firstPaymentMethod->discount->unit ? [
                            'isRub' => $firstPaymentMethod->discount->unit === 'rub',
                            'value' => $firstPaymentMethod->discount->unit,
                        ] : null,
                    ] : null,
                    'paymentMethodGroups' => array_values(array_map(function($paymentMethodsById, $key) use($selectedPaymentMethodId) {
                        return [
                            'name' => is_numeric($key) ? '' : $key,
                            'selected' => in_array($selectedPaymentMethodId, array_keys($paymentMethodsById)),
                            'paymentMethods' => array_values(array_map(function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) use($selectedPaymentMethodId) {
                                return [
                                    'id' => $paymentMethod->id,
                                    'name' => $paymentMethod->name,
                                    'isOnline' => $paymentMethod->isOnline,
                                    'icon' => $paymentMethod->icon,
                                    'selected' => $paymentMethod->id == $selectedPaymentMethodId,
                                ];
                            }, $paymentMethodsById)),
                        ];
                    }, $paymentMethodsByType, array_keys($paymentMethodsByType))),
                ];
            }, $paymentMethodsByDiscount)),
        ];
    }
}
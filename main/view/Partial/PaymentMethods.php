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

        $paymentMethodGroupsByDiscount = [];
        /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $selectedPaymentMethod */
        $selectedPaymentMethod = null;
        call_user_func(function() use(&$paymentMethodGroupsByDiscount, &$selectedPaymentMethod, $paymentMethods, $selectedPaymentMethodId) {
            /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $previousPaymentMethod */
            $previousPaymentMethod = null;
            $index = 0;

            foreach ($paymentMethods as $paymentMethod) {
                if (!$paymentMethod->discount || !$previousPaymentMethod || !$previousPaymentMethod->discount || $paymentMethod->discount->value !== $previousPaymentMethod->discount->value || $paymentMethod->discount->unit !== $previousPaymentMethod->discount->unit) {
                    $index++;
                }

                $paymentMethodGroupsByDiscount[$index][$paymentMethod->id] = $paymentMethod;
                $previousPaymentMethod = $paymentMethod;

                if ($paymentMethod->id == $selectedPaymentMethodId) {
                    $selectedPaymentMethod = $paymentMethod;
                }
            }

            return $paymentMethodGroupsByDiscount;
        });

        return [
            'paymentMethodGroups' => array_values(array_map(function($paymentMethodsById) use($selectedPaymentMethodId, $helper) {
                /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $firstPaymentMethod */
                $firstPaymentMethod = reset($paymentMethodsById);

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
            }, $paymentMethodGroupsByDiscount)),
            'selectedPaymentMethod' => $selectedPaymentMethod ? [
                'name' => $selectedPaymentMethod->name,
            ] : null,
            'paymentMethods' => array_values(array_map(function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) use($selectedPaymentMethodId, $helper) {
                return [
                    'id' => $paymentMethod->id,
                    'name' => $paymentMethod->name,
                    'isOnline' => $paymentMethod->isOnline,
                    'icon' => $paymentMethod->icon,
                    'selected' => $paymentMethod->id == $selectedPaymentMethodId,
                    'discount' => $paymentMethod->discount ? [
                        'action' => $paymentMethod->discount->action,
                        'sum' => $paymentMethod->discount->sum,
                        'value' => $helper->formatPrice($paymentMethod->discount->value),
                        'unit' => $paymentMethod->discount->unit ? [
                            'isRub' => $paymentMethod->discount->unit === 'rub',
                            'value' => $paymentMethod->discount->unit,
                        ] : null,
                    ] : null,
                ];
            }, $paymentMethods)),
        ];
    }
}
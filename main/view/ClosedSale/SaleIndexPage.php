<?php

namespace View\ClosedSale;


use Model\ClosedSale\ClosedSaleEntity;
use \View\DefaultLayout;

class SaleIndexPage extends DefaultLayout
{

    /** @var ClosedSaleEntity[] */
    protected $sales;

    public function prepare()
    {
        parent::prepare();
        $this->sales = $this->getParam('sales', []);
        $this->title = 'Secret Sale';
    }

    public function slotContent() {
        return $this->render('closed-sale/sale-index', $this->params);
    }

    /**
     * @param bool $new Новые акции
     *
     * @return \Model\ClosedSale\ClosedSaleEntity[]
     */
    public function getSales($new = true)
    {
        if ($new) {
            $endDate = (new \DateTime())->setTimestamp(strtotime(sprintf('%s day midnight', ClosedSaleEntity::ACTION_DAYS)));
        } else {
            $endDate = (new \DateTime)->setTimestamp(strtotime(sprintf('%s day midnight', ClosedSaleEntity::ACTION_DAYS - 1)));
        }

        $actions = array_filter(
            $this->sales,
            function(ClosedSaleEntity $saleEntity) use ($endDate) {
                return $saleEntity->endsAt->format('d.m.Y') === $endDate->format('d.m.Y');
            }
        );

        usort($actions, function (ClosedSaleEntity $first, ClosedSaleEntity $second) {
            return $first->priority > $second->priority;
        });

        return $actions;
    }
}

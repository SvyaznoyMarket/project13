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
        $this->title = 'Секретная распродажа';
        $this->addMeta('robots', 'none');
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
        $endDate = (new \DateTime)->setTimestamp(strtotime('1 day midnight'));

        $actions = array_filter(
            $this->sales,
            function(ClosedSaleEntity $saleEntity) use ($endDate, $new) {
                return $new
                    ? $saleEntity->endsAt > $endDate
                    : $saleEntity->endsAt == $endDate;
            }
        );

        usort($actions, function (ClosedSaleEntity $first, ClosedSaleEntity $second) {
            return $first->priority > $second->priority;
        });

        return $actions;
    }
}

<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;

/**
 * Class AppOrder
 * @package Source\Models\CafeApp
 */
class AppOrder extends Model
{
  /**
   * AppOrder constructor.
   */
  public function __construct()
  {
    parent::__construct("app_orders", ["id"],
      ["user_id", "card_id", "subscription_id", "transaction", "amount", "status"]);
  }

  /**
   * @return mixed|Model|null
   */
  public function creditCard()
  {
    return (new AppCreditCard())->findById($this->card_id);
  }
}
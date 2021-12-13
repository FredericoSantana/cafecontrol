<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;

/**
 * Class AppSubscription
 * @package Source\Models\CafeApp
 */
class AppSubscription extends Model
{
  /**
   * AppSubscription constructor.
   */
  public function __construct()
  {
    parent::__construct(
      "app_subscriptions",
      ["id"],
      ["user_id", "plan_id", "status", "pay_status", "started", "due_day", "next_due"]
    );
  }

  /**
   * @return mixed|Model|null
   */
  public function plan()
  {
    return (new AppPlan())->findById($this->plan_id);
  }

  /**
   * @return mixed|Model|null
   */
  public function creditCard()
  {
    return (new AppCreditCard())->findById($this->card_id);
  }
}
<?php

namespace Source\App\Admin;

use Source\Models\CafeApp\AppSubscription;

/**
 * Class Control
 * @package Source\App\Admin
 */
class Control extends Admin
{
  /**
   * Control constructor.
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   *
   */
  public function home(): void
  {

    $head = $this->seo->render(
      CONF_SITE_NAME . " | Control",
      CONF_SITE_DESC,
      url("/admin"),
      theme("/assets/images/image.jpg", CONF_VIEW_ADMIN),
      false
    );

    echo $this->view->render("widgets/control/home", [
      "app" => "control/home",
      "head" => $head,
      "stats" => (object)[
        "subscriptions" => (new AppSubscription())->find("pay_status = :s", "s=active")->count(),
        "subscriptionsMonth" => (new AppSubscription())->find(
          "pay_status = :s AND year(started) = year(now()) AND month(started) = month(now())",
          "s=active"
        )->count(),
        "recurrence" => (new AppSubscription())->recurrence(),
        "recurrenceMonth" => (new AppSubscription())->recurrenceMonth()
      ],
      "subscriptions" => (new AppSubscription())->find()->order("started DESC")->limit(10)->fetch(true)
    ]);
  }

  /**
   * @param array|null $data
   */
  public function subscriptions(?array $data): void
  {

  }

  /**
   * @param array|null $data
   */
  public function subscription(?array $data): void
  {

  }

  /**
   * @param array|null $data
   */
  public function plans(?array $data): void
  {

  }

  /**
   * @param array|null $data
   */
  public function plan(?array $data): void
  {

  }
}
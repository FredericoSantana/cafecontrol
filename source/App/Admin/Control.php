<?php

namespace Source\App\Admin;

use Source\Models\CafeApp\AppSubscription;
use Source\Support\Pager;

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
    //Search redirect
    if (!empty($data["s"])) {
      $s = filter_var($data["s"], FILTER_SANITIZE_STRIPPED);
      echo json_encode(["redirect" => url("/admin/control/subscriptions/{$s}/1")]);
      return;
    }

    $search = null;
    $subscriptions = (new AppSubscription())->find();

    if (!empty($data["search"]) && $data["search"] != "all") {
      $search = filter_var($data["search"], FILTER_SANITIZE_STRIPPED);
      $subscriptions = (new AppSubscription())->find(
        "user_id IN(
          SELECT id FROM users WHERE MATCH(first_name, last_name, email) AGAINST(:s)
        )",
        "s={$search}"
      );
    }

    $all = ($search ?? "all");
    $pager = new Pager(url("/admin/control/subscriptions/{$all}/"));
    $pager->pager($subscriptions->count(), 12, (!empty($data["page"]) ? $data["page"] : 1));

    $head = $this->seo->render(
      CONF_SITE_NAME . " | Assinantes",
      CONF_SITE_DESC,
      url("/admin"),
      theme("/assets/images/image.jpg", CONF_VIEW_ADMIN),
      false
    );

    echo $this->view->render("widgets/control/subscriptions", [
      "app" => "control/subscriptions",
      "head" => $head,
      "subscriptions" => $subscriptions->limit($pager->limit())->offset($pager->offset())->fetch(true),
      "paginator" => $pager->render(),
      "search" => $search
    ]);
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
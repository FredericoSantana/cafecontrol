<?php

namespace Source\App\Admin;

use Source\Models\Faq\Channel;
use Source\Support\Pager;

/**
 * Class Faq
 * @package Source\App\Admin
 */
class Faq extends Admin
{
  /**
   * Faq constructor.
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * @param array|null $data
   */
  public function home(?array $data): void
  {
    $channels = (new Channel())->find();

    $pager = new Pager(url("/admin/faq/home/"));
    $pager->pager($channels->count(), 5, (!empty($data["page"]) ? $data["page"] : 1));

    $head = $this->seo->render(
      CONF_SITE_NAME . " | FAQ",
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/faqs/home", [
      "app" => "faq/home",
      "head" => $head,
      "channels" => $channels->order("channel")->limit($pager->limit())->offset($pager->offset())->fetch(true),
      "paginator" => $pager->render()
    ]);
  }

  /**
   * @param array|null $data
   */
  public function channel(?array $data): void
  {

    $head = $this->seo->render(
      CONF_SITE_NAME . " | FAQ: Novo Canal",
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/faqs/channel", [
      "app" => "faq/home",
      "head" => $head,
      "channel" => ""
    ]);
    
  }

  /**
   * @param array|null $data
   */
  public function question(?array $data): void
  {
    $head = $this->seo->render(
      CONF_SITE_NAME . " | FAQ: Perguntas",
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/faqs/question", [
      "app" => "faq/home",
      "head" => $head,
      "channel" => (object)[
        "id" => 10
      ],
      "question" => ""
    ]);
  }
}
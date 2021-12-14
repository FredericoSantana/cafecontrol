<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;
use Source\Models\User;

/**
 * Class Online
 * @package Source\Model\Report
 */
class Online extends Model
{
  /** @var int */
  private int $sessionTime;

  /**
   * @param int $sessionTime
   */
  public function __construct(int $sessionTime = 20)
  {
    $this->sessionTime = $sessionTime;
    parent::__construct("report_online", ["id"], ["ip", "url", "agent"]);
  }

  /**
   * @param bool $count
   * @return array|int|null
   */
  public function findByActive(bool $count = false)
  {
    $find = $this->find("updated_at >= NOW() - INTERVAL {$this->sessionTime} MINUTE");
    if ($count) {
      return $find->count();
    }

    $find->order("updated_at DESC");
    return $find->fetch(true);
  }

  /**
   * @return Online
   */
  public function report(bool $clear = true): Online
  {
    $session = new Session();

    if ($clear) {
      $this->clear();
    }

    if (!$session->has("online")) {
      $this->user = ($session->authUser ?? null);
      $this->url = (filter_input(INPUT_GET, "route", FILTER_SANITIZE_STRIPPED) ?? "/");
      // Quando em produção, se não funcionar tentar usar o que está comentado.
//      $this->ip = $_SERVER['REMOTE_ADDR'];
      $this->ip = filter_input(INPUT_SERVER, "REMOTE_ADDR");
//      $this->agent = $_SERVER["HTTP_USER_AGENT"];
      $this->agent = filter_input(INPUT_SERVER, "HTTP_USER_AGENT");

      $this->save();
      $session->set("online", $this->id);
      return $this;
    }

    $find = $this->findById($session->online);

    if (!$find) {
      $session->unset("online");
      return $this;
    }

    $find->user = ($session->authUser ?? null);
    $find->url = (filter_input(INPUT_GET, "route", FILTER_SANITIZE_STRIPPED) ?? "/");
    $find->pages += 1;
    $find->save();

    return $this;
  }

  /**
   * CLEAR ONLINE
   */
  public function clear(): void
  {
    $this->delete("updated_at <= NOW() - INTERVAL {$this->sessionTime} MINUTE", null);
  }

  /**
   * @return mixed|Model|null
   */
  public function user()
  {
    return (new User())->findById($this->user);
  }
}
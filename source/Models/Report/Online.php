<?php

namespace Source\Models\Report;

use Source\Core\Model;
use Source\Core\Session;

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

    return $find->fetch(true);
  }

  /**
   * @return Online
   */
  public function report(bool $clear = true): Online
  {
    $session = new Session();

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

    if ($clear) {
      $this->clear();
    }

    return $this;
  }

  public function clear(): void
  {
    $this->delete("updated_at <= NOW() - INTERVAL {$this->sessionTime} MINUTE", null);
  }

  /**
   * @return bool
   */
  public function save(): bool
  {
    /** Update Access */
    if (!empty($this->id)) {
      $onlineId = $this->id;
      $this->update($this->safe(), "id = :id", "id={$onlineId}");
      if ($this->fail()) {
        $this->message->error("Erro ao atualizar, verifique os dados");
        return false;
      }
    }

    /** Create Access */
    if (empty($this->id)) {
      $onlineId = $this->create($this->safe());
      if ($this->fail()) {
        $this->message->error("Erro ao cadastrar, verifique os dados");
        return false;
      }
    }

    $this->data = $this->findById($onlineId)->data();
    return true;
  }
}
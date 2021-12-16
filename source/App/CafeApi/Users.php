<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppInvoice;

/**
 * Class Users
 * @package Source\App\CafeApi
 */
class Users extends CafeApi
{
  /**
   * Users constructor.
   * @throws \Exception
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * list user data
   */
  public function index(): void
  {
    $user = $this->user->data();
    $user->photo = CONF_URL_BASE . "/" . "/{$user->photo}";
    unset($user->password, $user->forget);

    $reponse["user"] = $user;
    $reponse["user"]->balance = (new AppInvoice())->balance($this->user);

    $this->back($reponse);
    return;
  }

  /**
   * @param array $data
   */
  public function update(array $data): void
  {

  }

  /**
   *
   */
  public function photo()
  {

  }
}
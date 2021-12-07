<?php


namespace Source\Models;

use Source\Core\Model;
use Source\Core\View;
use Source\Support\Email;

/**
 * Class Auth
 * @package Source\Models
 */
class Auth extends Model
{
  /**
   * Auth constructor.
   */
  public function __construct()
  {
    parent::__construct("user", ["id"], ["email", "password"]);
  }

  /**
   * @param User $user
   * @return bool
   */
  public function register(User $user): bool
  {
    if (!$user->save()) {
      $this->message = $user->message;
      return false;
    }

    $view = new View(__DIR__ . "/../../shared/views/email");
    $message = $view->render("confirm", [
      "first_name" => $user->first_name,
      "confirm_link" => url("/obrigado/" . base64_encode($user->email))
    ]);

    (new Email())->bootstrap(
      "Ative a sua conta no " . CONF_SITE_NAME,
      $message,
      $user->email,
      "{$user->first_name} {$user->last_name}"
    )->send();

    return true;
  }

}
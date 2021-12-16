<?php

namespace Source\App\CafeApi;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Models\CafeApp\AppInvoice;
use Source\Models\CafeApp\AppWallet;

/**
 * Class CafeApi
 * @package Source\App\CafeApi
 */
class CafeApi extends Controller
{
  /** @var */
  protected $user;

  /** @var */
  protected $headers;

  /** @var */
  protected $response;

  /**
   * CafeApi constructor.
   */
  public function __construct()
  {
    parent::__construct("/");

    header('Content-Type: application/json; charset=UTF-8');
    $this->headers = getallheaders();

    $auth = $this->auth();
    if (!$auth) {
      exit;
    }

    (new AppWallet())->start($this->user);
    (new AppInvoice())->findById($this->user, 3);
  }

  protected function call(int $code, string $type = null, string $message = null, string $run = "errors"): CafeApi
  {
    http_response_code($code);

    if (!empty($type)) {
      $this->response = [
        $rule => [
          "type" => $type,
          "message" => (!empty($message) ? $message : null)
        ]
      ];
    }
    return $this;
  }

  protected function back(array $response = null): CafeApi
  {
    if (!empty($response)) {
      $this->response = (!empty($this->response) ? array_merge($this->response, $response): $response);
    }

    echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return $this;
  }

  protected function auth(): bool
  {
    if (empty($this->headers["email"]) || empty($this->headers["password"])) {
      $this->call(
        400,
        "auth_empty",
        "Favor informe seu e-mail e senha"
      )->back();
      return false;
    }

    $auth = new Auth();
    $user = $auth->attempt($this->headers["email"], $this->headers["password"], 1);

    if (!$user) {
      $this->call(
        401,
        "invalid_auth",
        $auth->message()->getText()
      )->back();
      return false;
    }

    $this->user = $user;
    return true;
  }
}
<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppCreditCard
 * @package Source\Models\CafeApp
 */
class AppCreditCard extends Model
{
  /** @var string */
  private string $apiurl;

  /** @var string */
  private string $apikey;

  /** @var string */
  private string $endpoint;

  /** @var array */
  private array $build;

  /** @var string */
  private string $callback;

  /**
   * AppCreditCard constructor.
   */
  public function __construct()
  {
    parent::__construct("app_credit_cards", ["id"], ["user_id", "brand", "last_digits", "cvv", "hash"]);

    $this->apiurl = "https://api.pagar.me";

    if (CONF_PAGARME_MODE == "live") {
      $this->apikey = CONF_PAGARME_LIVE;
    }else{
      $this->apikey = CONF_PAGARME_TEST;
    }
  }

  /**
   * @param User $user
   * @param string $number
   * @param string $name
   * @param string $expDate
   * @param string $cvv
   * @return AppCreditCard|null
   */
  public function creditCard(User $user, string $number, string $name, string $expDate, string $cvv): ?AppCreditCard
  {
    $this->build = [
      "card_number" => $this->clear($number),
      "card_holder_name" =>filter_var($name, FILTER_SANITIZE_STRIPPED),
      "card_expiration_date" => $this->clear($expDate),
      "card_cvv" => $this->clear($cvv)
    ];
    
    $this->endpoint = "/1/cards";
    $this->post();

    var_dump($this->callback);
  }

  /**
   * @param string $number
   * @return string
   */
  private function clear(string $number): string
  {
    return preg_replace("/[^0-9]/", "", $number);
  }

  /**
   *
   */
  private function post()
  {
    $url = $this->apiurl . $this->endpoint;
    $api = ["api_key" => $this->apikey];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array_merge($this->build, $api)));
    curl_setopt($ch, CURLOPT_HTTPHEADER, []);
    $this->callback = json_decode(curl_exec($ch));
    curl_close($ch);
  }
}
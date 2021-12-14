<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Models\CafeApp\AppCreditCard;
use Source\Models\CafeApp\AppOrder;
use Source\Models\CafeApp\AppPlan;
use Source\Models\CafeApp\AppSubscription;

class Pay extends Controller
{
  public function __construct()
  {
    parent::__construct(__DIR__ . "/../../shared/pagarme/");
  }

  public function create(array $data): void
  {
    $user = Auth::user();
    $plan = (new AppPlan())->findById($data["plan"]);

    if (request_limit("paycreate", 3, 300)) {
      $json["message"] = $this->message->warning(
        "Desculpe {$user->first_name}, mas por segurança aguarde pelo menos 5 minutos para tentar outro cartão."
      )->render();
      echo json_encode($json);
      return;
    }

    $checkSubscribe = (new AppSubscription())->find(
      "user_id = :user AND status != :status","user={$user->id}&status=canceled"
    )->fetch();

    if ($checkSubscribe) {
      $json["message"] = $this->message->warning(
        "Você já tem uma assinatura ativa {$user->first_name}. Não é necessário assinar o {$plan->name} mais de uma vez."
      )->render();
      echo json_encode($json);
      return;
    }
    
    $creditCard = new AppCreditCard();
    $card = $creditCard->creditCard(
      $user, $data["card_number"], $data["card_holder_name"], $data["card_expiration_date"], $data["card_cvv"]
    );

    if (!$card) {
      $json["message"] = $creditCard->message()
        ->before("Oooops! ")
        ->after(". Favor verifique os dados para tentar assinar novamente.")
        ->render();

      echo json_encode($json);
      return;
    }

    $transaction = $card->transaction($plan->price);

    if (!$transaction) {
      $json["message"] = $creditCard->message()
        ->before("Oooops! ")
        ->after(". Você pode tentar novamente com um novo cartão.")
        ->render();

      echo json_encode($json);
      return;
    }

    $subscription = (new AppSubscription())->subscribe($user, $plan, $card);
    (new AppOrder())->byCreditCard($user, $card, $subscription, $transaction);
    
    $this->message->success(
      "Bem vindo(a) ao {$plan->name} {$user->first_name}. Sua assinatura está ativa e você já pode controlar. Confira os detalhes..."
    )->flash();

    $json["redirect"] = url("/app/assinatura");
    echo json_encode($json);
  }

  public function update(array $data): void
  {

  }
}
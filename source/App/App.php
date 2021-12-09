<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Core\View;
use Source\Models\Auth;
use Source\Models\CafeApp\AppCategory;
use Source\Models\CafeApp\AppInvoice;
use Source\Models\Post;
use Source\Models\Report\Access;
use Source\Models\Report\Online;
use Source\Models\User;
use Source\Support\Email;
use Source\Support\Message;

/**
 * Class App
 * @package Source\App
 */
class App extends Controller
{
  /** @var User */
  private $user;

  /**
   * App constructor.
   */
  public function __construct()
  {
    parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_APP . "/");

    if (!$this->user = Auth::user()) {
      $this->message->warning("Efetue login para acessar o APP.")->flash();
      redirect("/entrar");
    }

    (new Access())->report();
    (new Online())->report();
  }

  /**
   * APP HOME
   */
  public function home()
  {
    $head = $this->seo->render(
      "Olá {$this->user->first_name}. Vamos controlar? - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    //CHART

    $dateChart = [];
    for ($month = -4; $month <= 0; $month++) {
      $dateChart[] = date("m/Y", strtotime("{$month}month"));
    }

    $chartData = new \stdClass();
    $chartData->categories = "'" . implode("','", $dateChart) . "'";
    $chartData->expense = "0,0,0,0,0";
    $chartData->income = "0,0,0,0,0";

    $chart = (new AppInvoice())
      ->find("user_id = :user AND status = :status AND due_at >= DATE(now() - INTERVAL 5 MONTH) GROUP BY year(due_at) ASC, month(due_at) ASC",
            "user={$this->user->id}&status=paid",
           "
             year(due_at) AS due_year,
             month(due_at) AS due_month,
             DATE_FORMAT(due_at, '%m/%Y') AS due_date,
             (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'income' AND year(due_at) = due_year AND month(due_at) = due_month) AS income,
             (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'expense' AND year(due_at) = due_year AND month(due_at) = due_month) AS expense
             "
      )->limit(5)
      ->fetch(true);

    if ($chart) {
      $chartCategories = [];
      $chartExpense = [];
      $chartIncome = [];

      foreach ($chart as $chartItem) {
        $chartCategories[] = $chartItem->due_date;
        $chartExpense[] = $chartItem->expense;
        $chartIncome[] = $chartItem->income;
      }

      $chartData->categories = "'" . implode("','", $chartCategories) . "'";
      $chartData->expense = implode(",", array_map("abs", $chartExpense));
      $chartData->income = implode(",", array_map("abs", $chartIncome));
    }

    //END CHART

    //INCOME && EXPENSE

    $income = (new AppInvoice())
      ->find("user_id = :user AND type = 'income' AND status = 'unpaid' AND date(due_at) <= date(now() + INTERVAL 1 MONTH)",
        "user={$this->user->id}")
      ->order("due_at")
      ->fetch(true);

    $expense = (new AppInvoice())
      ->find("user_id = :user AND type = 'expense' AND status = 'unpaid' AND date(due_at) <= date(now() + INTERVAL 1 MONTH)",
        "user={$this->user->id}")
      ->order("due_at")
      ->fetch(true);

    //END INCOME && EXPENSE

    //WALLET

    $wallet = (new AppInvoice())->find(
      "user_id = :user AND status = :status",
      "user={$this->user->id}&status=paid",
      "
          (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'income') AS income,
          (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND status = :status AND type = 'expense') AS expense
      ")->fetch();

    if ($wallet) {
      $wallet->wallet = $wallet->income - $wallet->expense;
    }

    //END WALLET

    //POSTS

    $posts = (new Post())->find()->limit(3)->order("post_at DESC")->fetch(true);

    //END POSTS

    echo $this->view->render("home", [
      "head" => $head,
      "chart" => $chartData,
      "income" => $income,
      "expense" => $expense,
      "wallet" => $wallet,
      "posts" => $posts
    ]);
  }

  /**
   * @param array $data
   */
  public function filter(array $data)
  {
    var_dump($data);
  }

  /**
   * @param array|null $data
   */
  public function income(?array $data):void
  {
    $head = $this->seo->render(
      "Minhas receitas - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    $categories = (new AppCategory())
      ->find("type = :t", "t=income", "id, name")
      ->order("order_by, name")
      ->fetch(true);

    echo $this->view->render("invoices", [
      "user" => $this->user,
      "head" => $head,
      "type" => "income",
      "categories" => $categories,
      "invoices" => (new AppInvoice())->filter($this->user, "income", ($data ?? null)),
      "filter" => (object)[
        "status" => ($data["status"] ?? null),
        "category" => ($data["category"] ?? null),
        "date" => (!empty($data["date"]) ? str_replace("-", "/", $data["date"]) : null)
      ]
    ]);
  }


  /**
   * @param array|null $data
   */
  public function expense(?array $data): void
  {
    $head = $this->seo->render(
      "Minhas despesas - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    $categories = (new AppCategory())
      ->find("type = :t", "t=expense", "id, name")
      ->order("order_by, name")
      ->fetch(true);

    echo $this->view->render("invoices", [
      "user" => $this->user,
      "head" => $head,
      "type" => "expense",
      "categories" => $categories,
      "invoices" => (new AppInvoice())->filter($this->user, "expense", ($data ?? null)),
      "filter" => (object)[
        "status" => ($data["status"] ?? null),
        "category" => ($data["category"] ?? null),
        "date" => (!empty($data["date"]) ? str_replace("-", "/", $data["date"]) : null)
      ]
    ]);
  }

  /**
   * @param array $data
   */
  public function launch(array $data): void
  {
    if (request_limit("applaunch", 20, 300)) {
      $json["message"] = $this->message->warning(
        "Foi muito rápido {$this->user->first_name}! Aguarde 5 minutos para novos lançamentos."
      )->render();
      echo json_encode($json);
      return;
    }

    if (!empty($data["enrollments"]) && ($data["enrollments"] < 2 || $data["enrollments"] > 420)) {
      $json["message"] = $this->message->warning(
        "Oooops {$this->user->first_name}! Para lançar o número de parcelas deve ser entre 2 e 420."
      )->render();
      echo json_encode($json);
      return;
    }

    $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
    $status = (date($data["due_at"]) <= date("Y-m-d") ? "paid" : "unpaid");

    $invoice = (new AppInvoice());
    $invoice->user_id = $this->user->id;
    $invoice->wallet_id = $data["wallet"];
    $invoice->category_id = $data["category"];
    $invoice->invoice_of = null;
    $invoice->description = $data["description"];
    $invoice->type = ($data["repeat_when"] == "fixed" ? "fixed_{$data["type"]}" : $data["type"]);
    $invoice->value = str_replace([".", ","], ["", "."], $data["value"]);
    $invoice->currency = $data["currency"];
    $invoice->due_at = $data["due_at"];
    $invoice->repeat_when = $data["repeat_when"];
    $invoice->period = ($data["period"] ?? "month");
    $invoice->enrollments = ($data["enrollments"] ?? 1);
    $invoice->enrollment_of = 1;
    $invoice->status = ($data["repeat_when"] == "fixed" ? "paid" : $status);

    if (!$invoice->save()) {
      $json["message"] = $invoice->message()->before("Ooops! ")->render();
      echo json_encode($json);
      return;
    }

    if ($invoice->repeat_when == "enrollment") {
      $invoiceOf = $invoice->id;
      for ($enrollment = 1; $enrollment < $invoice->enrollments; $enrollment++) {
        $invoice->id = null;
        $invoice->invoice_of = $invoiceOf;
        $invoice->due_at = date("Y-m-d", strtotime($data["due_at"] . "+{$enrollment}month"));
        $invoice->status = (date($invoice->due_at) <= date("Y-m-d") ? "paid" : "unpaid");
        $invoice->enrollment_of = $enrollment + 1;
        $invoice->save();
      }
    }

    if ($invoice->type == "income") {
      $this->message->success("Receita lançada com sucesso. Use o filtro para controlar.")->render();
    }else{
      $this->message->success("Despesa lançada com sucesso. Use o filtro para controlar.")->render();
    }

    $json["reload"] = true;
    echo json_encode($json);
  }

  /**
   * @param array $data
   * @throws \Exception
   */
  public function support(array $data): void
  {
    if (empty($data["message"])) {
      $json["message"] = $this->message->warning("Para enviar escreva sua mensagem")->render();
      echo json_encode($json);
      return;
    }

    if (request_limit("appsupport", 3, 300)) {
      $json["message"] = $this->message->warning(
        "Por favor aguarde 5 minutos para enviar novos contatos, sugestões ou reclamações"
      )->render();
      echo json_encode($json);
      return;
    }

    if (request_repeat("message", $data["message"])) {
      $json["message"] = $this->message->info(
        "Já recebemos sua solicitação {$this->user->first_name}. Agradecemos pelo contato e responderemos em breve."
      )->render();
      echo json_encode($json);
      return;
    }

    $subject = date_fmt() . " - {$data["subject"]}";
    $message = filter_var($data["message"], FILTER_SANITIZE_STRING);

    $view = new View(__DIR__ . "/../../shared/views/email", "php");
    $body = $view->render("mail", [
      "subject" => $subject,
      "message" => str_textarea($message)
    ]);

    (new Email())->bootstrap(
      $subject,
      $body,
      CONF_MAIL_SUPPORT,
      "Suporte " . CONF_SITE_NAME,
    )->queue($this->user->email, "{$this->user->first_name} {$this->user->last_name}");

    $this->message->success(
      "Recebemos sua solicitação {$this->user->first_name}. Agradecemos pelo contato e responderemos em breve."
    )->flash();
    $json["reload"] = true;
    echo json_encode($json);

  }

  /**
   * APP INVOICE (Fatura)
   */
  public function invoice()
  {
    $head = $this->seo->render(
      "Aluguel - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    echo $this->view->render("invoice", [
      "head" => $head
    ]);
  }

  /**
   * APP PROFILE (Perfil)
   */
  public function profile()
  {
    $head = $this->seo->render(
      "Meu perfil - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    echo $this->view->render("profile", [
      "head" => $head
    ]);
  }

  /**
   * APP LOGOUT
   */
  public function logout()
  {
    (new Message())->info("Você saiu com sucesso " . Auth::user()->first_name . ". Volte logo :)")->flash();

    Auth::logout();
    redirect("/entrar");
  }
}
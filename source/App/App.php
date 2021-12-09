<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Auth;
use Source\Models\CafeApp\AppInvoice;
use Source\Models\Post;
use Source\Models\Report\Access;
use Source\Models\Report\Online;
use Source\Models\User;
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
      ->find("user_id = :user AND status = :status AND due_at >= DATE(now() - INTERVAL 4 MONTH) GROUP BY year(due_at) ASC, month(due_at) ASC",
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
          (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND type = 'income') AS income,
          (SELECT SUM(value) FROM app_invoices WHERE user_id = :user AND type = 'expense') AS expense
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
   * APP INCOME (Receber)
   */
  public function income()
  {
    $head = $this->seo->render(
      "Minhas receitas - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    echo $this->view->render("income", [
      "head" => $head
    ]);
  }

  /**
   * APP EXPENSE (Pagar)
   */
  public function expense()
  {
    $head = $this->seo->render(
      "Minhas despesas - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg"),
      false
    );

    echo $this->view->render("expense", [
      "head" => $head
    ]);
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
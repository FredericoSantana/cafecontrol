<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Auth;
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

    //RESTRIÇÃO
    //O controlador só vai executar e compor as views caso esteja logado e tenha autorização para tal
    if (!$this->user = Auth::user()) {
      $this->message->warning("Efetue login para acessar o App")->flash();
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
    echo $this->view->render("home", []);
  }

  /**
   * APP LOGOUT
   */
  public function logout()
  {
    (new Message())->info("Você saiu com sucesso " . Auth::user()->first_name . ". Volte logo :/")->flash();

    Auth::logout();

    redirect("/entrar");
  }
}
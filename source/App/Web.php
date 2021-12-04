<?php

namespace Source\App;

use Source\Core\Controller;
use Source\Models\Faq\Question;
use Source\Models\Post;
use Source\Support\Pager;


/**
 * Class Web
 * @package Source\App
 */
class Web extends Controller
{
  /**
   * Web constructor.
   */
  public function __construct()
  {
    parent::__construct(__DIR__ . "/../../themes/" . CONF_VIEW_THEME . "/");
  }

  /**
   * SITE HOME
   */
  public function home(): void
  {
    $head = $this->seo->render(
      CONF_SITE_NAME . " - " . CONF_SITE_TITLE,
      CONF_SITE_DESC,
      url(),
      theme("/assets/images/share.jpg")
    );
    
    echo $this->view->render("home", [
      "head" => $head,
      "video" => "lDZGl9Wdc7Y",
      "blog" => (new Post())
        ->find()
        ->order("post_at DESC")
        ->limit(6)
        ->fetch(true)
    ]);
  }

  /**
   * SITE ABOUT
   */
  public function about(): void
  {
    $head = $this->seo->render(
      "Descubra o " . CONF_SITE_NAME . " - " . CONF_SITE_DESC,
      CONF_SITE_DESC,
      url('/sobre'),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("about", [
      "head" => $head,
      "video" => "lDZGl9Wdc7Y",
      "faq" => (new Question())
        ->find("channel_id = :id", "id=1", "question, response")
        ->order("order_by")
        ->fetch(true)
    ]);
  }


  /**
   * SITE BLOG
   * @param array|null $data
   */
  public function blog(?array $data): void
  {
    $head = $this->seo->render(
      "Blog - " . CONF_SITE_NAME,
      "Confira em nosso blog dicas e sacadas de como controlar e melhorar as suas contas. Vamos tomar um café?",
      url('/blog'),
      theme("/assets/images/share.jpg")
    );

    $pager = new Pager(url("blog/page/"));
    $pager->pager(100, 10, ($data['page'] ?? 1));

    echo $this->view->render("blog", [
      "head" => $head,
      "paginator" => $pager->render()
    ]);
  }

  /**
   * SITE BLOG POST
   * @param array $data
   */
  public function blogPost(array $data): void
  {
    $postName = $data['postName'];

    $head = $this->seo->render(
      "POST NAME - " . CONF_SITE_NAME,
      "POST HEADLINE",
      url('/blog/{$postName}'),
      theme("BLOG IMAGE")
    );

    echo $this->view->render("blog-post", [
      "head" => $head,
      "data" => $this->seo->data()
    ]);
  }


  /**
   * SITE LOGIN
   */
  public function login(): void
  {
    $head = $this->seo->render(
      "Entrar - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url("/entrar"),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("auth-login", [
      "head" => $head
    ]);
  }


  /**
   * SITE FORGET
   */
  public function forget(): void
  {
    $head = $this->seo->render(
      "Recuperar Senha - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url("/recuperar"),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("auth-forget", [
      "head" => $head
    ]);
  }


  /**
   * SITE REGISTER
   */
  public function register(): void
  {
    $head = $this->seo->render(
      "Criar Conta - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url("/cadastrar"),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("auth-register", [
      "head" => $head
    ]);
  }

  /**
   * SITE OPT-IN CONFIRM
   */
  public function confirm(): void
  {
    $head = $this->seo->render(
      "Confirme seu cadastro - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url("/confirma"),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("optin-confirm", [
      "head" => $head
    ]);
  }

  /**
   * SITE OPT-IN SUCCESS
   */
  public function success()
  {
    $head = $this->seo->render(
      "Bem-vindo(a) ao - " . CONF_SITE_NAME,
      CONF_SITE_DESC,
      url("/obrigado"),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("optin-success", [
      "head" => $head
    ]);
  }

  /**
   * SITE TERMS
   */
  public function terms(): void
  {
    $head = $this->seo->render(
      CONF_SITE_NAME . " - Termos de Uso",
      CONF_SITE_DESC,
      url('/termos'),
      theme("/assets/images/share.jpg")
    );

    echo $this->view->render("terms", ["head" => $head]);
  }

  /**
   * SITE NAV ERROR
   * @param array $data
   */
  public function error(array $data): void
  {
    $error = new \stdClass();

    switch ($data['errcode']) {
      case "problemas":
        $error->code = "OPS";
        $error->title = "Estamos enfrentando problemas!";
        $error->message = "Sentimos muito, mas o conteúdo que você tentou acessar não existe, está indisponível no momento ou foi removido :/";
        $error->linkTitle = "ENVIAR E-MAIL";
        $error->link = "mailto:" . CONF_MAIL_SUPPORT;
        break;

      case "manutencao":
        $error->code = "OPS";
        $error->title = "Desculpe. Estamos em manutenção!";
        $error->message = "Voltamos logo! Por hora estamos trabalhando para melhorar nosso conteúdo para você controlar melhor as suas contas :P";
        $error->linkTitle = null;
        $error->link = null;
        break;

      default:
        $error->code = $data['errcode'];
        $error->title = "Ooops. Conteúdo indispinível :/";
        $error->message = "Sentimos muito, mas o conteúdo que você tentou acessar não existe, está indisponível no momento ou foi removido :/";
        $error->linkTitle = "Continue navegando!";
        $error->link = url_back();
        break;
    }



    $head = $this->seo->render(
      "{$error->code} | {$error->title}",
      "$error->message",
      url("/ops/{$error->code}"),
      theme("/assets/images/share.jpg"),
      false
    );

    echo $this->view->render("error", [
      "head" => $head,
      "error" => $error
    ]);
  }
}
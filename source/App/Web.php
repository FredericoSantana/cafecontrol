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

    $blog = (new Post())->find();

    $pager = new Pager(url("blog/p/"));
    $pager->pager($blog->count(), 9, ($data['page'] ?? 1));

    echo $this->view->render("blog", [
      "head" => $head,
      "blog" => $blog->limit($pager->limit())->offset($pager->offset())->fetch(true),
      "paginator" => $pager->render()
    ]);
  }

  /**
   * SITE BLOG SEARCH
   * @param array $data
   */
  public function blogSearch(array $data): void
  {
    if (!empty($data['s'])) {
      $search = filter_var($data['s'], FILTER_SANITIZE_STRIPPED);
      echo json_encode(["redirect" => url("/blog/buscar/{$search}/1")]);
      return;
    }

    if (empty($data['terms'])) {
      redirect("/blog");
    }

    $search = filter_var($data['terms'], FILTER_SANITIZE_STRIPPED);
    $page = (filter_var($data['page'], FILTER_VALIDATE_INT) >= 1 ? $data['page'] : 1);

    $head = $this->seo->render(
      "Pesquisa por {$search} - " . CONF_SITE_NAME,
      "Confira os resultados de sua pesquisa para {$search}",
      url("/blog/buscar/{$search}/{page}"),
      theme("/assets/images/share.jpg")
    );

    $post = (new Post())->find(" title LIKE :s OR subtitle LIKE :s", "s=%{$search}%");
    var_dump($post);
  }

  /**
   * SITE BLOG POST
   * @param array $data
   */
  public function blogPost(array $data): void
  {
    $post = (new Post())->findByUri($data['uri']);

    if (!$post) {
      redirect("/404");
    }

    $post->views +=1;
    $post->save();

    $head = $this->seo->render(
      $post->title . " - " . CONF_SITE_NAME,
      $post->subtitle,
      url("/blog/{$post->uri}"),
      image($post->cover, 1200, 628)
    );

    echo $this->view->render("blog-post", [
      "head" => $head,
      "post" => $post,
      "related" => (new Post())
        ->find("category = :c AND id != :i", "c={$post->category}&i={$post->id}")
        ->order("rand()")
        ->limit(3)
        ->fetch(true)
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
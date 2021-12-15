<?php


namespace Source\App\Admin;


use Source\Models\Category;
use Source\Models\Post;
use Source\Models\User;
use Source\Support\Pager;
use Source\Support\Thumb;
use Source\Support\Upload;

/**
 * Class Blog
 * @package Source\App\Admin
 */
class Blog extends Admin
{
  /**
   * Blog constructor.
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * @param array|null $data
   */
  public function home(?array $data): void
  {
    //Search Redirect
    if (!empty($data["s"])) {
      $s = filter_var($data["s"], FILTER_SANITIZE_STRIPPED);
      echo json_encode(["redirect" => url("/admin/blog/home/{$s}/1")]);
      return;
    }

    $search = null;
    $posts = (new Post())->find();

    if (!empty($data["search"]) && $data["search"] != "all") {
      $search = filter_var($data["search"], FILTER_SANITIZE_STRIPPED);
      $posts = (new Post())->find("MATCH(title, subtitle) AGAINST(:s)", "s={$search}");
      if (!$posts->count()) {
        $this->message->info("Sua pesquisa não retornou resultados")->flash();
        redirect("/admin/blog/home");
      }
    }

    $all = ($search ?? "all");
    $pager = new Pager(url("admin/blog/home/{$all}/"));
    $pager->pager($posts->count(), 12, (!empty($data["page"]) ? $data["page"] : 1));

    $head = $this->seo->render(
      CONF_SITE_NAME . " | Blog",
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/blog/home", [
      "app" => "blog/home",
      "head" => $head,
      "posts" => $posts->limit($pager->limit())->offset($pager->offset())->order("post_at DESC")->fetch(true),
      "paginator" => $pager->render(),
      "search" => $search
    ]);
  }

  /**
   * @param array|null $data
   * @throws \Exception
   */
  public function post(?array $data): void
  {
    //MCE Upload
    if (!empty($data["upload"]) && !empty($_FILES["image"])) {
      $files = $_FILES["image"];
      $upload = new Upload();
      $image = $upload->image($files, "post-" . time());

      if (!$image) {
        $json["message"] = $upload->message()->render();
        echo json_encode($json);
        return;
      }

      $json["mce_image"] = '<img style="width: 100%;" src="' . url("/storage/{$image}") . '" alt="{title}" title="{title}">';
      echo json_encode($json);
      return;
    }

    //Create
    if (!empty($data["action"]) && $data["action"] == "create") {
      $content = $data["content"];
      $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

      $postCreate = new Post();
      $postCreate->author = $data["author"];
      $postCreate->category = $data["category"];
      $postCreate->title = $data["title"];
      $postCreate->uri = str_slug($postCreate->title);
      $postCreate->subtitle = $data["subtitle"];
      $postCreate->content = str_replace(["{title}"], [$postCreate->title], $content);
      $postCreate->video = $data["video"];
      $postCreate->status = $data["status"];
      $postCreate->post_at = date_fmt_back($data["post_at"]);

      //Upload cover
      if (!empty($_FILES["cover"])) {
        $files = $_FILES["cover"];
        $upload = new Upload();
        $image = $upload->image($files, $postCreate->title);

        if (!$image) {
          $json["message"] = $upload->message()->render();
          echo json_encode($json);
          return;
        }

        $postCreate->cover = $image;
      }

      if (!$postCreate->save()) {
        $json["message"] = $postCreate->message()->render();
        echo json_encode($json);
        return;
      }

      $this->message->success("Post publicado com sucesso")->flash();
      $json["redirect"] = url("/admin/blog/post/{$postCreate->id}");

      echo json_encode($json);
      return;
    }

    //Update
    if (!empty($data["action"]) && $data["action"] == "update") {
      $content = $data["content"];
      $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
      $postEdit = (new Post())->findById($data["post_id"]);

      if (!$postEdit) {
        $this->message->error("Você tentou atualizar um post que não existe ou foi removido")->flash();
        echo json_encode(["redirect" => url("/admin/blog/home")]);
        return;
      }

      $postEdit->author = $data["author"];
      $postEdit->category = $data["category"];
      $postEdit->title = $data["title"];
      $postEdit->uri = str_slug($postEdit->title);
      $postEdit->subtitle = $data["subtitle"];
      $postEdit->content = str_replace(["{title}"], [$postEdit->title], $content);
      $postEdit->video = $data["video"];
      $postEdit->status = $data["status"];
      $postEdit->post_at = date_fmt_back($data["post_at"]);

      //Upload cover
      if (!empty($_FILES["cover"])) {
        if ($postEdit->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postEdit->cover}")) {
          unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postEdit->cover}");
          (new Thumb())->flush($postEdit->cover);
        }

        $files = $_FILES["cover"];
        $upload = new Upload();
        $image = $upload->image($files, $postEdit->title);

        if (!$image) {
          $json["message"] = $upload->message()->render();
          echo json_encode($json);
          return;
        }

        $postEdit->cover = $image;
      }

      if (!$postEdit->save()) {
        $json["message"] = $postEdit->message()->render();
        echo json_encode($json);
        return;
      }

      $this->message->success("Post atualizado com sucesso")->flash();

      echo json_encode(["reload" => true]);
      return;
    }

    //Delete
    if (!empty($data["action"]) && $data["action"] == "delete") {
      $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
      $postDelete = (new Post())->findById($data["post_id"]);

      if (!$postDelete) {
        $this->message->error("Você tentou excluir um post que não existe ou já foi removido")->flash();
        echo json_encode(["reload" => true]);
        return;
      }

      if ($postDelete->cover && file_exists(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postDelete->cover}")) {
        unlink(__DIR__ . "/../../../" . CONF_UPLOAD_DIR . "/{$postDelete->cover}");
        (new Thumb())->flush($postDelete->cover);
      }

      $postDelete->destroy();
      $this->message->success("O post foi excluído com sucesso")->flash();
      echo json_encode(["reload" => true]);
      return;
    }

    $postEdit = null;

    if (!empty($data["post_id"])) {
      $postId = filter_var($data["post_id"], FILTER_VALIDATE_INT);
      $postEdit = (new Post())->findById($postId);
    }

    $head = $this->seo->render(
      CONF_SITE_NAME . " | " . ($postEdit->title ?? "Novo Artigo"),
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/blog/post", [
      "app" => "blog/post",
      "head" => $head,
      "post" => $postEdit,
      "categories" => (new Category())->find("type = :type", "type=post")->order("title")->fetch(true),
      "authors" => (new User())->find("level >= :level", "level=5")->fetch(true)
    ]);
  }

  /**
   * @param array|null $data
   */
  public function categories(?array $data): void
  {

  }

  /**
   * @param array|null $data
   */
  public function category(?array $data): void
  {

  }
}
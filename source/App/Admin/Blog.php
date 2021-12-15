<?php


namespace Source\App\Admin;


use Source\Models\Category;
use Source\Models\Post;
use Source\Models\User;
use Source\Support\Pager;
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
      "posts" => $posts->order("post_at DESC")->limit($pager->limit())->offset($pager->offset())->fetch(true),
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

    //Update

    //Delete

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
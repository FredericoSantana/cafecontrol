<?php


namespace Source\App\Admin;

use Source\Models\User;
use Source\Support\Pager;
use Source\Support\Thumb;
use Source\Support\Upload;

class Users extends Admin
{
  public function __construct()
  {
    parent::__construct();
  }

  public function home(?array $data): void
  {
    //search redirect
    if (!empty($data["s"])) {
      $s = filter_var($data["s"], FILTER_SANITIZE_STRIPPED);
      echo json_encode(["redirect" => url("/admin/users/home/{$s}/1")]);
      return;
    }

    $search = null;
    $users = (new User())->find();

    if (!empty($data["search"]) && $data["search"] != "all") {
      $search = filter_var($data["search"], FILTER_SANITIZE_STRIPPED);
      $users = (new User())->find("MATCH(first_name, last_name, email) AGAINST(:s)", "s={$search}");
      if (!$users->count()) {
        $this->message->info("Sua pesquisa não retornou resultados")->flash();
        redirect("/admin/users/home");
      }
    }

    $all = ($search ?? "all");
    $pager = new Pager(url("/admin/users/home/{$all}/"));
    $pager->pager($users->count(), 12, (!empty($data["page"]) ? $data["page"] : 1));


    $head = $this->seo->render(
      CONF_SITE_NAME . " | Usuários",
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/users/home", [
      "app" => "users/home",
      "head" => $head,
      "search" => $search,
      "users" => $users->order("first_name, last_name")->limit($pager->limit())->offset($pager->offset())->fetch(true),
      "paginator" => $pager->render()
    ]);
  }

  public function user(?array $data): void
  {
    //create
    if (!empty($data["action"]) && $data["action"] == "create") {
      $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);

      $userCreate = new User();
      $userCreate->first_name = $data["first_name"];
      $userCreate->last_name = $data["last_name"];
      $userCreate->email = $data["email"];
      $userCreate->password = $data["password"];
      $userCreate->level = $data["level"];
      $userCreate->genre = $data["genre"];
      $userCreate->datebirth = date_fmt_back($data["datebirth"]);
      $userCreate->document = preg_replace("/[^0-9]/", "", $data["document"]);
      $userCreate->status = $data["status"];

      //upload photo
      if (!empty($_FILES["photo"])) {
        $files = $_FILES["photo"];
        $upload = new Upload();
        $image = $upload->image($files, $userCreate->fullName(), 600);

        if (!$image) {
          $json["message"] = $upload->message()->render();
          echo json_encode($json);
          return;
        }

        $userCreate->photo = $image;
      }

      if (!$userCreate->save()) {
        $json["message"] = $userCreate->message()->render();
        echo json_encode($json);
        return;
      }

      $this->message->success("Usuário cadastrado com sucesso...")->flash();
      $json["redirect"] = url("/admin/users/user/{$userCreate->id}");

      echo json_encode($json);
      return;
    }

    $userEdit = null;
    if (!empty($data["user_id"])) {
      $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);
      $userEdit = (new User())->findById($userId);
    }

    $head = $this->seo->render(
      CONF_SITE_NAME . " | " . ($userEdit ? "Perfil de {$userEdit->fullName()}" : "Novo Usuário"),
      CONF_SITE_DESC,
      url("/admin"),
      url("/admin/assets/images/image.jpg"),
      false
    );

    echo $this->view->render("widgets/users/user", [
      "app" => "users/user",
      "head" => $head,
      "user" => $userEdit
    ]);
  }
}
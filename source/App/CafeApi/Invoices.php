<?php

namespace Source\App\CafeApi;

use Source\Models\CafeApp\AppInvoice;
use Source\Support\Pager;

class Invoices extends CafeApi
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * List All Invoices
   */
  public function index(): void
  {
    $where = "";
    $params = "";
    $values = $this->headers;

    //By Wallet
    if (!empty($values["wallet_id"]) && $wallet_id = filter_var($values["wallet_id"], FILTER_VALIDATE_INT)) {
      $where .= " AND wallet_id = :wallet_id";
      $params .= "&wallet_id={$wallet_id}";
    }

    //By Type
    $typeList = ["income", "expense", "fixed_income", "fixed_expense"];
    if (!empty($values["type"]) && in_array($values["type"], $typeList) && $type = $values["type"]) {
      $where .= " AND type = :type";
      $params .= "&type={$type}";
    }

    //By Status
    $statusList = ["paid", "unpaid"];
    if (!empty($values["status"]) && in_array($values["status"], $statusList) && $status = $values["status"]) {
      $where .= " AND status = :status";
      $params .= "&status={$status}";
    }

    //Get Invoices
    $invoices = (new AppInvoice())->find("user_id = :user_id{$where}",
    "user_id={$this->user->id}{$params}");

    if (!$invoices->count()) {
      $this->call(
        404,
        "not_found",
        "Nada encontrado para sua pesquisa. Tente outros termos"
      )->back(["results" => 0]);
      return;
    }

    $page = (!empty($values["page"]) ? $values["page"] : 1);
    $pager = new Pager(url("/invoices/"));
    $pager->pager($invoices->count(), 10, $page);

    $response["results"] = $invoices->count();
    $response["page"] = $pager->page();
    $response["pages"] = $pager->pages();

    foreach ($invoices->limit($pager->limit())->offset($pager->offset())->order("due_at ASC")->fetch(true) as $invoice) {
      $response["invoices"][] = $invoice->data();
    }

    $this->back($response);
    return;
  }

  public function create(array $data): void
  {

  }

  public function read(array $data): void
  {

  }

  public function update(array $data): void
  {

  }

  public function delete(array $data): void
  {

  }
}
<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;
use Source\Models\User;

/**
 * Class AppInvoice
 * @package Source\Models\CafeApp
 */
class AppInvoice extends Model
{
  /**
   * AppInvoice constructor.
   */
  public function __construct()
  {
    parent::__construct("app_invoices", ["id"],
      ["user_id", "wallet_id", "category_id", "description", "type", "value", "due_at", "repeat_when"]
    );
  }


  /**
   * @param User $user
   * @param string $type
   * @param array|null $filter
   * @param int|null $limit
   * @return array|null
   */
  public function filter(User $user, string $type, ?array $filter, ?int $limit = null): ?array
  {
    $status = (!empty($filter["status"]) && $filter["status"] == "paid" ? "AND status = 'paid'" : (!empty($filter["status"]) && $filter["status"] == "unpaid" ? "AND status = 'unpaid'" : null));
    $category = (!empty($filter["category"]) && $filter["category"] != "all" ? "AND category_id = '{$filter["category"]}'" : null);

    $due_year = (!empty($filter["date"]) ? explode("-", $filter["date"])[1] : date("Y"));
    $due_month = (!empty($filter["date"]) ? explode("-", $filter["date"])[0] : date("m"));
    $due_at = "AND (year(due_at) = '{$due_year}' AND month(due_at) = '{$due_month}')";

    $due = $this->find(
      "user_id = :user AND type = :type {$status} {$category} {$due_at}",
      "user={$user->id}&type={$type}"
    )->order("day(due_at) ASC");

    if ($limit) {
      $due->limit($limit);
    }

    return $due->fetch(true);
  }

  /**
   * @return AppCategory
   */
  public function category(): AppCategory
  {
    return (new AppCategory())->findById($this->category_id);
  }
}
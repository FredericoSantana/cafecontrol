<?php

namespace Source\Models\CafeApp;

use Source\Core\Model;

class AppInvoice extends Model
{
  public function __construct()
  {
    parent::__construct(
      "app_invoice",
      ["id"],
      [
        "user_id",
        "wallet_id",
        "category_id",
        "description",
        "type",
        "value",
        "due_at",
        "repeat_when"
      ]
    );
  }
}
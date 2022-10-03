<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinanceTransaction extends Model
{
    use SoftDeletes;

    public function finance_account(){
        return $this->belongsTo("App\FinanceAccount", "finance_account_id");
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FinanceTransaction;
use Auth;
use Log;

class FinanceTransactionController extends Controller
{

    public function createFinanceTransaction(Request $request, $id = null){
        try {
            $validator = Validator::make($request->all(), [
                'name'              => 'required|string|max:255',
                'finance_account_id'=> 'nullable|integer|exists:finance_accounts,id',
                'amount'            => 'required|numeric',
                'description'       => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                $message = $validator->errors();
                return $this->ResponseJson(
                    CONFIG("statusmessage.BAD_REQUEST"),
                    null,
                    $message,
                );
            }
            $user_id =  Auth::user()->id;

            $account = new FinanceTransaction;
            if(!is_null($id)){
                $account = FinanceTransaction::find($id);
                if(is_null($account)){
                    return $this->ResponseJson(
                        CONFIG("statusmessage.RESOURCE_NOT_FOUND"),
                    );
                }
                $account->updated_at = time();
            }else{
                $account->user_id = $user_id;
                $account->created_at = time();
            }
            
            $account->name = $request->get("name");
            $account->finance_account_id = $request->get("finance_account_id");
            $account->description = $request->get("description");
            $account->amount = $request->get("amount");
            $account->save();

            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function financeTransactions(Request $request){
        try {
            $limit = $request->get("limit") ?? 20;
            $page = $request->get("page") ?? 1;
            if($page < 1){
                $page = 1;
            }
            $offset = $limit * ($page - 1);
            $search = $request->get("search");
            $start_date = $request->get("start_date");
            $end_date = $request->get("end_date");
            $date = $request->get("date");
            $finance_account_name = $request->get("finance_account_name");

            $user_id =  Auth::user()->id;
            
            $query = FinanceTransaction::with("finance_account")->where("user_id", $user_id);
            
            if(isset($search)){
                $query->where(function($query) use($search) {
                    $query->where("name", "LIKE", "%$search%")
                        ->orWhere("description", "LIKE", "%$search%");
                });
            }

            if(isset($start_date)){
                $query->whereDate("created_at", ">=", $start_date);
            }

            if(isset($end_date)){
                $query->whereDate("created_at", "<=", $end_date);
            }

            if(isset($date)){
                $query->whereDate("created_at", $date);
            }

            if(isset($finance_account_name)){
                $query->whereHas("finance_account", function($query) use($finance_account_name){
                    $query->where("name", "LIKE", "%$finance_account_name%");
                });
            }

            $total_data = $query->count();
            $query->skip($offset)->take($limit);

            $data = $query->orderBy("created_at", "desc")
                        ->get();

            return $this->ResponsePaginateJson(
                CONFIG("statusmessage.SUCCESS"),
                $data,
                $limit,
                ((int) $page) + 1,
                $total_data,
                $page
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function deleteFinanceTransaction(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $account = FinanceTransaction::where("id", $id)
                            ->where("user_id", $user_id)
                            ->first();
            if(is_null($account)){
                return $this->ResponseJson(
                    CONFIG("statusmessage.RESOURCE_NOT_FOUND"),
                );
            }
            $account->delete();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }
    
    public function detailFinanceTransaction(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $account = FinanceTransaction::with("finance_account")->where("id", $id)
                                ->where("user_id", $user_id)
                                ->first();
            if(is_null($account)){
                return $this->ResponseJson(
                    CONFIG("statusmessage.RESOURCE_NOT_FOUND"),
                );
            }
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
                $account
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function restoreFinanceTransaction(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $transaction = FinanceTransaction::onlyTrashed()
                        ->where("id", $id)
                        ->where("user_id", $user_id)
                        ->first();

            if(is_null($transaction)){
                return $this->ResponseJson(
                    CONFIG("statusmessage.RESOURCE_NOT_FOUND"),
                );
            }
            $transaction->restore();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function dailyReports(Request $request){
        try {

            $start_date = $request->get("start_date");
            $end_date = $request->get("end_date");

            $user_id =  Auth::user()->id;
            $query = FinanceTransaction::selectRaw("DATE(created_at) as date, SUM(amount) as total_amount")
                        ->where("user_id", $user_id);

            if(isset($start_date)){
                $query->whereDate("created_at", ">=", $start_date);
            }

            if(isset($end_date)){
                $query->whereDate("created_at", "<=", $end_date);
            }

            $data = $query->groupBy("date")->get();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
                $data
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function monthlyReports(Request $request){
        try {
            $year = $request->get("year");

            $user_id =  Auth::user()->id;
            $query = FinanceTransaction::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date, SUM(amount) as total_amount")
                        ->where("user_id", $user_id);

            if(isset($year)){
                $query->whereYear("created_at", $year);
            }

            $data = $query->groupBy("date")->get();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
                $data
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }
}

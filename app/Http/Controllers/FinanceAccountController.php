<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\FinanceAccount;
use Auth;
use Log;

class FinanceAccountController extends Controller
{
    public function financeAccounts(Request $request){
        try {
            $limit = $request->get("limit") ?? 20;
            $page = $request->get("page") ?? 1;
            if($page < 1){
                $page = 1;
            }
            $offset = $limit * ($page - 1);
            $search = $request->get("search");
            $finance_account_name = $request->get("finance_account_name");
            $user_id =  Auth::user()->id;
            
            $query = FinanceAccount::where("user_id", $user_id);

            if(isset($search)){
                $query->where(function($query) use($search) {
                    $query->where("name", "LIKE", "%$search%")
                        ->orWhere("type", "LIKE", "%$search%")
                        ->orWhere("description", "LIKE", "%$search%");
                });
            }

            if(isset($finance_account_name)){
                $query->where("name", "LIKE", "%$finance_account_name%");
            }

            $total_data = $query->count();
            $query->skip($offset)->take($limit);

            $data = $query->get();

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

    public function createFinanceAccounts(Request $request, $id = null){
        try {
            $validator = Validator::make($request->all(), [
                'name'      => 'required|string|max:255',
                'type'      => 'nullable|string|max:255',
                'description'   => 'nullable|string|max:500',
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

            $account = new FinanceAccount;
            if(!is_null($id)){
                $account = FinanceAccount::find($id);
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
            $account->type = $request->get("type");
            $account->description = $request->get("description");
            $account->save();

            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }

    public function deleteFinanceAccount(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $account = FinanceAccount::where("id", $id)
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
    
    public function detailFinanceAccount(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $account = FinanceAccount::where("id", $id)
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

    public function restoreFinanceAccount(Request $request, $id){
        try {
            $user_id =  Auth::user()->id;
            $account = FinanceAccount::onlyTrashed()
                            ->where("id", $id)
                            ->where("user_id", $user_id)
                            ->first();

            if(is_null($account)){
                return $this->ResponseJson(
                    CONFIG("statusmessage.RESOURCE_NOT_FOUND"),
                );
            }
            $account->restore();
            return $this->ResponseJson(
                CONFIG("statusmessage.SUCCESS"),
            );

        } catch (\Exception $e) {
            Log::error($e);
            return $this->ResponseJsonError();
        }
    }
}

<?php

namespace App\Http\Controllers\API\Backend\Stocks;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Localization;
use Illuminate\Http\Request;
use Cache;

class CurrenciesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:currency_settings'])->only('index');
        $this->middleware(['permission:add_currency'])->only('store');
        $this->middleware(['permission:edit_currency'])->only(['edit', 'update']);
        $this->middleware(['permission:publish_currency'])->only(['updateStatus']);
    }

    # change the currency
    public function changeCurrency(Request $request)
    {

        $request->session()->put('currency_code', $request->currency_code);

        $currency = Currency::where('code', $request->currency_code)->first();

        $request->session()->put('local_currency_rate', $currency->rate);
        $request->session()->put('currency_symbol', $currency->symbol);
        $request->session()->put('currency_symbol_alignment', $currency->alignment);

        flash(localize('Currency changed to ') . ' ' . $currency->name)->success();
        return response()->json([
            'message'=>'Currency changed to'.' '.$currency->name
        ],200);
    }

    # currency list
    public function index(Request $request)
    {
        $searchKey = null;
        $currencies = Currency::oldest();
        if ($request->search != null) {
            $currencies = $currencies->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $currencies = $currencies->get();
        return response()->json([
            $currencies,
            $searchKey
        ],200);
    }

    # store currency
    public function store(Request $request)
    {
        if (Currency::where('code', $request->code)->first()) {
            flash(localize('This code is already used for another currency'))->error();
            return response()->json([
               'message'=>'This code is already used for another currency'
            ],200);
        }

        $currency = new Currency;
        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->rate = $request->rate;
        $currency->alignment = $request->alignment;
        $currency->save();

        Cache::forget('currencies');
        flash(localize('Currency has been inserted successfully'))->success();
        return response()->json([
            'message'=>'Currency has been inserted successfully'
        ],200);
    }

    # edit currency
    public function edit($id)
    {
        $currency = Currency::findOrFail($id);
        return response()->json([
            $currency
        ],200);
    }

    # update currency
    public function update(Request $request)
    {
        $checkCurrency = Currency::where('code', $request->code)->first();
        $currency = Currency::findOrFail($request->id);

        if (
            $checkCurrency &&
            $checkCurrency->id != $currency->id
        ) {
            flash(localize('This code is already used for another currency'))->error();
            return response()->json([
                'message'=>'This code is already used for another currency'
            ],200);
        }

        $currency->name = $request->name;
        $currency->symbol = $request->symbol;
        $currency->code = $request->code;
        $currency->rate = $request->rate;
        $currency->alignment = $request->alignment;
        $currency->save();

        Cache::forget('currencies');
        flash(localize('Currency has been updated successfully'))->success();
        return response()->json([
            'message'=>'Currency has been updated successfully'
        ],200);
    }

    # update status 
    public function updateStatus(Request $request)
    {
        $currency = Currency::findOrFail($request->id);
        $activatedCurrencies = Currency::where('is_active', 1)->count();

        if (getSetting('default_currency') == $currency->code && $request->is_active == 0) {
            flash(localize('Default currency can not be disabled'))->error();
            return response()->json([
                'message'=>'Default currency can not be disabled'
            ],200);
        } elseif ($activatedCurrencies <= 1 && $request->is_active == 0) {
            flash(localize('Minimum 1 currency need to be enabled'))->error();
            return response()->json([
                'message'=>'Minimum 1 currency need to be enabled'
            ],200);
        }

        $currency->is_active = $request->is_active;
        if ($currency->save()) {
            flash(localize('Status updated successfully'))->success();
            return response()->json([
                'message'=>'Status updated successfully'
            ],200);
        }
        return 0;
    }
}

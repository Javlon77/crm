<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Cost;
use App\Models\Currency;
use App\Models\Plan;

class FinanceController extends Controller
{
    public function index(){

        $sales = sales::all();
        $costs = Cost::all();
        return view('finance.index', compact('sales','costs'));
    }

    //plan index
    public function plan(){

        $plans = plan::orderBy('year') -> get();
        return view('finance.plan', compact('plans'));
    }

    //plan save 
    public function storePlan(Request $request){

        $data = $request -> validate([
            'year' => 'unique:plans,year'
        ],
        [
            'year.unique' => $request -> year. ' - yil uchun plan allaqachon mavjud mavjud'
        ]);
        $data['first_plan'] = str_replace(' ', '', $request -> first_plan);
        $data['second_plan'] = str_replace(' ', '', $request -> second_plan);
        $data['annual_plan'] = str_replace(' ', '', $request -> annual_plan);
        
        plan::create($data);
        return redirect() -> back();
    }

    // plan destroy
    public function destroyPlan($id) {
        plan::find($id) -> delete(); 
        return redirect() -> back();
    }

    // annual plan data
    public function annual(Request $request){
        $month_name = [
            '1' => 'Yanvar',
            '2' => 'Fevral',
            '3' => 'Mart',
            '4' => 'Aprel',
            '5' => 'May',
            '6' => 'Iyyun',
            '7' => 'Iyyul',
            '8' => 'Avgust',
            '9' => 'Sentabr',
            '10' => 'Oktabr',
            '11' => 'Noyabr',
            '12' => 'Dekabr',
        ];
        if($request -> has('year')){
            $year = $request -> year;
        } else $year = now() -> format('Y');

        $plan = plan::where('year', $year) -> first();
        $annual_cost = Cost::whereYear('created_at', $year) -> get();
        $costs = $annual_cost ->groupBy('month');
        $sales = sales::whereYear('created_at', $year) -> get();
        $months = $sales -> groupBy('month');
          
        foreach (now()->subMonths(12)->monthsUntil(now()) as $date) {
            $months[$date->format('m')] = $months[$date->format('m')] ?? collect([]);
        }
        foreach (now()->subMonths(12)->monthsUntil(now()) as $date) {
            $costs[$date->format('m')] = $costs[$date->format('m')] ?? collect([]);
        }
        
        // first_plan data collect
        $first_plan_array =array_filter($months -> toarray(), fn($key) => $key < 7,ARRAY_FILTER_USE_KEY);
        $first_plan_array = collect($first_plan_array) -> map(function ($name) { return collect($name); });
        $first_plan['total_amount'] = 0;
        $first_plan['total_amount_usd'] = 0;
        $first_plan['profit'] = 0;
        $first_plan['profit_usd'] = 0;
        $first_plan['net_profit'] = 0;
        $first_plan['net_profit_usd'] = 0;
        $first_plan['sale'] = 0;
        $first_plan['product'] = 0;
        
        foreach($first_plan_array as $i){
            $first_plan['total_amount'] += $i -> sum('total_amount');   
            $first_plan['total_amount_usd'] += $i -> sum('total_amount_usd');   
            $first_plan['profit'] += $i -> sum('profit');   
            $first_plan['profit_usd'] += $i -> sum('profit_usd');   
            $first_plan['net_profit'] += $i -> sum('net_profit');   
            $first_plan['net_profit_usd'] += $i -> sum('net_profit_usd');   
            $first_plan['sale'] += $i -> count();   
            $first_plan['product'] += $i -> sum('total_quantity');   
        }
        $first_plan['cost'] = $costs['01']->sum('cost') + $costs['02']->sum('cost') + $costs['03']->sum('cost') + $costs['04']->sum('cost') + $costs['05']->sum('cost') + $costs['06']->sum('cost') + ( $first_plan['profit'] -  $first_plan['net_profit'] );
        $first_plan['cost_usd'] = $costs['01']->sum('cost_usd') + $costs['02']->sum('cost_usd') + $costs['03']->sum('cost_usd') + $costs['04']->sum('cost_usd') + $costs['05']->sum('cost_usd') + $costs['06']->sum('cost_usd') + ( $first_plan['profit_usd'] -  $first_plan['net_profit_usd'] );
        
        // second_plan
        $second_plan_array =array_filter($months -> toarray(), fn($key) => $key > 6,ARRAY_FILTER_USE_KEY);
        $second_plan_array = collect($second_plan_array) -> map(function ($name) { return collect($name); });
        $second_plan['total_amount'] = 0;
        $second_plan['total_amount_usd'] = 0;
        $second_plan['profit'] = 0;
        $second_plan['profit_usd'] = 0;
        $second_plan['net_profit'] = 0;
        $second_plan['net_profit_usd'] = 0;
        $second_plan['sale'] = 0;
        $second_plan['product'] = 0;
        
        foreach($second_plan_array as $i){
            $second_plan['total_amount'] += $i -> sum('total_amount');   
            $second_plan['total_amount_usd'] += $i -> sum('total_amount_usd');   
            $second_plan['profit'] += $i -> sum('profit');   
            $second_plan['profit_usd'] += $i -> sum('profit_usd');   
            $second_plan['net_profit'] += $i -> sum('net_profit');   
            $second_plan['net_profit_usd'] += $i -> sum('net_profit_usd');   
            $second_plan['sale'] += $i -> count();   
            $second_plan['product'] += $i -> sum('total_quantity');   
        }
        $second_plan['cost'] = $costs['07']->sum('cost') + $costs['08']->sum('cost') + $costs['09']->sum('cost') + $costs['10']->sum('cost') + $costs['11']->sum('cost') + $costs['12']->sum('cost') + ( $second_plan['profit'] -  $second_plan['net_profit'] );
        $second_plan['cost_usd'] = $costs['07']->sum('cost_usd') + $costs['08']->sum('cost_usd') + $costs['09']->sum('cost_usd') + $costs['10']->sum('cost_usd') + $costs['11']->sum('cost_usd') + $costs['12']->sum('cost_usd') + ( $second_plan['profit_usd'] -  $second_plan['net_profit_usd'] );
        
        return view('finance.annual', compact('month_name', 'sales','months','first_plan', 'second_plan', 'costs','annual_cost', 'year', 'plan'));
    }
}
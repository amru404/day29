<?php

namespace App\Http\Controllers\Admin;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Auth\User\User;
use App\Models\Blog;
use App\Models\Product;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $counts = [
            'users' => \DB::table('users')->count(),
            'users_unconfirmed' => \DB::table('users')->where('confirmed', false)->count(),
            'users_inactive' => \DB::table('users')->where('active', false)->count(),
            'protected_pages' => 0,
        ];

        foreach (\Route::getRoutes() as $route) {
            foreach ($route->middleware() as $middleware) {
                if (preg_match("/protection/", $middleware, $matches)) $counts['protected_pages']++;
            }
        }

        return view('admin.dashboard', ['counts' => $counts]);
    }


    public function getLogChartData(Request $request)
    {
        \Validator::make($request->all(), [
            'start' => 'required|date|before_or_equal:now',
            'end' => 'required|date|after_or_equal:start',
        ])->validate();

        $start = new Carbon($request->get('start'));
        $end = new Carbon($request->get('end'));

        $dates = collect(\LogViewer::dates())->filter(function ($value, $key) use ($start, $end) {
            $value = new Carbon($value);
            return $value->timestamp >= $start->timestamp && $value->timestamp <= $end->timestamp;
        });


        $levels = \LogViewer::levels();

        $data = [];

        while ($start->diffInDays($end, false) >= 0) {

            foreach ($levels as $level) {
                $data[$level][$start->format('Y-m-d')] = 0;
            }

            if ($dates->contains($start->format('Y-m-d'))) {
                /** @var  $log Log */
                $logs = \LogViewer::get($start->format('Y-m-d'));

                /** @var  $log LogEntry */
                foreach ($logs->entries() as $log) {
                    $data[$log->level][$log->datetime->format($start->format('Y-m-d'))] += 1;
                }
            }

            $start->addDay();
        }

        return response($data);
    }

    public function getRegistrationChartData()
    {

        $data = [
            'registration_form' => User::whereDoesntHave('providers')->count(),
            'google' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'google');
            })->count(),
            'facebook' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'facebook');
            })->count(),
            'twitter' => User::whereHas('providers', function ($query) {
                $query->where('provider', 'twitter');
            })->count(),
        ];

        return response($data);
    }

    function getReporting(Request $request) {
        $product = Product::all();
        $testProduct = Product::query();

        $testProduct->when($request->hargaAwal, function ($query) use ($request){
            return $query->where('harga','=>', $request->hargaAwal, '&&', 'harga', '=', $request->hargaAkhir);
        });
        return view('admin.reporting',compact('product'));
    }

    function getAllProduct() {
        $product = Product::all();
        for ($i=0; $i <count($product) ; $i++) { 
            if ($product[$i]['harga'] < 50000) {
                $product[$i]['price_range'] = 'less_50000';
            } else if ($product[$i]['harga'] >= 50000 && $product[$i]['harga'] < 99999){
                $product[$i]['price_range'] = '_50000_99999';
            } else if ($product[$i]['harga'] >= 100000 && $product[$i]['harga'] < 999999){
                $product[$i]['price_range'] = '_100000_999999';
            } else{
                $product[$i]['price_range'] = 'more_1000000';
            }
            $product[$i]['created_range'] = substr($product[$i]['created_at'],0,7);

        }
        return response($product);
    }

    function getChartProduct(Request $request) {
        $product = Product::all();

        $less_50000 = 0;
        $_50000_99999 = 0;
        $_100000_999999 = 0;
        $more_1000000 = 0;

        for ($i = 0; $i < count($product); $i++) { 
            // Classify price ranges
            if ($product[$i]['harga'] < 50000) {
                $product[$i]['price_range'] = 'less_50000';
                $less_50000++;
            } else if ($product[$i]['harga'] >= 50000 && $product[$i]['harga'] < 100000) {
                $product[$i]['price_range'] = '_50000_99999';
                $_50000_99999++; 
            } else if ($product[$i]['harga'] >= 100000 && $product[$i]['harga'] < 1000000) {
                $product[$i]['price_range'] = '_100000_999999';
                $_100000_999999++; 
            } else {
                $product[$i]['price_range'] = 'more_1000000';
                $more_1000000++;
            }

            $product[$i]['created_range'] = substr($product[$i]['created_at'], 0, 7);
        }       
            $data = [
                "less_50000" => $less_50000,
                "_50000_99999" => $_50000_99999,
                "_100000_999999" => $_100000_999999,
                "more_1000000" => $more_1000000,
            ];

            return response($data);
        }
    }

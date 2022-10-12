<?php
namespace App\Controllers;

use Core\Database\DB;
use Core\Database\QueryBuilder;
use Core\Http\JsonResponse;
use Core\Http\Request;


class IndexController
{
    public function index(Request $request)
    {
//        $conn = DB::getInstance();
//        $query = new QueryBuilder($conn, 'test');
//        dd($query->whereBetween('co2', 0, 100)->first());
//        DB::table('test')->where('username', '=', 'boris')->update(['col1' => 'boris2']);
        DB::table('test')->insert(['username' => 'user_name', 'col1' => 'val3', 'co2' => rand(0,500)]);
        $data = DB::table('test')->whereBetween('co2', 0, 100)->get();
        return jsonResponse($data);
    }


    public function edit(Request $request, $var1, $var2)
    {
        $a =  "method: " . $request->getMethod() . "<br>";
        $a .= "var1 = $var1 <br> var2 = $var2";
        return response($a, 201);
    }
}
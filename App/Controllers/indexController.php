<?php
namespace App\Controllers;

use Core\Http\Request;


class IndexController
{
    public function index(Request $request)
    {
        return response('Hello world');
    }


    public function edit(Request $request, $var1, $var2)
    {
        $a =  "method: " . $request->getMethod() . "<br>";
        $a .= "var1 = $var1 <br> var2 = $var2";
        return response($a, 201);
    }
}
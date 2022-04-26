<?php

namespace App\Http\Controllers;


use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    public function index(Request $request)
    {
        $cityQuery=City::query();

        if($request->has('name')) {
            $cityQuery->where('name', 'like', '%'.$request->get('name').'%');
        }
        if($request->has('parent_id')) {
            $cityQuery->where('parent_id', '=', $request->get('parent_id'));
        }
        if($request->has('limit')&&$request->has('page')) {
            $page = $request->page;
            $limit = $request->limit;
            $offset = ($page - 1) * $limit;
            $count = count($cityQuery->get());
            $cities = $cityQuery->limit($limit)->offset($offset)->get();

        }
        else
        {
            $count = count($cityQuery->get());
            $cities = $cityQuery->get();
        }
        $countries=Country::get();
//        print_r($countries);
//        die();

        $datas=treeForTwoTable($countries,$cities);
        clearEmptyChildren($datas);
        return response()->json(['data' => $datas, 'total' => $count]);

    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'parent_id'=>['required','integer',Rule::exists('countries','id')]
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }


        $model= new City();
        $model->name=$request->name;
        $model->parent_id=$request->parent_id;
        $model->save();

        return createSuccess($model);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'parent_id'=>['required','integer',Rule::exists('countries','id')],

        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= City::find($request->id);
        $model->name=$request->name;
        $model->parent_id=$request->parent_id;
        $model->save();

        return updateSuccess($model);
    }
    public function delete($id)
    {
        $district=checkIfExist('District','parent_id',$id);
        $city=checkIfExist('City','id',$id);
        if($city==0){
            return notFoundError($id);
        }
        if($district==1){
            return notDeleteError();
        }

        else{
            City::find($id)->delete();
            return deleted();
        }


    }
    public function single($id)
    {

        $model=City::query()
            ->select('*')
            ->where('id', $id)
            ->first();
        if($model!=null) {
            return response()->json($model);
        }
        else{
            return notFoundError($id);
        }
    }

    }

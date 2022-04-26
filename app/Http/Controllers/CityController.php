<?php

namespace App\Http\Controllers;


use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): JsonResponse
    {
        paginate ($request, $limit, $offset);
        $cityQuery = City::query();

        if($request -> has('name')) {
            $cityQuery->where('name', 'like', '%'.$request->get('name').'%');
        }
        if($request -> has('parent_id')) {
            $cityQuery -> where('parent_id', '=', $request->get('parent_id'));
        }

        $count = count($cityQuery->get());
        $cities = $cityQuery->limit($limit)->offset($offset)->get();


        foreach ($cities as $city){
            $country=Country::query()->find($city->id);
            $city->parent_id=$country->name;
        }
        return response()->json(['data' => $cities, 'total' => $count]);

    }

    public function tree(Request $request): JsonResponse
    {
        paginate($request, $limit, $offset);
        $cityQuery=City::query();

        if($request->has('name')) {
            $cityQuery->where('name', 'like', '%'.$request->get('name').'%');
        }
        if($request->has('parent_id')) {
            $cityQuery->where('parent_id', '=', $request->get('parent_id'));
        }

            $count = count($cityQuery->get());
            $cities = $cityQuery->limit($limit)->offset($offset)->get();

        $countries=Country::get();


        $datas=treeForTwoTable($countries,$cities);
        clearEmptyChildren($datas);
        return response()->json(['data' => $datas, 'total' => $count]);

    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'=>['required','string'],
            'parent_id'=>['required','integer',Rule::exists('countries','id')]
        ]);

        if ($validator->fails())
        {
            return validationError($validator->errors());
        }

        $model= new City($request->only([
            'name',
            'parent_id',
        ]));

        $model->save();

        return createSuccess($model);
    }

    public function update(Request $request): JsonResponse
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
        $model->update($request->only([
            'name',
            'parent_id',
        ]));
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
    public function single($id): JsonResponse
    {

        $model = City::query()->find($id);

        if (!$model) {
            return notFoundError($id);
        }

        return response()->json($model);
    }

    }

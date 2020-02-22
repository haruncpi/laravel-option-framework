<?php

namespace Haruncpi\LaravelOptionFramework\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OptionController extends Controller
{
    private $viewPath, $dataPath, $baseUrl;
    private $ids = [];

    public function __construct()
    {
        $this->baseUrl = url('/');
        $this->viewPath = url(config('option-framework.view_route_path'));
        $this->dataPath = $this->viewPath . "?data_request=true";

    }

    private function keysExist(array $keys, array $arr)
    {
        return !array_diff_key(array_flip($keys), $arr);
    }

    private function validateOptions()
    {
        $options = config('options');
        if($options=='' || $options == null){
            throw new \Exception('No options configuration found');
        }

        if (count($options) == count($options, COUNT_RECURSIVE)) {
            throw new \Exception('Option config must be array( section_array(), section_array(), ... )');
        }
        foreach ($options as $op) {
            $sectionMandatory = ['id', 'label', 'icon', 'fields'];
            if (!$this->keysExist($sectionMandatory, $op)) {
                throw new \Exception('Every section must have ' . implode(',', $sectionMandatory));
            }

            $fields = $op['fields'];
            if (count($fields) == count($fields, COUNT_RECURSIVE)) {
                throw new \Exception('fields must be array of array');
            }

            $temp = ["id" => ""];
            foreach ($fields as $f) {
                $fieldMandatory = ['type', 'id', 'label'];
                if (!$this->keysExist($fieldMandatory, $f)) {
                    throw new \Exception('Every field must have ' . implode(',', $fieldMandatory));
                }
                //duplicate field checking
                if ($f['id'] == $temp['id']) {
                    throw new \Exception('Every field id must be unique');
                }
                $temp['id'] = $f['id'];
                //getting ids for checking update options
                array_push($this->ids, $f['id']);
            }
        }
    }


    private function getFields()
    {
        $options = config('options');
        $fields = [];
        foreach ($options as $row) {
            foreach ($row['fields'] as $field) {
                $fields[$field['id']] = $field;
            }
        }

        return $fields;
    }

    private function getValidationRules($forInputs = [])
    {
        $fields = $this->getFields();
        $rules = [];

        if (count($forInputs)) {
            foreach ($forInputs as $input) {
                if (isset($fields[$input]['validation']))
                    $rules[$input] = $fields[$input]['validation'];
            }

        } else {
            foreach ($fields as $f) {
                if (isset($f['validation']))
                    $rules[$f['id']] = $f['validation'];
            }
        }


        if (count($rules) == 0) {
            return [];
        } else {
            return $rules;
        }
    }

    private function autocompleteHandler(Request $request)
    {
        $this->validate($request, [
            'options' => 'sometimes|max:50',
            'q' => 'sometimes|max:50|alpha_dash',
            'id' => 'sometimes|numeric'
        ]);

        if ($request->has('options')) {
            $dtOptions = $request->get('options');
            $opConfig = explode(',', $dtOptions);
            $dtTable = isset($opConfig[0]) ? $opConfig[0] : '';
            $dtKeyCol = isset($opConfig[0]) ? $opConfig[1] : '';
            $dtValCol = isset($opConfig[0]) ? $opConfig[2] : '';

            $data = DB::table($dtTable)
                ->select([DB::raw($dtKeyCol . ' as id'), DB::raw($dtValCol . ' as text')]);

            if ($request->has('id')) {
                $id = $request->get('id');
                $data = $data->where([$dtKeyCol => $id])->get();
                if (isset($data[0]))
                    return response()->json($data[0]);
            } else {
                $query = $request->get('q');
                $data->where($dtValCol, 'LIKE', '%' . $query . '%');
            }

            return response()->json($data->limit(10)->get());

        }
    }

    private function dataViewHandler()
    {
        $this->validateOptions();
        $options = json_decode(json_encode(config('options')), false);
        return view('OptionFramework::data')->with('options', $options);
    }

    public function getIndex(Request $request)
    {
        if ($request->has('data_request')) {
            return $this->dataViewHandler();
        }

        if ($request->has('autocomplete_request')) {
            return $this->autocompleteHandler($request);
        }


        return view('OptionFramework::index', ([
            'baseUrl' => $this->baseUrl,
            'viewPath' => $this->viewPath,
            'dataPath' => $this->dataPath
        ]));
    }


    public function postIndex(Request $request)
    {
        $this->validateOptions();

        $inputs = $request->except('_token');
        $rules = $this->getValidationRules(array_keys($inputs));

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return redirect()->to($this->dataPath)->withErrors($validator);
        }

        foreach ($inputs as $option_name => $option_value) {
            if (in_array($option_name, $this->ids)) {
                if (optionExist($option_name)) {
                    updateOption($option_name, $option_value);
                } else {
                    createOption($option_name, $option_value);
                }
            }
        }
        return response()->json(['success' => true, 'msg' => 'Update Success'], 200);
    }
}
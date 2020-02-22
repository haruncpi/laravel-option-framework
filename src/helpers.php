<?php

use Haruncpi\LaravelOptionFramework\Model\Option;

function getOption($optionName)
{
    if (Option::where('option_name', $optionName)->count()) {
        $data = Option::where('option_name', $optionName)->first();
        return $data->option_value;
    }
}

function optionExist($optionName)
{
    if (Option::where('option_name', $optionName)->count())
        return true;
    else
        return false;
}

function createOption($optionName, $optionValue)
{
    $data = array('option_name' => $optionName, 'option_value' => $optionValue);
    return Option::create($data);
}

function updateOption($optionName, $optionValue)
{
    if (Option::where('option_name', $optionName)->count()) {
        $option = Option::where('option_name', $optionName)->first();
        $option->option_value = $optionValue;
        return $option->update();
    } else {
        return false;
    }
}

function deleteOption($optionName)
{
    if (Option::where('option_name', $optionName)->count()) {
        $option = Option::where('option_name', $optionName)->first();
        return $option->delete();
    } else {
        return false;
    }
}

function isAssoc($arr)
{
    if (is_array($arr)) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    } else {
        return false;
    }
}

function getIcon($field)
{

    if (is_object($field)) {
        if (isset($field->icon)) return $field->icon;

        switch ($field->type) {
            case 'timepicker':
                return 'fa-clock-o';
                break;
            case 'datepicker':
            case 'datetimepicker':
                return 'fa-calendar';
                break;
            case 'colorpicker':
                return 'fa fa-eyedropper';
                break;
            default:
                return config('option-framework.default_icon');
                break;
        }
    }
}
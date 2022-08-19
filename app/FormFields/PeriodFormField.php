<?php

namespace App\FormFields;

use TCG\Voyager\FormFields\AbstractHandler;

class PeriodFormField extends AbstractHandler
{
    protected $codename = 'period';

    public function createContent($row, $dataType, $dataTypeContent, $options)
    {
        return view('formfields.period', [
            'row' => $row,
            'options' => $options,
            'dataType' => $dataType,
            'dataTypeContent' => $dataTypeContent
        ]);
    }
}

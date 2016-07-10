<?php

class ReportsGenerateController extends ModelController
{
    public $listFields = array(
        '.generate.generation_id',
        '.generate.product',
        '.generate.report_name',
        '.generate.created_by'
    );

    public function __construct() 
    {
        parent::__construct('.generate');
        $this->table->addOperation('selective_generation', "Selective Generation");
        $this->table->addOperation('direct_generation', "Direct Generation");
    }
        
    public function add() 
    {
        Application::addJavascript("vendor/mk/reports/src/generate/generator.js"); 
        Application::addJavascript("app/lib/js/common.js");
        $this->label ="New Form Generator";
        
        $reports = unserialize(PROUCTS_REPORTS);
        $form = Element::create('Form')->addAttribute("style", "width:90%");
        $products = Element::create("SelectionList")->setLabel("Product")->setId('product')->setName("product");
        $products->addAttribute("onchange", "updateModules()")->setRequired(true);
        
        foreach ($reports as $key => $report)
        {
            $value = Reporter::addFields($report);
            $reports[$key] = $value;
            $products->addOption($key);
        }

        $form->add(
            Element::create('FieldSet','Report Details')->add(
                Element::create("HiddenField", "report_json")->setId("report_json"),
                Element::create("TextField", "Report Name", "report_name")->setId("report_name"),
                Element::create("TextField", "Main Heading", "heading")->setId("heading"),
                $products,
                Element::create("SelectionList")->setLabel("Module")->setId("module")->setName("module")
                    ->addAttribute("onchange", "updateColumns()")->setRequired(true)
            ),
            Element::create('FieldSet','Sorting & Ordering')->add(
                Element::create("SelectionList")->setLabel("Sort Field")->setId("sort")->setName("sort"),
                Element::create("SelectionList")->setLabel("Order")->setId("order")->setName("order")
                    ->addOption('Ascending', 'ASC')->addOption('Descending', 'DESC'),
                Element::create("TextField")->setLabel("Limit")->setId("limit")->setName("limit")
                    ->addAttribute("style","width:30%"),
                Element::create("TextField","Width","width")->setId('width')->setValue(0)
                    ->addAttribute("readonly", "readonly")->addAttribute("style","width:20%"),
                Element::create("Checkbox", "Numbering", "numbering", "", "1")->setId('number')
                    ->addAttribute("onchange", "autoAdjustWidths()")
            )
        );
        
        $form->setCallback("ReportsGenerateController::saveGenerator", $this);
        
        $data = array(
            'form' => $form->render(),
            "data" => json_encode($reports)
        );
        
        return array(
            "template" => "file://" . SOFTWARE_HOME . "/vendor/mk/reports/src/generate/generator.tpl",
            "data" => $data
        );    
    }
    
    public function save($data)
    {
        $count = count($data) - 1;
        $product = $data[$count];
        $name = $data[$count-1];
        
        unset($data[$count], $data[$count-1]);
        $reportJson = implode("/", $data);
        
        $exist = $this->model->get(array('filter' => 'report_name = ?', 'bind' => [$name]));
        
        if($exist)
        {
            die("Can not save new report with an existing name");
        }

        $this->model->datastore->beginTransaction;
        
        $value['product'] = $product;
        $value['report_json'] = $reportJson;
        is_numeric($name) ? null : $value['report_name'] = $name;
        
        $this->model->setData($value);
        is_numeric($name) ? $this->model->update('generation_id', $name) : $this->model->save();
     
        $this->model->datastore->endTransaction;
        
        $notify = is_numeric($name) ? "Updated Successufully" : "Added Successufully";
        Application::redirect($this->urlPath . "?notification=$notify");
    }
    
    public static function saveGenerator($data, $form, $instance)
    {
        unset($data['width'],$data['numbering']);
        unset($data['heading'],$data['module']);
        unset($data['sort'],$data['order']);
        unset($data['limit']);
        
        $instance->model->datastore->beginTransaction;
        
        $instance->model->setData($data);
        $instance->model->save();
     
        $instance->model->datastore->endTransaction;
        Application::redirect($instance->urlPath . "?notification=Added Successufully");
    }

    public function selective_generation($params)
    {
        Application::redirect(PRODUCT_URL . "selective_guide/selection/$params[0]");
    }
    
    public function direct_generation($params)
    {
        $data = array();
        $model = Model::load(".generate");
        $json = reset($model[$params[0]]);
        $generator = json_decode($json['report_json'],true);
        
        $format =  reset($generator['report_format']) ? reset($generator['report_format']) : 'pdf';
        $orient =  reset($generator['page_orientation']) ? reset($generator['page_orientation']) : 'L';
        $size =  reset($generator['paper_size']) ? reset($generator['paper_size']) : 'A4';
        
        $data['report_format'] = $format;
        $data['page_orientation'] = $orient;
        $data['paper_size'] = $size;
        
        $data['title'] = reset($generator['heading']);
        $data['sub_title'] = reset($generator['sub_heading']);
        $data['hide_sub'] = Common::booleanFormat(reset($generator['hide_sub']));
        $data['repeat_logos'] = Common::booleanFormat(reset($generator['repeat_logos']));
        $data['sort_field'] = reset($generator['sort_field']);
        $data['order'] = reset($generator['order_field']);
        $data['limit'] = reset($generator['limit']);
        $data['numbering'] = Common::booleanFormat(reset($generator['numbering']));
        $data['overall'] = Common::booleanFormat(reset($generator['overall']));
        $data['cell_height'] = reset($generator['cell_height']);
        $data['wrap_replace'] = reset($generator['wrap_replace']);
        $data['wrap_cell'] = Common::booleanFormat(reset($generator['wrap_cell']));
        $data['summarize'] = Common::booleanFormat(reset($generator['summarize']));
        $data['groups_paging'] = Common::booleanFormat(reset($generator['groups_paging']));
        
        $style = array(
            'head_fonts' => Reporter::getStyles('h', $generator),
            'sub_fonts' => Reporter::getStyles('s', $generator),
            'body_fonts' => Reporter::getStyles('b', $generator),
            'grp_fonts' => Reporter::getStyles('g', $generator),
        );
        
        foreach ($style as $key => $value)
        {
            $data[$key] = array(
                'font' => $value[0],
                'size' => $value[1],
                'bottom_margin' => $value[2],
                'bold' => $value[3],
                'italics' => $value[4],
                'underline' => $value[5],
            );
        }
        
        foreach ($generator['fields'] as $key => $field)
        {
            $data["{$key}_group"] = $generator['field_group'][$key];
            $data["{$key}_width"] = $generator['field_widths'][$key];
            $data["{$key}_margin"] = $generator['field_margins'][$key];
            $data["{$key}_total"] = $generator['field_totals'][$key];
            $data["{$key}_fields"] = $generator['fields'][$key];
            $data["{$key}_type"] = $generator['field_types'][$key];
            $data["{$key}_field_names"] = $generator['field_names'][$key];
            $data["{$key}_options"] = $generator['field_filters'][$key];
            $data["{$key}_option_1"] = $generator['field_filter_1'][$key];
            $data["{$key}_option_2"] = $generator['field_filter_2'][$key];
//            $data["{$key}_ex"] = $generator['field_widths'][$key];
        }
        
        $data['field_count'] = count($generator['fields']);
        $data['model'] = reset($generator['model']);
        Reporter::reportGenerator($data);
    }
    
    public function edit($params) 
    {
        $model = Model::load(".generate");
        $json = reset($model[$params[0]]);
        $g = json_decode($json['report_json'],true);
        
        $value = Reporter::addFields([$g['model']]);
        $columns = Element::create("ColumnContainer",4);
        
        foreach ($g['fields'] as $key => $field)
        {
            $field = Element::create("FieldSet")->add(
                Element::create("UniElement")->setTemplate(
                    Element::create("SetupForm", $key, $value[0][1], 
                        $key + 1,
                        $g['fields'][$key],
                        $g['field_names'][$key]
                    )
                )
            );
            $columns->add($field);
        }
        
        $form = Element::create('Form')->add(
            $columns,
            Element::create("FieldSet")->add(
                Element::create("MultiElements")->setTemplate(element::create("SetupForm", 'new', $value[0][1])),
                Element::create("HiddenField","generator")->setValue($params[0])
            )->addAttribute('style', 'width:23%')
        )->setCallback("ReportsGenerateController::updateGenerator", $this);
        
        return $form->render();
    }
    
    public static function updateGenerator($data, $form, $instance)
    {
        $model = Model::load(".generate");
        $json = reset($model[$data['generator']]);
        $gen = json_decode($json['report_json'],true);
        
        unset($gen['field_filters'], $gen['field_filter_1'], $gen['field_filter_2']);
        unset($gen['field_prefixes'], $gen['field_substitutes'], $gen['field_addons']);
        unset($gen['fields'], $gen['field_group'], $gen['field_names'], $gen['field_types']);
        unset($gen['field_totals'], $gen['field_widths'], $gen['field_names'], $gen['field_margins']);
        
        $new = $data['new'];
        $generator = $data['generator'];
        unset($data['new'], $data['generator']);
        $check = Reporter::addFields([$gen['model']]);
        $number = reset($gen['numbering']) === 'true' ? 96 : 100;
        
        foreach ($data as $value)
        {
            if($value['field'])
            {
                $gen['fields'][] = $value['field'];
                $gen['field_names'][] = $value['field_name'];
                $gen['field_types'][] = self::getFieldType($value['field'], $check[0][1]);
            }
        }
        
        foreach ($new as $val)
        {
            if($val['field'])
            {
                $gen['fields'][] = $val['field'];
                $gen['field_names'][] = $val['field_name'];
                $gen['field_types'][] = self::getFieldType($val['field'], $check[0][1]);
            }
        }
        
        for ($i = 0; $i < count($gen['fields']); $i++)
        {
            $gen['field_widths'][$i] = round(($number / count($gen['fields'])),2);
        }
        
        $instance->model->datastore->beginTransaction;
        
        $instance->model->setData(
            array(
                'report_json' => json_encode($gen)
            )
        );
        $instance->model->update('generation_id', $generator);
     
        $instance->model->datastore->endTransaction;
        Application::redirect($instance->urlPath . "?notification=Successufully updated");
    }
    
    public static function getFieldType($field, $data)
    {
        foreach ($data as $key => $value)
        {
            if($key == $field)
            {
                return $value['type'];
            }
        }
    }
}
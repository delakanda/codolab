<?php

class ReportsSelectiveGuideController extends Controller
{
    public function __construct()
    {
        $this->_showInMenu = false;
        $this->label = "Generate";
    }

    public function getContents()
    {
    }
    
    public function selection($params)
    {
        Application::addJavascript("vendor/mk/reports/src/selective_guide/report_generator.js");
        Application::addJavascript("app/lib/js/common.js");
        
        $model = Model::load(".generate");
        $json = reset($model[$params[0]]);
        $generator = json_decode($json['report_json'],true);
        $substitutes = Reporter::addFields([$generator['model']]);

        $h = Reporter::getStyles('h', $generator);
        $s = Reporter::getStyles('s', $generator);
        $b = Reporter::getStyles('b', $generator);
        $g = Reporter::getStyles('g', $generator);
        
        $sort = Element::create("SelectionList")->setLabel("Sort Field")->setName("sort_field");
        $columns = Element::create("ColumnContainer",4);
        
        foreach ($generator['fields'] as $key => $field)
        {
            $subs = Element::create("SelectionList","");
            $subs->setName("{$key}_substitute")->setId("{$key}_substitute");
            foreach ($substitutes[0][1] as $substitute)
            {
                $subs->addOption($substitute['name'], $substitute['name']);
            }
            
            $sort->addOption($generator['field_names'][$key],$field);
            
            $options = Element::create("SelectionList")->setName("{$key}_options");
            $grouping = Element::create("SelectionList","Group","{$key}_group");
            
            $options->setId("{$key}_opt")->addAttribute("onchange", "updateOptionFieldData()");
            $optionField = Reporter::addFieldOptions($options, $generator['field_types'][$key]);
            Reporter::addGroupingOptions($grouping, count($generator['fields']));
            
            $fieldset = Element::create("FieldSet",$generator['field_names'][$key])->add(
                Element::create("ColumnContainer",2)->add(
                    Element::create("FieldSet","Details")->add(
                        Element::create("TextField","Width","{$key}_width")->setValue($generator['field_widths'][$key])
                            ->addAttribute("onchange", "preventives()")->addAttribute("onblur", "preventives()")->setId("{$key}_width"),
                        Element::create("TextField","Margin","{$key}_margin")->setId("{$key}_margin")
                            ->addAttribute("onfocusout", "preventives()")->addAttribute("onchange", "preventives()"),
                        Element::create("SelectionList", "Total","{$key}_total")->setId("{$key}_total")
                            ->addOption("Yes", "true")
                            ->addOption("No", "false")
                            ->setValue($generator['field_totals'][$key])
                            ->addAttribute("onchange", "preventives()"),
                        $grouping->addAttribute("onchange", "updateGroupSelection($key)")
                            ->addAttribute("onfocus", "getGroupValue($key)")
                            ->setValue($generator['field_group'][$key]),
                        Element::create('HiddenField', "{$key}_fields")->setId("{$key}_fields")
                            ->setValue($generator['fields'][$key]),
                        Element::create('HiddenField', "{$key}_type")
                            ->setValue($generator['field_types'][$key])->setId("{$key}_type"),
                        Element::create('HiddenField', "{$key}_field_names")->setId("{$key}_field_names")
                            ->setValue($generator['field_names'][$key])
                    ),
                    Element::create("FieldSet","Options")->add(
                        $options->setValue($generator['field_filters'][$key]),
                        Element::create($optionField)->setName("{$key}_option_1")->setId("{$key}_option_1")
                            ->addAttribute("onchange", "updateOptionFieldData()")->addAttribute("onkeyup", "updateOptionFieldData()")
                            ->addAttribute("onkeydown", "updateOptionFieldData()")->addAttribute("onblur", "updateOptionFieldData()")
                            ->addAttribute("style", "visibility:hidden")->setValue($generator['field_filter_1'][$key]),
                        Element::create($optionField)->setName("{$key}_option_2")->setId("{$key}_option_2")
                            ->addAttribute("onchange", "updateOptionFieldData()")->addAttribute("onkeyup", "updateOptionFieldData()")
                            ->addAttribute("onkeydown", "updateOptionFieldData()")->addAttribute("onblur", "updateOptionFieldData()")
                            ->addAttribute("style", "visibility:hidden")->setValue($generator['field_filter_2'][$key]),
                        Element::create("Checkbox", "Nulls", "{$key}_nulls", "", "true")->setId("{$key}_nulls")
                            ->setValue($generator['field_nulls'][$key])
                    )
                ),
                Element::create("FieldSet","Substitute")->add(
                    $subs->setValue($generator['field_substitutes'][$key])->addAttribute("disabled", "disabled"),
                    Element::create('TextField',"Add-on" ,"{$key}_addon")->setId("{$key}_addon")
                        ->addAttribute("disabled", "disabled")->setValue($generator['field_addons'][$key]),
                    Element::create("Checkbox", "Suffix", "{$key}_prefix", "", "true")->setId("{$key}_prefix")
                        ->setValue($generator['field_prefixes'][$key])->addAttribute("disabled", "disabled")
                )
            );
            
            $columns->add($fieldset);
        }
        
        $format =  reset($generator['report_format']) ? reset($generator['report_format']) : 'pdf';
        $orient =  reset($generator['page_orientation']) ? reset($generator['page_orientation']) : 'L';
        $size =  reset($generator['paper_size']) ? reset($generator['paper_size']) : 'A4';
        
        $form = Element::create('Form')->setCallback("Reporter::reportGenerator", $this);
        $form->setSubmitValue('Generate')->add(
            Element::create("FieldSet","Report Format")->add(
                Element::create("SelectionList", "File Format", "report_format")
                    ->addOption("Hypertext Markup Language (HTML)","html")
                    ->addOption("Portable Document Format (PDF)","pdf")
                    ->addOption("Microsoft Excel (XLS)","xls")
//                    ->addOption("Microsoft Word (DOC)","doc")
                    ->setValue($format)
                    ->setId('report_format')
                    ->setRequired(true),
                Element::create("SelectionList", "Page Orientation", "page_orientation")
                    ->addOption("Landscape", "L")
                    ->addOption("Portrait", "P")
                    ->setValue($orient)
                    ->setId('page_orientation'),
                Element::create("SelectionList", "Paper Size", "paper_size")
                    ->addOption("A4", "A4")
                    ->addOption("A3", "A3")
                    ->setValue($size)
                    ->setId('paper_size')
            )->addAttribute("style","width:50%"),
            Element::create("ColumnContainer",2)->add(
                Element::create("FieldSet","Report Details")->add(
                    Element::create("ColumnContainer",2)->add(
                        Element::create("FieldSet","Title")->add(
                            Element::create("TextField","Title","title")->setValue(reset($generator['heading']))->setId('heading'),
                            Element::create("TextArea","Subtitle","sub_title")->setId('sub_heading')->addAttribute("style", "font_size:8px")
                                ->addAttribute("onchange", "getGroupingDetails()")->addAttribute("onkeyup", "getGroupingDetails()")
                                ->addAttribute("onkeydown", "getGroupingDetails()")->addAttribute("onfocus", "getGroupingDetails()"),
                            Element::create("Checkbox", "Hide subtitle", "hide_sub", "", "true")->setValue(reset($generator['hide_sub']))
                                ->setId('hider')->addAttribute("onchange", "updateSubtitle()"),
                            Element::create("Checkbox", "Group Paging", "groups_paging", "", "true")->setId('groups_paging')
                                ->addAttribute("onchange", "updateGrouping()")->setValue(reset($generator['groups_paging'])),
                            Element::create("Checkbox", "Repeat Logos", "repeat_logos", "", "true")
                                ->setId('repeat_logos')->setValue(reset($generator['repeat_logos'])),
                            Element::create("Checkbox", "Summarize", "summarize", "", "true")
                                ->setId('summarize')->setValue(reset($generator['summarize']))
                        ),
                        Element::create("FieldSet","Content")->add(
                            $sort->setId('sort_field')->setValue(reset($generator['sort_field'])),
                            Element::create("SelectionList")->setLabel("Order")->setName("order")
                                ->setId('order')->setValue(reset($generator['order_field']))
                                ->addOption('Ascending', 'ASC')->addOption('Descending', 'DESC'),
                            Element::create("TextField","Limit","limit")->setId('limit')
                                ->setValue(reset($generator['limit']))
                                ->addAttribute("style","width:30%"),
                            Element::create("TextField","Width","width")->setId('width')
                                ->addAttribute("readonly", "readonly")->addAttribute("style","width:20%"),
                            Element::create("Checkbox", "Numbering", "numbering", "", "true")->setId('number')
                                ->addAttribute("onchange", "updateWidth()")->setValue(reset($generator['numbering'])),
                            Element::create("Checkbox", "Overall Total", "overall", "", "true")
                                ->setValue(reset($generator['overall']))->setId('overall'),
                            Element::create("Checkbox", "Wrap Cells", "wrap_cell", "", "true")->setId('wrap_cell')
                                ->addAttribute("onchange", "updateWrap()")->setValue(reset($generator['wrap_cell'])),
                            Element::create("TextField", "Cell Height", "cell_height")->setId('cell_height')
                                ->addAttribute("style","width:30%")->setValue(reset($generator['cell_height'])),
                            Element::create("SelectionList", "Wrapper", "wrap_replace")
                                ->addOption(" ", " ")
                                ->addOption("-", "-")
                                ->setValue(reset($generator['wrap_replace']))
                        )
                    )
                ),
                Element::create("FieldSet","Report Fonts")->add(
                    Element::create("ColumnContainer",4)->add(
                        Element::create("FieldSet","Heading")->add(
                            Element::create("UniElement")->setTemplate(Element::create("FontForm","head_fonts", $h[0], $h[1], $h[2], $h[3], $h[4], $h[5]))
                        ),
                        Element::create("FieldSet","Sub-heading")->add(
                            Element::create("UniElement")->setTemplate(Element::create("FontForm","sub_fonts", $s[0], $s[1], $s[2], $s[3], $s[4], $s[5]))
                        ),
                        Element::create("FieldSet","Body")->add(
                            Element::create("UniElement")->setTemplate(Element::create("FontForm","body_fonts", $b[0], $b[1], $b[2], $b[3], $b[4], $b[5]))
                        ),
                        Element::create("FieldSet","Grouping Headings")->add(
                            Element::create("UniElement")->setTemplate(Element::create("FontForm","grp_fonts", $g[0], $g[1], $g[2], $g[3], $g[4], $g[5]))
                        )
                    )
                )
            ),
            Element::create("FieldSet","Fields")->add(
                $columns
            ),
            Element::create('HiddenField','report_json')->setId(report_json),
            Element::create('HiddenField','json')->setValue($params[0])->setId('id'),
            Element::create('HiddenField', 'field_count')->setValue(count($generator['fields'])),
            Element::create('HiddenField', 'product')->setValue($json['product'])->setId('product'),
            Element::create('HiddenField', 'name')->setId('name')->setValue(reset($generator['name'])),
            Element::create('HiddenField', 'model')->setId('model')->setValue(reset($generator['model']))
        );
        
        $form->addAttribute("target", "blank");
        
        $data = array(
            'form' => $form->render(),
            'actual' => $h[2] ? $h[2] : 3,
            'key' => count($generator['fields'])
        );
        
        return array(
            "template" => "file://" . SOFTWARE_HOME . "/vendor/mk/reports/src/selective_guide/report_generator.tpl",
            "data" => $data
        );   
    }
}

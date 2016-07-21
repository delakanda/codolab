<?php

abstract class FilteredModelController extends ModelController
{
    protected $selectionLists = array();
    protected $filterFieldModel;

    abstract protected function addSelectionListToolbar();
    
    protected function setupListView()
    {
        parent::setupListView();
        $this->selectionLists = $this->addSelectionListToolbar();
        
        foreach ($this->selectionLists as $list)
        {
            $selectionList = Element::create("SelectionListToolbar", "{$list['filter_label']}");
            $this->addListItems($selectionList, $list);
            
            $this->filterFieldModel = $this->model;
            $selectionList->onchange = "wyf.updateFilter('{$this->listView->table->name}', '{$this->filterFieldModel->database}.{$list['filter_field']}', this.value)";
            $this->listView->addToolbarItem($selectionList);
        }
    }

    public function getContents()
    {
        $ret = parent::getContents();
        foreach ($this->selectionLists as $list)
        {
            $ret .= "<script type='text/javascript'>
                wyf.updateFilter('{$this->listView->table->name}', '{$this->filterFieldModel->database}.{$list['filter_field']}', '{$list['default_value']}');
                {$this->listView->table->name}Search();
            </script>";
        }
        return $ret;
    }
    
    protected function addListItems($selectionList, $list)
    {
        $selectionList->hasGroups = $list['has_groups'];
        
        foreach($list['list'] as $option)
        {
            $selectionList->add($option['item'], $option['value'], $option['group']);
        }
    }
    
    protected function addOption($item, $value, $group = null)
    {
        return [
            'group' => $group,
            'value' => $value,
            'item' => $item
        ];
    }
    
    protected function addSelectionList($filterField, $defaultValue, $filterLabel, $list, $hasGroups = false)
    {
        return [
            'default_value' => $defaultValue,
            'filter_field' =>  $filterField,
            'filter_label' =>  $filterLabel,
            'has_groups' => $hasGroups,
            'list' => $list
        ];
    }
    
}

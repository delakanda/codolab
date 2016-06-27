<?php

class DashboardController extends Controller
{
    public $list = array();
    
    public function __construct()
    {
        $this->label = "Dashboard";
        $this->_showInMenu = "false";
    }
    
    public function getPermissions()
    {
        $permissions = array(
           array("name"=>"can_log_in_to_web", "label"=>"Can log into the dashboard")
        );
        
        $widgets = $this->getWidgetsList();
        return array_merge($permissions, $widgets);
    }
    
    public function getContents()
    {
        if(Application::$config['no_dashboard'] || $_SESSION["role_id"]=="") 
        {
            return;
        }
    	
        $permissionsModel = Model::load("system.permissions");
        $permissions = $permissionsModel->get(
            array(
                "conditions"    =>  "module = '/dashboard' AND permissions.role_id='{$_SESSION["role_id"]}'"
            ),
            Model::MODE_ASSOC,
            false, false
        );
        
        $widgets = array();
        foreach($permissions as $permission)
        {
            if($permission["permission"] == "system/reminders" || $permission["permission"] == "can_log_in_to_web") 
            {
                continue;
            }
            
            if($permission['value'] == '1')
            {
                add_include_path("app/modules/{$permission["permission"]}");
                
                //adding include path for relocatables
                $redirect_str = explode("/", $permission["permission"]);
                $count_str = "";
                
                foreach($redirect_str as $key => $str)
                {
                    if ($key === 0) continue;
                    $count_str .= "/" . $str; 
                }
                add_include_path("vendor/mk/{$redirect_str[0]}/src{$count_str}");
                
                
                $widgetClass =  Application::camelize($permission["permission"], "/")."Widget";
                
                $widget = new $widgetClass();
                                
                if($widget->order > 0)
                {
                    $widgets[$widget->order] = $widget;
                }
                else
                {
                    $widgets[] = $widget;
                }
            }
        }
        
        $widgets[] = new SystemRemindersWidget();

        $layout = array();
        $k = 0;
        
        foreach($widgets as $i => $widget)
        {
            $widget = Widget::wrap($widget);
            if($widget !== false)
            {
                $layout[$k % 2] .= $widget;
                $k++;
            }
        }
        $ret = "<div id='widgets-wrapper'><div id='widgets-left'>{$layout[1]}</div><div id='widgets-right'>{$layout[0]}</div></div>";
        return $ret;
        
    }
    
    public function getWidgetsList($path = "", $prefix = "app/modules")
    {
        
        $d = dir($prefix . $path);
        $redirect = false;
      
        while (false !== ($entry = $d->read()))
        {      
            if($entry != "." && $entry != ".." && is_dir("$prefix$path/$entry"))
            {   
                //if it is a relocatable recall function with a new prefix
                if(file_exists("$prefix$path/$entry/" . "package_redirect.php"))
                {      
                    include "$prefix$path/$entry/package_redirect.php";
                    $new_prefix = $redirect_path;
                    $this->getWidgetsList("", $new_prefix);
                }
                    
                //set redirect flag to identify relocatable 
                $redirect = $prefix !== "app/modules" ? true : $redirect;
                $this->getDirWidgets($prefix, $path, $entry, $redirect);
                $this->getWidgetsList("$path/$entry", $prefix);
            }  
        }
       
        return $this->list;  
    }
    
    public function getDirWidgets($prefix, $path, $entry, $redirect)
    {
        $exist = false;
        $dirPath =  "$prefix$path/$entry";
        $name = substr("$path/$entry", 1);
        $module = preg_split("/\/+/", $prefix);
        $widgetClass = Application::camelize("$path/$entry", "/") . "Widget";
        
        if($redirect && file_exists("$dirPath/" . Application::camelize("$module[2]/$path/$entry", "/") . "Widget.php"))
        {
            $exist = true;
            $name = substr("$module[2]$path/$entry", 0);
            $widgetClass = Application::camelize("$module[2]/$path/$entry", "/") . "Widget";
        }
        
        else if($redirect && file_exists("$dirPath/" . Application::camelize("{$module[count($module)-1]}/$path/$entry", "/") . "Widget.php"))
        {
            $exist = true;
            $name = substr("{$module[count($module)-1]}$path/$entry", 0);
            $widgetClass = Application::camelize("{$module[count($module)-1]}/$path/$entry", "/") . "Widget";
        }
        
        if($exist)
        {
            add_include_path($dirPath);
            $widget = new $widgetClass();     
            $this->list[] = array(
                "label" =>  "{$widget->label} Widget",
                "name"  =>  $name
            );
        }
    }
}

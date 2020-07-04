<?php

namespace Sagartakle\Laracrud\Helpers;

use DB;
use Log;

use Sagartakle\Laracrud\Models\Menu;
use Sagartakle\Laracrud\Models\Page;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Upload;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Collective\Html\FormFacade;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

/**
 * Class CustomHelper
 * @package App\Helpers
 *
 * This is  Helper class contains methods required for Admin Panel functionality.
 */
class CustomHelper
{
    /**
     * Gives various names of Module in Object like label, table, model, controller, singular
     *
     * $names = CustomHelper::generateModuleNames($module_name);
     *
     * @param $module_name module name
     * @param $icon module icon in FontAwesome
     * @return object
     */
    public static function generateModuleNames($module_name, $icon)
    {
        $array = array();
        $module_name = trim($module_name);
        $module_name = str_replace(" ", "_", $module_name);
        
        $array['module'] = ucfirst(\Str::plural($module_name));
        $array['label'] = ucfirst(\Str::plural(preg_replace('/[A-Z]/', ' $0', $module_name)));
        $array['table'] = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $module_name)), '_'));
        $array['model'] = ucfirst(\Str::singular($module_name));
        $array['fa_icon'] = $icon;
        $array['controller'] = $array['module'] . "Controller";
        $array['singular_l'] = strtolower(\Str::singular($module_name));
        $array['singular_c'] = ucfirst(\Str::singular($module_name));
        
        return (object)$array;
    }
    
    /**
     * Get list of Database tables excluding  Context tables like
     * backups, configs, menus, migrations, modules, module_fields, module_field_types
     * password_resets, permissions, permission_role, role_module, role_module_fields, role_user
     *
     * Method currently supports MySQL and SQLite databases
     *
     * You can exclude additional tables by $$remove_tables
     *
     * $tables = CustomHelper::getDBTables([]);
     *
     * @param array $remove_tables exclude additional tables
     * @return array
     */
    public static function getDBTables($remove_tables = [])
    {
        if(env('DB_CONNECTION') == "sqlite") {
            $tables_sqlite = DB::select('select * from sqlite_master where type="table"');
            $tables = array();
            foreach($tables_sqlite as $table) {
                if($table->tbl_name != 'sqlite_sequence') {
                    $tables[] = $table->tbl_name;
                }
            }
        } else if(env('DB_CONNECTION') == "pgsql") {
            $tables_pgsql = DB::select("SELECT table_name FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema = 'public' ORDER BY table_name;");
            $tables = array();
            foreach($tables_pgsql as $table) {
                $tables[] = $table->table_name;
            }
        } else if(env('DB_CONNECTION') == "mysql") {
            $tables = DB::select('SHOW TABLES');
        } else {
            $tables = DB::select('SHOW TABLES');
        }
        
        $tables_out = array();
        foreach($tables as $table) {
            $table = (Array)$table;
            $tables_out[] = array_values($table)[0];
        }
        if(in_array(-1, $remove_tables)) {
            $remove_tables2 = array();
        } else {
            $remove_tables2 = array(
                'backups',
                'configs',
                'menus',
                'migrations',
                'modules',
                'module_fields',
                'module_field_types',
                'password_resets',
                'permissions',
                'permission_role',
                'role_module',
                'role_module_fields',
                'role_user'
            );
        }
        $remove_tables = array_merge($remove_tables, $remove_tables2);
        $remove_tables = array_unique($remove_tables);
        $tables_out = array_diff($tables_out, $remove_tables);
        
        $tables_out2 = array();
        foreach($tables_out as $table) {
            $tables_out2[$table] = $table;
        }
        
        return $tables_out2;
    }
    
    /**
     * Get Array of All Modules
     *
     * $modules = CustomHelper::getModuleNames([]);
     *
     * @param array $remove_modules to exclude certain modules.
     * @return array Array of Modules
     */
    public static function getModuleNames($remove_modules = [])
    {
        $modules = Module::all();
        
        $modules_out = array();
        foreach($modules as $module) {
            $modules_out[] = $module->name;
        }
        $modules_out = array_diff($modules_out, $remove_modules);
        
        $modules_out2 = array();
        foreach($modules_out as $module) {
            $modules_out2[$module] = $module;
        }
        
        return $modules_out2;
    }
    
    /**
     * Method to parse the dropdown, Multiselect, Taginput and radio values which are linked with
     * either other tables via "@" e.g. "@employees" or string array of values
     *
     * This function parse the either case and gives output in html labels.
     * Used only in show.blade.php of modules
     *
     * CustomHelper::parseValues($field['json_values']);
     *
     * @param $value value source for column e.g. @employees / ["Marvel","Universal"]
     * @return string html labeled values
     */
    public static function parseValues($value)
    {
        // return $value;
        $valueOut = "";
        if(strpos($value, '[') !== false) {
            $arr = json_decode($value);
            foreach($arr as $key) {
                $valueOut .= "<div class='label label-primary'>" . $key . "</div> ";
            }
        } else if(strpos($value, ',') !== false) {
            $arr = array_map('trim', explode(",", $value));
            foreach($arr as $key) {
                $valueOut .= "<div class='label label-primary'>" . $key . "</div> ";
            }
        } else if(strpos($value, '@') !== false) {
            $valueOut .= "<b data-toggle='tooltip' data-placement='top' title='From " . str_replace("@", "", $value) . " table' class='text-primary'>" . $value . "</b>";
        } else if($value == "") {
            $valueOut .= "";
        } else {
            $valueOut = "<div class='label label-primary'>" . $value . "</div> ";
        }
        return $valueOut;
    }
    
    /**
     * Log method to log either in command line or in Log file depending on $type.
     *
     * CustomHelper::log("info", "", $commandObject);
     *
     * @param $type where to put log - error / info / debug
     * @param $text text to put in log
     * @param $commandObject command object if log is to be put on commandline
     */
    public static function log($type, $text, $commandObject)
    {
        if($commandObject) {
            $commandObject->$type($text);
        } else {
            if($type == "line") {
                $type = "info";
            }
            Log::$type($text);
        }
    }
    
    /**
     * Method copies folder recursively into another
     *
     * CustomHelper::recurse_copy("", "");
     *
     * @param $src source folder
     * @param $dst destination folder
     */
    public static function recurse_copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst, 0777, true);
        while(false !== ($file = readdir($dir))) {
            if(($file != '.') && ($file != '..')) {
                if(is_dir($src . '/' . $file)) {
                    self::recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    // ignore files
                    if(!in_array($file, [".DS_Store"])) {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }
    
    /**
     * Method deletes folder and its content
     *
     * CustomHelper::recurse_delete("");
     *
     * @param $dir directory name
     */
    public static function recurse_delete($dir)
    {
        if(is_dir($dir)) {
            $objects = scandir($dir);
            foreach($objects as $object) {
                if($object != "." && $object != "..") {
                    if(is_dir($dir . "/" . $object))
                        self::recurse_delete($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            rmdir($dir);
        }
    }
    
    /**
     * Generate Random Password
     *
     * $password = CustomHelper::gen_password();
     *
     * @param int $chars_min minimum characters
     * @param int $chars_max maximum characters
     * @param bool $use_upper_case allowed uppercase characters
     * @param bool $include_numbers includes numbers or not
     * @param bool $include_special_chars include special charactors or not
     * @return string random password according to configuration
     */
    public static function gen_password($chars_min = 6, $chars_max = 8, $use_upper_case = false, $include_numbers = false, $include_special_chars = false)
    {
        $length = rand($chars_min, $chars_max);
        $selection = 'aeuoyibcdfghjklmnpqrstvwxz';
        if($include_numbers) {
            $selection .= "1234567890";
        }
        if($include_special_chars) {
            $selection .= "!@\"#$%&[]{}?|";
        }
        $password = "";
        for($i = 0; $i < $length; $i++) {
            $current_letter = $use_upper_case ? (rand(0, 1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];
            $password .= $current_letter;
        }
        return $password;
    }
    
    /**
     * Get url of image by using $upload_id
     *
     * CustomHelper::img($upload_id);
     *
     * @param $upload_id upload id of image / file
     * @return string file / image url
     */
    public static function img($upload_id, $size = "")
    {
        $upload = \App\Models\Upload::find($upload_id);
        if(isset($size) && $size != "") {
            $size = "?s=".$size;
        }
        if(isset($upload->id)) {
            return url("files/" .$upload->hash . "/" . $upload->name).$size;
        } else {
            return asset("public/assets/images/no-image-available.jpg");
        }
    }
    
    /**
     * Get Thumbnail image path of Uploaded image
     *
     * CustomHelper::createThumbnail($filepath, $thumbpath, $thumbnail_width, $thumbnail_height);
     *
     * @param $filepath file path
     * @param $thumbpath thumbnail path
     * @param $thumbnail_width thumbnail width
     * @param $thumbnail_height thumbnail height
     * @param bool $background background color - default transparent
     * @return bool/string Returns Thumbnail path
     */
    public static function createThumbnail($filepath, $thumbpath, $thumbnail_width, $thumbnail_height, $background = false)
    {
        list($original_width, $original_height, $original_type) = getimagesize($filepath);
        if($original_width > $original_height) {
            $new_width = $thumbnail_width;
            $new_height = intval($original_height * $new_width / $original_width);
        } else {
            $new_height = $thumbnail_height;
            $new_width = intval($original_width * $new_height / $original_height);
        }
        $dest_x = intval(($thumbnail_width - $new_width) / 2);
        $dest_y = intval(($thumbnail_height - $new_height) / 2);
        if($original_type === 1) {
            $imgt = "ImageGIF";
            $imgcreatefrom = "ImageCreateFromGIF";
        } else if($original_type === 2) {
            $imgt = "ImageJPEG";
            $imgcreatefrom = "ImageCreateFromJPEG";
        } else if($original_type === 3) {
            $imgt = "ImagePNG";
            $imgcreatefrom = "ImageCreateFromPNG";
        } else {
            return false;
        }
        $old_image = $imgcreatefrom($filepath);
        $new_image = imagecreatetruecolor($thumbnail_width, $thumbnail_height); // creates new image, but with a black background
        // figuring out the color for the background
        if(is_array($background) && count($background) === 3) {
            list($red, $green, $blue) = $background;
            $color = imagecolorallocate($new_image, $red, $green, $blue);
            imagefill($new_image, 0, 0, $color);
            // apply transparent background only if is a png image
        } else if($background === 'transparent' && $original_type === 3) {
            imagesavealpha($new_image, TRUE);
            $color = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
            imagefill($new_image, 0, 0, $color);
        }
        imagecopyresampled($new_image, $old_image, $dest_x, $dest_y, 0, 0, $new_width, $new_height, $original_width, $original_height);
        $imgt($new_image, $thumbpath);
        return file_exists($thumbpath);
    }
    
    /**
     * Print the menu editor view.
     * This needs to be done recursively
     *
     * CustomHelper::print_menu_editor($menu)
     *
     * @param $menu menu array from database
     * @return string menu editor html string
     */
    public static function print_menu_editor($menu)
    {
        $editing = \Collective\Html\FormFacade::open(['route' => [config('stlc.route_prefix') . '.menus.destroy', $menu->id], 'method' => 'delete', 'style' => 'display:inline']);
        $editing .= '<button class="btn btn-xs btn-danger pull-right"><i class="fa fa-times"></i></button>';
        $editing .= \Collective\Html\FormFacade::close();
        if($menu->type != "module") {
            $info = (object)array();
            $info->id = $menu->id;
            $info->name = $menu->name;
            $info->url = $menu->url;
            $info->type = $menu->type;
            $info->icon = $menu->icon;
            
            $editing .= '<a class="editMenuBtn btn btn-xs btn-success pull-right" info=\'' . json_encode($info) . '\'><i class="fa fa-edit"></i></a>';
        }
        $str = '<li class="dd-item dd3-item" data-id="' . $menu->id . '">
			<div class="dd-handle dd3-handle"></div>
			<div class="dd3-content"><i class="fa ' . $menu->icon . '"></i> ' . $menu->name . ' ' . $editing . '</div>';
        
        $childrens = \App\Models\Menu::where("parent", $menu->id)->orderBy('hierarchy', 'asc')->get();
        
        if(count($childrens) > 0) {
            $str .= '<ol class="dd-list">';
            foreach($childrens as $children) {
                $str .= self::print_menu_editor($children);
            }
            $str .= '</ol>';
        }
        $str .= '</li>';
        return $str;
    }
    
    /**
     * Print the sidebar menu view.
     * This needs to be done recursively
     *
     * CustomHelper::print_menu($menu)
     *
     * @param $menu menu array from database
     * @return string menu in html string
     */
    public static function print_menu(Menu $menu, $prefix = null,$checkAccess = false)
    {
        if(!isset($prefix)) {
            $prefix_url = config("stlc.route_prefix",'admin').'/';
        } else if(isset($prefix) && ($prefix == "no_prefix" || $prefix == "")) {
            $prefix_url = "";
        } else {
            $prefix_url = $prefix.'/';
        }
        $childrens = $menu->childrensMenu;

        $treeview = "";
        $str = "";
        $subviewSign = "";
        if(count($childrens)) {
            $treeview = " class=\"treeview\"";
            $subviewSign = '<i class="fa fa-angle-left pull-right"></i>';
        }
        
        if(count($childrens)) {
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    if($menu->link == "#") {
                        $str = '<li' . $treeview . '><a href="javascript:void(0)"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                    } else {
                        if(isset($children->name) && in_array($children->name,config("stlc.devloper_modules"))) {
                            $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.stlc_route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                        } else {
                            $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                        }
                    }
                } else {
                    if($children->type == 'page') {
                        $module = Page::where('name',$children->name)->first();
                    } else if($children->type == 'module') {
                        $mkmodule = Module::where('name',$children->name)->first();
                        if(isset($mkmodule) && $mkmodule->name == $children->name) {
                            $module = $mkmodule->name;
                        }
                    }
                    if(isset($module) && Module::hasAccess($module)) {
                        if(isset($children->name) && in_array($children->name,config("stlc.devloper_modules"))) {
                            $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.stlc_route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                        } else {
                            $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                        }
                    }
                }
            }
        } else {
            if(isset($menu->name) && in_array($menu->name,config("stlc.devloper_modules"))) {
                $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.stlc_route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
            } else {
                $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("stlc.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
            }
        }
        
        if(count($childrens)) {
            $str .= '<ul class="treeview-menu">';
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    $str .= self::print_menu($children,$prefix,$checkAccess);
                } else {
                    if($children->type == 'page') {
                        $module = Page::where('name',$children->name)->first();
                    } else if($children->type == 'module') {
                        $mkmodule = Module::where('name',$children->name)->first();
                        if(isset($mkmodule) && $mkmodule->name == $children->name) {
                            $module = $mkmodule->name;
                        } else {
                            $module = collect();
                        }
                    }
                    if(isset($module) && (Module::hasAccess($module) || $checkAccess)) {
                        $str .= self::print_menu($children,$prefix,$checkAccess);
                    }
                }
            }
            $str .= '</ul>';
        }
        $str .= '</li>';
        return $str;
    }
    
    /**
     * Print the top navbar menu view.
     * This needs to be done recursively
     *
     * CustomHelper::print_menu_topnav($menu)
     *
     * @param $menu menu array from database
     * @param bool $active is this menu active or not
     * @return string menu in html string
     */
    public static function print_menu_topnav($menu, $active = false)
    {
        $childrens = \App\Models\Menu::where("parent", $menu->id)->orderBy('hierarchy', 'asc')->get();
        
        $treeview = "";
        $treeview2 = "";
        $subviewSign = "";
        if(count($childrens)) {
            $treeview = " class=\"dropdown\"";
            $treeview2 = " class=\"dropdown-toggle\" data-toggle=\"dropdown\"";
            $subviewSign = ' <span class="caret"></span>';
        }
        $active_str = '';
        if($active) {
            $active_str = 'class="active"';
        }
        
        $str = '<li ' . $treeview . '' . $active_str . '><a ' . $treeview2 . ' href="' . url(config("stlc.route_prefix") . '/' . $menu->url) . '">' . $menu->label . $subviewSign . '</a>';
        
        if(count($childrens)) {
            $str .= '<ul class="dropdown-menu" role="menu">';
            foreach($childrens as $children) {
                $str .= self::print_menu_topnav($children);
            }
            $str .= '</ul>';
        }
        $str .= '</li>';
        return $str;
    }
    
    /**
     * delete and regenarate menu links
     * CustomHelper::generateMenu();
     *
     */
    public static function generateMenu()
    {
		// Generating Module Menus
		if(Schema::hasTable('modules')) {
			$modules = Module::all();
		} else {
			$modules = [];
		}
		if(Schema::hasTable('menus')) {
            Menu::truncate();
			$dashboardMenu = Menu::create([
				"name" => "Dashboard","label" => 'My Dashboard',
				"link" => "dashboard","icon" => "fa-dashboard","type" => 'custom'
			]);
			$profileMenu = Menu::create([
				"name" => "Profile","label" => 'Profile',"link" => "#",
				"icon" => "fa-group","type" => 'custom',"hierarchy" => 1
			]);
			$usersMenu = Menu::create([
				"name" => "user","label" => 'Users',"link" => "#",
				"icon" => "fa-users","type" => 'custom',"hierarchy" => 1
			]);
			$ProgramsMenu = Menu::create([
				"name" => "Programs","label" => 'Programs',"link" => "#",
				"icon" => "fa-tasks","type" => 'custom',"hierarchy" => 1
            ]);
			// $AssessmentMenu = Menu::create([
			// 	"name" => "Assessments","label" => 'Assessments',"link" => "#",
			// 	"icon" => "fa-check-square-o","type" => 'custom',"hierarchy" => 1
            // ]);
			$SettingsMenu = Menu::create([
				"name" => "Setting","label" => 'Settings',"link" => "#",
				"icon" => "fa-cogs","type" => 'custom',"hierarchy" => 1
            ]);
		}
		foreach ($modules as $module) {
			$parent = Null;
			if(!in_array($module->name, config('lara.crud.restrictedModules.menu',[]))) {
                $label = $module->label;
                if($module->name == 'MasterUsers') {
                    $label = "Users";
                }  
				if(in_array($module->name, ["Roles","Employees"])) {
                    $parent = $profileMenu->id;
				} else if(in_array($module->name, ['MasterUsers','UserRewards','UserScores','UserSettings'])) {
					$parent = $usersMenu->id;
				} else if(in_array($module->name, ['WorkAreas','WorkAreasTopics','NewsAndMedias','Testimonials','Blogs','MediaAndNews'])) {
					$parent = $ProgramsMenu->id;
				// } else if(in_array($module->name, ['Questions','Answers'])) {
				// 	$parent = $AssessmentMenu->id;
				} else if(in_array($module->name, ['Faqs','Enquiries','PageContents','Messages','Taxes','Coupons','Rewards','Categories'])) {
					$parent = $SettingsMenu->id;
				}
				if(Schema::hasTable('menus')) {
					Menu::create([
						"name" => $module->name,
						"label" => $label,
						"link" => $module->table_name,
						"icon" => $module->icon,
						"parent" => $parent
					]);
				}
			}
		}

		if(Schema::hasTable('menus')) {
			$ranking = ['Dashboard','Profile','Settings'];
			$menus = Menu::all();
			$count = count($ranking);
			foreach($menus as $menu) {
				if(in_array($menu->name,$ranking)) {
					$menu->rank = array_search($menu->name, $ranking);
				} else {
					$menu->rank = ++$count;
				}
				$menu->save();
			}
		}
    }

    /**
     * Get laravel version. very important in installation and handling Laravel 5.3 changes.
     *
     * CustomHelper::laravel_ver()
     *
     * @return float|string laravel version
     */
    public static function laravel_ver()
    {
        $var = \App::VERSION();
        
        if(\Str::startsWith($var, "5.2")) {
            return 5.2;
        } else if(\Str::startsWith($var, "5.3")) {
            return 5.3;
        } else if(substr_count($var, ".") == 3) {
            $var = substr($var, 0, strrpos($var, "."));
            return $var . "-str";
        } else {
            return floatval($var);
        }
    }
    
    /**
     * Get real Module name by replacing underscores within name
     *
     * @param $name Module Name with whitespace filled by underscores
     * @return mixed return Module Name
     */
    public static function real_module_name($name)
    {
        $name = preg_replace('/(?<!\ )[A-Z]/', ' $0', $name);
        $name = preg_replace('/[^A-Za-z0-9\-]/', ' ', $name);
        // $name = str_replace('/[?-_]/', ' ', $name);
        return $name;
    }
    
    /**
     * Get complete line within file by comparing passed substring $str
     *
     * CustomHelper::getLineWithString()
     *
     * @param $fileName file name to be scanned
     * @param $str substring to be checked for line match
     * @return int/string return -1 if failed to find otherwise complete line in string format
     */
    public static function getLineWithString($fileName, $str)
    {
        $lines = file($fileName);
        foreach($lines as $lineNumber => $line) {
            if(strpos($line, $str) !== false) {
                return $line;
            }
        }
        return -1;
    }
    
    /**
     * Get complete line within given file contents by comparing passed substring $str
     *
     * CustomHelper::getLineWithString2()
     *
     * @param $content content to be scanned
     * @param $str substring to be checked for line match
     * @return int/string return -1 if failed to find otherwise complete line in string format
     */
    public static function getLineWithString2($content, $str)
    {
        $lines = explode(PHP_EOL, $content);
        foreach($lines as $lineNumber => $line) {
            if(strpos($line, $str) !== false) {
                return $line;
            }
        }
        return -1;
    }
    
    /**
     * Method sets parameter in ".env" file as well as into php environment.
     *
     * CustomHelper::setenv("CACHE_DRIVER", "array");
     *
     * @param $param parameter name
     * @param $value parameter value
     */
    public static function setenv($param, $value)
    {
        
        $envfile = self::openFile('.env');
        $line = self::getLineWithString('.env', $param . '=');
        $envfile = str_replace($line, $param . "=" . $value . "\n", $envfile);
        file_put_contents('.env', $envfile);
        
        $_ENV[$param] = $value;
        putenv($param . "=" . $value);
    }
    
    /**
     * Get file contents
     *
     * @param $from file path
     * @return string file contents in String
     */
    public static function openFile($from)
    {
        $md = file_get_contents($from);
        return $md;
    }
    
    /**
     * Delete file
     *
     * CustomHelper::deleteFile();
     *
     * @param $file_path file's path to be deleted
     */
    public static function deleteFile($file_path)
    {
        if(file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    /**
     * Get Migration file name by passing matching table name
     *
     * CustomHelper::get_migration_file("students_table");
     *
     * @param $file_name matching table name like 'create_employees_table'
     * @return string returns migration file name if found else blank string
     */
    public static function get_migration_file($file_name)
    {
        $mfiles = scandir(base_path('database/migrations/'));
        foreach($mfiles as $mfile) {
            if(\Str::contains($mfile, $file_name)) {
                $mgr_file = base_path('database/migrations/' . $mfile);
                if(file_exists($mgr_file)) {
                    return 'database/migrations/' . $mfile;
                }
            }
        }
        return "";
    }
    
    /**
     * Check if passed array is associative
     *
     * @param array $array array to be checked associative or not
     * @return bool true if associative
     */
    public static function is_assoc_array(array $array)
    {
        // Keys of the array
        $keys = array_keys($array);
        
        // If the array keys of the keys match the keys, then the array must
        // not be associative (e.g. the keys array looked like {0:0, 1:1...}).
        return array_keys($keys) !== $keys;
    }
    
    /**
     * arrya splice kay
     *
     * @param array $array array
     * @return array
     */
    public static function array_splice_key(array $old_array, $first_index, $second_index,array $push_array)
    {
        return array_slice($old_array, $first_index, $second_index, true) +
                $push_array +
                array_slice($old_array, $second_index, NULL, true);
    }

    /**
     * date change formate
     * 
     * CustomHelper::date_format($date);
     *
     * @param array date string
     * @return formatedDate
     */
    public static function date_format($date, $type = "show")
    {
        if(isset($date) && $date != "") {
            if($type == "show") {
                return date("M d, Y", strtotime($date));
            } else if($type == "field_show") {
                return date("d-m-Y", strtotime($date));
            } else if($type == "field_show_with_time") {
                return date("M d, Y h:i A", strtotime($date));
            } else if($type == "data_save_simpel") {
                return date("Y-m-d", strtotime($date));
            } else if($type == "data_save_simpel_with_time") {
                return date("Y-m-d H:i:s", strtotime($date));
            } else if($type == "data_save") {
                return \Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } else if($type == "data_save_with_time") {
                return \Carbon::createFromFormat('d/m/Y H:i:s', $date)->format('Y-m-d H:i:s');
            } else if($type == "month_save") {
                return \Carbon::parse($date)->format('M Y');
            }
            return $date;
        } else {
            return $date;
        }
    }
    
    /**
     * columns alphbet of excel column
     *
     * @param array date string
     * @return columns alphbet of excel column
     */
    public static function alphabetRange($count = '26', $end_column = 'ZZ', $first_letters = '') {
        $columns = array();
        $lenth = $count;
        $length = strlen($end_column);
        $letters = range('A', 'Z');

        // Iterate over 26 letters.
        foreach ($letters as $letter) {
            if($lenth <= "0") {
                break;
            }
            // Paste the $first_letters before the next.
            $column = $first_letters . $letter; 
            // Add the column to the final array.
            $columns[] = $column;
            // If it was the end column that was added, return the columns.
            if ($column == $end_column) {
                return $columns;
            }
            $lenth--;
        }
        // Add the column children.
        foreach ($columns as $column) {
            if (!in_array($end_column, $columns) && strlen($column) < $length) {
                $new_columns = self::alphabetRange(($count - count($columns)) ,$end_column, $column);
                // Merge the new columns which were created with the final columns array.
                $columns = array_merge($columns, $new_columns);
            }
        }

        return $columns;
    }

    /**
     * columns alphbet of excel column
     *
     * @param array date string
     * @return columns alphbet of excel column
     */
    static function array_equal($a, $b) {
        return (
            is_array($a) 
            && is_array($b) 
            && count($a) == count($b) 
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    /**
     * send sms
     * CustomHelper::sendsms($data);
     * @param $data as array
     * @return response
     */
    public static function sendsms($data = [])
    {
        if(config('stlc.sms_status',false)) {
            // $arr['user'] = "";
            // $arr['password'] = "";
            $arr['mobiles'] = $data['phone'] ?? "";
            $arr['authkey'] = "";
            $arr['message'] = $data['message'] ?? "";
            $arr['route'] = "4";
            $arr['country'] = "0";
            $arr['sender'] = "PECFYS";
            $url = "https://api.msg91.com/api/sendhttp.php";//.http_build_query($arr);
            $response = (new Client)->get($url,['query'=>$arr]);
            if ($response->getBody()) {
                return $response->getBody();
                // JSON string: { ... }
            } else {
                return $response->getBody();
            }
        }
        return 'env sms_status false';
    }

    public static function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r")); 
        }
        else {
            exec($cmd . " > /dev/null &");  
        }
    }

    /**
     * upload file
     * 
     * CustomHelper::fileUpload($file);
     *
     * @param file date string
     * @return upload
     */
    public static function fileUpload($file,$delete_file_id = null,$url = null)
    {
        $date_append = date("Y-m-d-His-");
        $folder = storage_path('uploads');
        if(!isset($url)) {
            $filename = $file->getClientOriginalName();
            $upload_success = $file->move($folder, $date_append.$filename);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
        } else {
            $success = self::save_file_by_url($url,$folder,$date_append);
            $filename = $success['filename'] ?? $success->filename ?? '';
            $extension = $success['extension'] ?? $success->extension ?? '';
        }
        // return json_encode(((isset($upload_success) && $upload_success == true) || (isset($success['filename']) && !in_array($success['filename'],['female.png','male.png']))));
        if((isset($upload_success) && $upload_success == true) || (isset($success['filename']) && !in_array($success['filename'],['female.png','male.png','others.png']))) {
            $upload = Upload::create([
                "name" => $filename,
                "path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
                "extension" => $extension,
                "caption" => "",
                "hash" => "",
                "public" => true,
                "user_id" => \Auth::id()
            ]);
            // apply unique random hash to file
            while(true) {
                $hash = strtolower(\Str::random(20));
                if(!Upload::where("hash", $hash)->count()) {
                    $upload->hash = $hash;
                    break;
                }
            }
            if(isset($delete_file_id)) {
                $old_upload = Upload::find($delete_file_id);
                if(isset($old_upload->id)) {
                    if(file_exists($old_upload->path)){
                        unlink($old_upload->path);
                        $old_upload->delete();
                    }
                }
            }
            $upload->save();
            return $upload;
        }
        return [];
    }
    
    /**
     * print arrya or object
     * 
     * \CustomHelper::ajprint($date);
     *
     */
    public static function ajprint($array, $retirn = true) {
        echo '<pre>'.json_encode($array,JSON_PRETTY_PRINT).'</pre>';
        if($retirn) {
            exit;
        }
    }

    public static function checkRemoteFile($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    /**
     * print arrya or object
     * 
     * \CustomHelper::save_file_by_url($url);
     *
     */
    public static function save_file_by_url($url,$dir = null,$date_append = null)
    {
        // Initialize the cURL session
        if(self::checkRemoteFile($url)) {
            $ch = curl_init($url);
            // Inintialize directory name where  file will be save 
            if(!isset($dir)) {
                $dir = storage_path('uploads');
            }
            // Use basename() function to return the base name of file  
            $file = pathinfo($url);
            $filename = $file['filename'];
            if(!isset($file['extension']) || empty($file['extension'])) {
                $file['extension'] = 'jpg';
            }
            if(isset($filename) && !in_array($filename,['male','female','others','female.png','male.png','others.png'])) {
                $filename .= '.'.$file['extension'];
                // Save file into file location 
                if(isset($date_append)) {
                    $save_file_loc = $dir .DIRECTORY_SEPARATOR.$date_append. $filename;
                } else {
                    $save_file_loc = $dir .DIRECTORY_SEPARATOR. $filename;
                }
                // Open file  
                $fp = fopen($save_file_loc, 'wb'); 
                // It set an option for a cURL transfer 
                curl_setopt($ch, CURLOPT_FILE, $fp); 
                curl_setopt($ch, CURLOPT_HEADER, 0); 
                // Perform a cURL session 
                $result = curl_exec($ch); 
                // Closes a cURL session and frees all resources 
                curl_close($ch); 
                // Close file 
                fclose($fp);
                return ['result'=>$result,'filename' => $filename,'extension'=>$file['extension']];
            } else {
                return [];
            }
        }
        return self::checkRemoteFile($url);
    }
}


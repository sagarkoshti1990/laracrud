<?php

namespace Sagartakle\Laracrud\Helpers;

use DB;
use Log;

use Sagartakle\Laracrud\Models\Menu;
use Sagartakle\Laracrud\Models\Page;
use Sagartakle\Laracrud\Models\Upload;
use GuzzleHttp\Client;
use Jenssegers\Date\Date;
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
    public static function generateModuleNames($module_name,$module_compair=[])
    {
        $array = array();
        $module_name = trim($module_name);
        $module_name = str_replace(" ", "_", $module_name);
        $array['name'] = ucfirst(\Str::plural($module_name));
        $array['label'] = ucfirst(\Str::plural(preg_replace('/[A-Z]/', ' $0', $module_name)));
        $array['table_name'] = \Str::plural(ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $module_name)), '_'));
        if($module_name == "Users") {
            $array['model'] = "App\\".ucfirst(\Str::singular($module_name));
        } else {
            $array['model'] = "App\Models\\".ucfirst(\Str::singular($module_name));
        }
        $array['controller'] = "App\Http\Controllers\Admin\\".$array['name'] . "Controller";
        
        if(isset($module_compair) && collect($module_compair)->count() > 0) {
            $arr = [];
            if(isset($module_compair['model']) && $module_compair['model'] != $array['model']) {
                $arr['model'] = $module_compair['model'];
            }
            if(isset($module_compair['controller']) && $module_compair['controller'] != $array['controller']) {
                $arr['controller'] = $module_compair['controller'];
            }
            if(isset($module_compair['label']) && $module_compair['label'] != $array['label']) {
                $arr['label'] = $module_compair['label'];
            }
            $array = $arr;
        }

        return $array;
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
        $modules = \Module::all();
        
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
    public static function img($upload, $size = "",$default = null)
    {
        if(!isset($upload->id)) {
            if(class_exists(config('stlc.upload_model'))) {
                $upload = config('stlc.upload_model')::find($upload);
            }
        }
        if(isset($size) && $size != "") {
            $size = "?s=".$size;
        }
        if(isset($upload->id)) {
            return url("files/" .$upload->hash . "/" . $upload->name).$size;
        } else {
            if(isset($default)) {
                return $default;
            } else {
                return asset("public/img/logo.png");
            }
        }
    }
    
    /**
     * Get url of image by using $upload_id
     *
     * CustomHelper::showHtml($upload_id);
     *
     * @param $upload_id upload id of image / file
     * @return string file / image url
     */
    public static function showHtml($upload_id = null,$uploaded_file = 'uploaded_file',$removable = true)
    {
        if(class_exists(config('stlc.upload_model'))) {
            $upload = config('stlc.upload_model')::find($upload_id);
        }
        if(isset($upload->id)) {
            $url_file = url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name);
            $img = "<a title='$upload->name' class='".$uploaded_file." d-inline-block position-relative my-1 mr-2 align-top' upload_id='".$upload->id."' target='_blank' href='".$url_file."'>";

            $image = '';
            if(in_array($upload->extension, ["jpg", "JPG", "jpeg", "png", "gif", "bmp"])) {
                $url_file .= "?s=100";
                $image = '<img  width="100" src="'.$url_file.'" class="card-img-top">';
            } else if(in_array($upload->extension, ["ogg",'wav','mp3'])) {
                $image = '<i class="far fa-file-audio fa-7x text-warning"></i>';
            } else if(in_array($upload->extension, ["mp4","WEBM","MPEG","AVI","WMV","MOV","FLV","SWF"])) {
                $image = '<i class="far fa-file-video fa-7x text-success"></i>';
            } else {
                switch ($upload->extension) {
                    case "pdf":
                    $image = '<i class="far fa-file-pdf fa-7x text-danger"></i>';
                    break;
                case "xls":
                    $image = '<i class="far fa-file-excel fa-7x text-success"></i>';
                    break;
                case "docx":
                    $image = '<i class="far fa-file-word fa-7x"></i>';
                    break;
                case "xlsx":
                    $image = '<i class="far fa-file-excel fa-7x text-success"></i>';
                    break;
                case "csv":
                    $image += '<span class="fa-stack" style="color: #31A867 !important;">';
                    $image += '<i class="far fa-file fa-stack-2x"></i>';
                    $image += '<strong class="fa-stack-1x">CSV</strong>';
                    $image += '</span>';
                    break;
                default:
                    $image = '<i class="far fa-file-text fa-7x"></i>';
                    break;
                }
            }
            $str_name = substr($upload->name,0,10).(strlen($upload->name > 10) ? ".." : "");
            $img .= '<div class="card text-center m-0" style="width: 100px;">
                        '.$image.'
                    <div class="card-body p-1">
                        <p class="card-text">'.$str_name.'</p>
                    </div>
                </div>';
            if($removable == true) {
                $img .= "<i title='Remove File' class='fa fa-times'></i>";
            }
            $img .= "</a>";
            $hide = "d-none";
        } else {
            $img = "<a class='".$uploaded_file." d-none d-inline-block position-relative mt-1 mr-1' target='_blank'>";
            $img .= "<span id='img_icon'></span>";
            $img .= "<i title='Remove File' class='fa fa-times'></i>";
            $img .= "</a>";
            $hide = "";
        }
        return $img;
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
        try {
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
        } catch (\Exception $ex) {
            return response()->json(['status' => 'exception_error', 'message' => 'error', 'errors' => $ex->getMessage()]);
        }
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

        $treeview = " class=\"nav-item\"";
        $str = "";
        $subviewSign = "";
        if(count($childrens)) {
            $treeview = " class=\"nav-item has-treeview\"";
            $subviewSign = '<i class="right fas fa-angle-left"></i>';
        }
        
        if(count($childrens)) {
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    if($menu->link == "#") {
                        $str = '<li' . $treeview . '><a href="javascript:void(0)"><i class="nav-icon ' . $menu->icon . '"></i> <p>' . $menu->label . $subviewSign . '</p></a>';
                    } else {
                        $str = '<li' . $treeview . '><a class="nav-link" href="' . url($prefix_url . $menu->link) . '"><i class="nav-icon ' . $menu->icon . '"></i> <p>' . $menu->label . $subviewSign . '</p></a>';
                    }
                } else {
                    if($children->type == 'page') {
                        $module = Page::where('name',$children->name)->first();
                    } else if($children->type == 'module') {
                        $mkmodule = \Module::where('name',$children->name)->first();
                        if(isset($mkmodule) && $mkmodule->name == $children->name) {
                            $module = $mkmodule->name;
                        } else {
                            $module = collect();
                        }
                    }
                    
                    if(isset($module) && (\Module::hasAccess($module) || $checkAccess)) {
                        if($menu->link == "#") {
                            $str = '<li' . $treeview . '><a class="nav-link" href="javascript:void(0)"><i class="nav-icon ' . $menu->icon . '"></i> <p>' . $menu->label . $subviewSign . '</p></a>';
                        } else {
                            $str = '<li' . $treeview . '><a class="nav-link" href="' . url($prefix_url . $menu->link) . '"><i class="nav-icon ' . $menu->icon . '"></i> <p>' . $menu->label . $subviewSign . '</p></a>';
                        }
                    }
                }
            }
        } else {
            $str = '<li' . $treeview . '><a class="nav-link" href="' . url($prefix_url . $menu->link) . '"><i class="nav-icon ' . $menu->icon . '"></i> <p>' . $menu->label . $subviewSign . '</p></a>';
        }
        
        if(count($childrens)) {
            $str .= '<ul class="nav nav-treeview">';
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    $str .= self::print_menu($children,$prefix,$checkAccess);
                } else {
                    if($children->type == 'page') {
                        $module = Page::where('name',$children->name)->first();
                    } else if($children->type == 'module') {
                        $mkmodule = \Module::where('name',$children->name)->first();
                        if(isset($mkmodule) && $mkmodule->name == $children->name) {
                            $module = $mkmodule->name;
                        } else {
                            $module = collect();
                        }
                    }
                    if(isset($module) && (\Module::hasAccess($module) || $checkAccess)) {
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
        $childrens = config('stlc.menu_model')::where("parent", $menu->id)->orderBy('hierarchy', 'asc')->get();
        
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
    public static function generateMenu($menus = [],$parent = null,$generateModule = true,$withTruncate = true)
    {
        // Generating Module Menus
		if(Schema::hasTable('menus')) {
            if($withTruncate == true) {
                config('stlc.menu_model')::truncate();
            }
            if(!isset($menus) || (!isset($parent) && is_array($menus) && count($menus) == 0)) {
                $menus = config('stlc.generateMenu',[]);
            }
            foreach($menus as $key => $menu) {
                $menuData = [];
                if(is_string($menu)) {
                    $module = \Module::where('name',$menu)->first();
                    if(isset($module->id)) {
                        $menuData['name'] = $module->name;
                        $menuData['label'] = $module->label;
                        $menuData['link'] = $module->table_name;
                        $menuData['icon'] = $module->icon;
                    }
                } else if(isset($menu) && is_array($menu) && isset($menu['name'])){
                    if(isset($menu['name'])) {
                        $module = \Module::where('name',$menu['name'])->first();
                    }
                    if(isset($module->id)) {
                        $menuData['name'] = $module->name;
                        $menuData['label'] = $menu['label'] ?? $module->label;
                        $menuData['link'] = $menu['link'] ?? $module->table_name;
                        $menuData['icon'] = $menu['icon'] ?? $module->icon;
                    } else {
                        $menuData = $menu;
                    }
                }
                if(isset($menuData['name'])) {
                    $storeMenu = config('stlc.menu_model')::create([
                        'name' => $menuData['name'],
                        'label' => $menuData['label'] ?? ucfirst(\Str::plural(preg_replace('/[A-Z]/', ' $0', $menuData['name']))),
                        'link' => $menuData['link'] ?? "#",
                        'icon' => $menuData['icon'] ?? "fa fa-smile",
                        'type' => $menuData['type'] ?? 'module',
                        'rank' => $menuData['rank'] ?? $key,
                        'parent' => $parent,
                        'hierarchy' => $menuData['hierarchy'] ?? 0,
                    ]);

                    if(isset($menu['childMenu']) && is_array($menu['childMenu']) > 0) {
                        self::generateMenu($menu['childMenu'],$storeMenu->id,false,false);
                    }
                } else {
                    dd($menu,'not found');
                }
            }
            if(Schema::hasTable('modules') && $generateModule == true) {
                $modules = \Module::whereNotIn('name',config('stlc.restrictedModules.menu',['Users','Uploads']))
                            ->whereNotIn('name',config('stlc.menu_model')::select('name')->pluck('name'))->get();
                foreach ($modules as $module) {
                    if(Schema::hasTable('menus')) {
                        config('stlc.menu_model')::create([
                            "name" => $module->name,
                            "label" => $module->label,
                            "link" => $module->table_name,
                            "icon" => $module->icon
                        ]);
                    }
                }
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
                return ['response' => $response->getBody(),'data' => $arr,'url' => $url];
                // JSON string: { ... }
            } else {
                return $response->getBody();
                return ['status'=>"400",'response' => $response->getBody(),'data' => $arr,'url' => $url];
            }
        }
        return 'env sms_status false';
    }

    /**
     * send sms
     * CustomHelper::sendMail($data);
     * @param $data as array
     * @return response
     */
    public static function sendMail($data = [])
    {   
        if(config('lara.base.mail_status',false)) {
            $data['authkey'] = $data['authkey'] ?? "";
            $data['template_id'] = $data['template_id'] ?? "";
            $data['to'] = $data['to'] ?? "";
            $data['from'] = config('lara.base.mail_to_support');
            $url = "https://api.msg91.com/api/v5/email";//.http_build_query($data);
            $response = (new Client)->request("POST", $url, [ 'json' => $data ]);
            if ($response->getBody()) {
                return ['status'=>"200",'response' => json_decode((string) $response->getBody(), true),'data' => $data,'url' => $url];
            } else {
                return ['status'=>"400",'response' => $response->getBody(),'data' => $data,'url' => $url];
            }
        }
        return 'env mail_status false';
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
            $upload = config('stlc.upload_model')::create([
                "name" => $filename,
                "path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
                "extension" => $extension,
                "caption" => "",
                "hash" => "",
                "public" => true,
				"context_id" => \Module::user()->id ?? null,
				"context_type" => get_class(\Module::user())
            ]);
            // apply unique random hash to file
            while(true) {
                $hash = strtolower(\Str::random(20));
                if(!config('stlc.upload_model')::where("hash", $hash)->count()) {
                    $upload->hash = $hash;
                    break;
                }
            }
            if(isset($delete_file_id)) {
                $old_upload = config('stlc.upload_model')::find($delete_file_id);
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

    /**
     * please check env APP_URL
     */
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
     * please check env APP_URL
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
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
            return url("files/" . $upload->hash . DIRECTORY_SEPARATOR . $upload->name).$size;
        } else {
            return "";
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
        $editing = \Collective\Html\FormFacade::open(['route' => [config('lara.base.route_prefix') . '.menus.destroy', $menu->id], 'method' => 'delete', 'style' => 'display:inline']);
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
    public static function print_menu(Menu $menu, $active = false)
    {
        $childrens = $menu->childrensMenu;

        $treeview = "";
        $str = "";
        $subviewSign = "";
        if(count($childrens)) {
            $treeview = " class=\"treeview\"";
            $subviewSign = '<i class="fa fa-angle-left pull-right"></i>';
        }
        $active_str = '';
        if($active) {
            $active_str = 'class="active"';
        }
        if(count($childrens)) {
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    if($menu->link == "#") {
                        $str = '<li' . $treeview . ' ' . $active_str . '><a href="javascript:void(0)"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                    } else {
                        $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("lara.base.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                    }
                } else {
                    if($children->type == 'page') {
                        $module = Page::where('name',$children->name)->first();
                    } else if($children->type == 'module') {
                        $mkmodule = Module::where('name',$children->name)->first();
                        if(isset($mkmodule) && $mkmodule->name == $children->name) {
                            $module = $mkmodule;
                        }
                    }
                    if(isset($module) && Module::hasAccess($module)) {
                        $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("lara.base.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
                    }
                }
            }
        } else {
            $str = '<li' . $treeview . ' ' . $active_str . '><a href="' . url(config("lara.base.route_prefix") . '/' . $menu->link) . '"><i class="fa ' . $menu->icon . ' text-purple"></i> <span>' . $menu->label . '</span> ' . $subviewSign . '</a>';
        }
        
        if(count($childrens)) {
            $str .= '<ul class="treeview-menu">';
            foreach($childrens as $children) {
                if($children->type == 'custom') {
                    $str .= self::print_menu($children);
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
                    if(isset($module) && Module::hasAccess($module)) {
                        $str .= self::print_menu($children);
                    }
                }
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
            // $scMenu = Menu::create([
			// 	"name" => "SportsAndCommunities","label" => 'Sports && Communities',"link" => "#",
			// 	"icon" => "fa-connectdevelop","type" => 'custom',"hierarchy" => 1
			// ]);
			$masterMenu = Menu::create([
				"name" => "Master","label" => 'Master',"link" => "#",
				"icon" => "fa-cogs","type" => 'custom',"hierarchy" => 1
            ]);
            $eventMenu = Menu::create([
				"name" => "Event","label" => 'Events',"link" => "#",
				"icon" => "fa-map","type" => 'custom',"hierarchy" => 1
            ]);
            $partnerMenu = Menu::create([
				"name" => "Partner","label" => 'Packages',"link" => "#",
				"icon" => "fa-wpforms","type" => 'custom',"hierarchy" => 1
			]);
			$mcMenu = Menu::create([
				"name" => "MarketingAndContents","label" => 'Marketing & Contents',"link" => "#",
				"icon" => "fa-bullhorn","type" => 'custom',"hierarchy" => 1
            ]);
			$OthersMenu = Menu::create([
				"name" => "Others","label" => 'Others',"link" => "#",
				"icon" => "fa-ellipsis-v","type" => 'custom',"hierarchy" => 1
            ]);
		}
		foreach ($modules as $module) {
			$parent = Null;
			if(!in_array($module->name, ["Users","Tests","Uploads",'Devicetokens','Notifications','VerificationTokens','Addresses','Settings','ComplaintReports','PartnerComponents','PartnerUpgrades','Headers','Chats','Posts','UserFavorites','ReportedUsers','BlockedUsers'])) {
                $label = $module->label;
                if($module->name == 'MasterUsers') {
                    $label = "Users";
                }  
				if(in_array($module->name, ["Employees","Roles",'PartnerUsers'])) {
                    $parent = $profileMenu->id;
                    if($module->name == 'PartnerUsers') {
                        $label = "Partner";
                    }
                // } else if(in_array($module->name, [])) { PackageAddons
                //     $parent = $scMenu->id;
				} else if(in_array($module->name, ['Events'])) { 
					$parent = $eventMenu->id;
				}else if(in_array($module->name, ['Packages','PackageComponents','PackageUpgrades','PackageAddons'])) {
                    if($module->name == 'PackageComponents') {
                        $label = "Components";
                    } else if($module->name == 'PackageUpgrades') {
                        $label = "Upgrades";
                    } else if($module->name == 'PackageAddons') {
                        $label = "Addons";
                    }else{
                        $label = "List";
                    }
                
                    $parent = $partnerMenu->id;
            
                }else if(in_array($module->name, ['Campaigns','Channels','Campaignchannels','Advertisements','Celebrities','Fansites'])) {
					$parent = $mcMenu->id;
				}else if(in_array($module->name, ['Faqs','Messages','Enquiries','PageContents','Stickers'])) {
					$parent = $OthersMenu->id;
				} else if(in_array($module->name, [
                    "Sports","SportCategories",'Communities',
					"States","Cities",'Memberships','PassionLevels','Subscriptions',
					'Rewards','Questions','Answers','ComplaintReports','Featurelists','Coupons'
				])) {
					$parent = $masterMenu->id;
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
			$ranking = [
				'Dashboard','Master','MasterUsers','UserConnections',"Sports","SportCategories",'Communities','Profile'
			];
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
                return \Date::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } else if($type == "data_save_with_time") {
                return \Date::createFromFormat('d/m/Y H:i:s', $date)->format('Y-m-d H:i:s');
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
    static function array_equal($a, $b) {
        return (
            is_array($a) 
            && is_array($b) 
            && count($a) == count($b) 
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    public static function sendsms($data = [])
    {
        if(env('SMS_STATUS',false)) {
            $arr['user'] = "CRMPWR";
            $arr['password'] = "123456";
            $arr['sender'] = "CRMPWR";
            $arr['dest'] = $data['phone'] ?? "";
            $arr['text'] = $data['massage'] ?? "";
            $url = "http://api?".http_build_query($arr);
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                echo json_encode(curl_error($ch));
            }
            curl_close($ch);
            return $data;
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
        return [];
    }
}

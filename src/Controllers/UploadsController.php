<?php

namespace Sagartakle\Laracrud\Controllers;

use Auth;
use File;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Upload;
use Illuminate\Http\Request;
use Sagartakle\Laracrud\Controllers\StlcController;
use Illuminate\Support\Facades\Response as FacadeResponse;

class UploadsController extends StlcController
{
    function __construct() {
        $this->crud = Module::make('Uploads',['setModel' => Upload::class]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($this->crud->hasAccess('view')) {
            
            $crud = $this->crud;
            $uploads = Upload::all();

            return view('admin.Uploads.index1', [
                'crud' => $crud,
                'uploads' => $uploads
            ]);
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }
	/**
     * Get file
     *
     * @return \Illuminate\Http\Response
     */
    public function get_file(Request $request, $hash, $name)
    {
        $upload = Upload::where("hash", $hash)->first();
        
        // Validate Upload Hash & Filename
        if(!isset($upload->id) || $upload->name != $name) {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access 1"
            ]);
        }

        if($upload->public == 1) {
            $upload->public = true;
        } else {
            $upload->public = false;
        }

        // Validate if Image is Public
        if(!$upload->public && !isset(Auth::user()->id)) {
            return response()->json([
                'status' => "failure",
                'message' => "Unauthorized Access 2",
            ]);
        }

        // if($upload->public || Entrust::hasRole('SUPER_ADMIN') || Auth::user()->id == $upload->user_id) {
            
            $path = $upload->path;

            if(!File::exists($path))
                abort(404);
            
            // Check if thumbnail
            $size = $request->s ?? "150";
            if(isset($size)) {
                $arrsize = explode("X",$size);
                if(isset($arrsize) && count($arrsize) > 1) {
                    $size = "-".$arrsize[0].'x'.$arrsize[1];
                } else {
                    $arrsize[0] = $size;
                    $arrsize[1] = $size;
                    $size = "-".$arrsize[0].'x'.$arrsize[1];
                }

                $thumbpath = storage_path("thumbnails".DIRECTORY_SEPARATOR.$size.basename($upload->path));
                
                if(File::exists($thumbpath)) {
                    $path = $thumbpath;
                } else {
                    // Create Thumbnail
                    \CustomHelper::createThumbnail($upload->path, $thumbpath, $arrsize[0] ?? $size, $arrsize[1] ?? $size, "transparent");
                    $path = $thumbpath;
                }
            }

            $file = File::get($path);
            $type = File::mimeType($path);

            $download = $request->download;
            if(isset($download)) {
                return response()->download($path, $upload->name);
            } else {
                $response = FacadeResponse::make($file, 200);
                $response->header("Content-Type", $type);
            }
            
            return $response;
        // } else {
        //     return response()->json([
        //         'status' => "failure",
        //         'message' => "Unauthorized Access 3"
        //     ]);
        // }
    }

    /**
     * Get all files from uploads folder
     *
     * @return \Illuminate\Http\Response
     */
    public function uploaded_files(Request $response)
    {
		if(Module::hasAccess("Uploads", "view")) {

            if(isset($response->file_type) && $response->file_type == "image") {
                $uploads = Upload::whereIn('extension',["jpg", "jpeg", "png", "gif", "bmp"])->get();
            } else if(isset($response->file_type) && in_array($response->file_type, ['file','files'])){
                $uploads = Upload::all();
            } else if(isset($response->file_type)){
                $uploads = Upload::where('extension',$response->file_type)->get();
            } else {
                $uploads = Upload::all();
            }
            
			$uploads2 = array();
			foreach ($uploads as $upload) {
				$u = (object) array();
				$u->id = $upload->id;
				$u->name = $upload->name;
				$u->extension = $upload->extension;
				$u->hash = $upload->hash;
				$u->public = $upload->public;
				$u->caption = $upload->caption;
				$u->user = $upload->user->name;
				
				$uploads2[] = $u;
			}
			
			// $folder = storage_path('/uploads');
			// if(file_exists($folder)) {
			//     $filesArr = File::allFiles($folder);
			//     foreach ($filesArr as $file) {
			//         $uploads2[] = $file->getfilename();
			//     }
			// }
			return response()->json(['uploads' => $uploads2]);
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
    }

    /**
     * Update Uploads Caption
     *
     * @return \Illuminate\Http\Response
     */
    public function update_caption()
    {
        if(Module::hasAccess("Uploads", "edit")) {
			$file_id = $request->get('file_id');
			$caption = $request->get('caption');
			
			$upload = Upload::find($file_id);
			if(isset($upload->id)) {
				// if($upload->user_id == Auth::user()->id || Entrust::hasRole('SUPER_ADMIN')) {
	
					// Update Caption
					$upload->caption = $caption;
					$upload->save();
	
					return response()->json([
						'status' => "success"
					]);
	
				// } else {
				// 	return response()->json([
				// 		'status' => "failure",
				// 		'message' => "Upload not found"
				// 	]);
				// }
			} else {
				return response()->json([
					'status' => "failure",
					'message' => "Upload not found"
				]);
			}
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
    }

    /**
     * Update Uploads Filename
     *
     * @return \Illuminate\Http\Response
     */
    public function update_filename()
    {
        if(Module::hasAccess("Uploads", "edit")) {
			$file_id = $request->get('file_id');
			$filename = $request->get('filename');
			
			$upload = Upload::find($file_id);
			if(isset($upload->id)) {
				// if($upload->user_id == Auth::user()->id || Entrust::hasRole('SUPER_ADMIN')) {
	
					// Update Caption
					$upload->name = $filename;
					$upload->save();
	
					return response()->json([
						'status' => "success"
					]);
	
				// } else {
				// 	return response()->json([
				// 		'status' => "failure",
				// 		'message' => "Unauthorized Access 1"
				// 	]);
				// }
			} else {
				return response()->json([
					'status' => "failure",
					'message' => "Upload not found"
				]);
			}
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
    }

    /**
     * Update Uploads Public Visibility
     *
     * @return \Illuminate\Http\Response
     */
    public function update_public()
    {
		if(Module::hasAccess("Uploads", "edit")) {
			$file_id = $request->get('file_id');
			$public = $request->get('public');
			if(isset($public)) {
				$public = true;
			} else {
				$public = false;
			}
			
			$upload = Upload::find($file_id);
			if(isset($upload->id)) {
				// if($upload->user_id == Auth::user()->id || Entrust::hasRole('SUPER_ADMIN')) {
	
					// Update Caption
					$upload->public = $public;
					$upload->save();
	
					return response()->json([
						'status' => "success"
					]);
	
				// } else {
				// 	return response()->json([
				// 		'status' => "failure",
				// 		'message' => "Unauthorized Access 1"
				// 	]);
				// }
			} else {
				return response()->json([
					'status' => "failure",
					'message' => "Upload not found"
				]);
			}
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
    }

    /**
     * Remove the specified upload from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete_file()
    {
        if(Module::hasAccess("Uploads", "delete")) {
			$file_id = $request->get('file_id');
			
			$upload = Upload::find($file_id);
			if(isset($upload->id)) {
				// if($upload->user_id == Auth::user()->id || Entrust::hasRole('SUPER_ADMIN')) {
	
					// Update Caption
					$upload->delete();
	
					return response()->json([
						'status' => "success"
					]);
	
				// } else {
				// 	return response()->json([
				// 		'status' => "failure",
				// 		'message' => "Unauthorized Access 1"
				// 	]);
				// }
			} else {
				return response()->json([
					'status' => "failure",
					'message' => "Upload not found"
				]);
			}
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
    }
}

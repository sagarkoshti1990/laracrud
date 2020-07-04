<?php

namespace Sagartakle\Laracrud\Controllers;

use DB;
use Auth;
use File;
use Validator;

use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Upload;
use Sagartakle\Laracrud\Models\Activity;
use Illuminate\Http\Request;
use Yajra\DataTables\Datatables;
use Illuminate\Routing\Controller;
use Prologue\Alerts\Facades\Alert;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Facades\Response as FacadeResponse;

class UploadsController extends Controller
{
    function __construct() {
        $this->crud = Module::make('Uploads');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($this->crud->hasAccess('create')) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $crud = $this->crud;

            return view('admin.Uploads.create', [
                'crud' => $crud,
                'src' => $src
            ]);
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($this->crud->hasAccess('create')) {
            if (is_null($request)) {
                $request = \Request::instance();
            }
            
            $rules = Module::validateRules("Uploads", $request);
			$validator = Validator::make($request->all(), $rules);
			
			if ($validator->fails()) {
				return redirect()->back()->withErrors($validator)->withInput();
			}

            // replace empty values with NULL, so that it will work with MySQL strict mode on
            foreach ($request->input() as $key => $value) {
                if (empty($value) && $value !== '0') {
                    $request->request->set($key, null);
                }
            }

            // insert item in the db
            $item = $this->crud->create($request);
            $this->data['entry'] = $this->crud->entry = $item;

            // add activity log
            // \Activity::log(config('App.activity_log.CREATED'), $this->crud, ['new' => $item]);

            // show a success message
            if(!$request->src_ajax) {
                \Alert::success(trans('crud.insert_success'))->flash();
            }

            if(isset($request->go_view) && $request->go_view) {
                return redirect($this->crud->route.'/'.$item->id);
            } else if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'success', 'massage' => 'created', 'item' => $item]);
            } else if(isset($request->src)) {
                return redirect($request->src);
            } else {
                return redirect($this->crud->route);
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if($this->crud->hasAccess('view')) {
            if(isset($request->src)) {
                $src = url($request->src);
            } else {
                $src = Null;
            }

            $upload = Upload::find($id);
            if(isset($upload->id)) {
                
                $crud = $this->crud;
                $crud->datatable = true;
                $crud->row = $upload;
            
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'updated', 'item' => $upload]);
                } else {
                    return view('admin.Uploads.show', [
                        'crud' => $crud,
                        'upload' => $upload,
                        'src' => $src,
                        'represent_attr' => $crud->module->represent_attr
                    ]);
                }
            } else {
                if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    return view('errors.404', [
                        'record_id' => $id,
                        'record_name' => ucfirst("uploads"),
                    ]);
                }
            }
        } else {
            if(isset($request->get_data_ajax) && $request->get_data_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
            }
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if($this->crud->hasAccess('edit')) {
            if(isset($request->src)) {
                $src = $request->src;
            } else {
                $src = Null;
            }
            
            $upload = Upload::find($id);
            if(isset($upload->id)) {
                
                $crud = $this->crud;
                $crud->row = $upload;
            
                return view('admin.Uploads.edit', [
                    'crud' => $crud,
                    'upload' => $upload,
                    'src' => $src
                ]);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("uploads"),
                ]);
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($this->crud->hasAccess('edit')) {
            // old data
            $old_item = Upload::find($id);
            if(isset($old_item->id)) {
                if (is_null($request)) {
                    $request = \Request::instance();
                }

                $rules = Module::validateRules("Uploads", $request, true);
                $validator = Validator::make($request->all(), $rules);
                
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // replace empty values with NULL, so that it will work with MySQL strict mode on
                foreach ($request->input() as $key => $value) {
                    if (empty($value) && $value !== '0') {
                        $request->request->set($key, null);
                    }
                }

                // update the row in the db
                $item = $this->crud->update($id, $request);
                $this->data['entry'] = $this->crud->entry = $item;

                // add activity log
                // \Activity::log(config('App.activity_log.UPDATED'), $this->crud, ['new' => $item, 'old' => $old_item]);

                // show a success message
                if(!$request->src_ajax) {
                    \Alert::success(trans('crud.update_success'))->flash();
                }

                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'updated', 'item' => $item]);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    return redirect($this->crud->route);
                }
            } else {
                abort(403, trans('crud.data_not_found'));
            }
        } else {
            abort(403, trans('crud.unauthorized_access'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if($this->crud->hasAccess('delete')) {
            // old data
            $old_item = Upload::find($id);
            if(isset($old_item->id)) {
                $upload = Upload::find($id)->delete();

                // add activity log
                // \Activity::log(config('App.activity_log.DELETED'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'deleted']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    // return redirect()->route(config('stlc.route_prefix') . 'crud.uploads.index');
                    return (string) $upload;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    abort(403, trans('crud.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, $id)
    {
        if($this->crud->hasAccess('deactivate')) {
            // old data
            $old_item = Upload::onlyTrashed()->find($id);
            if(isset($old_item->id)) {
                $upload = Upload::onlyTrashed()->find($id)->restore();

                // add activity log
                // \Activity::log(config('App.activity_log.restore'), $this->crud, ['old' => $old_item]);
                
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'success', 'massage' => 'restore']);
                } else if(isset($request->src)) {
                    return redirect($request->src);
                } else {
                    // return redirect()->route(config('stlc.route_prefix') . 'crud.uploads.index');
                    return (string) $upload;
                }
            } else {
                if(isset($request->src_ajax) && $request->src_ajax) {
                    return response()->json(['status' => 'failed', 'massage' => trans('crud.data_not_found')]);
                } else {
                    abort(403, trans('crud.data_not_found'));
                }
            }
        } else {
            if(isset($request->src_ajax) && $request->src_ajax) {
                return response()->json(['status' => 'failed', 'massage' => trans('crud.unauthorized_access')]);
            } else {
                abort(403, trans('crud.unauthorized_access'));
            }
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
     * Upload fiels via DropZone.js
     *
     * @return \Illuminate\Http\Response
     */
    public function upload_files(Request $request) {
        
		if(Module::hasAccess("Uploads", "create")) {
			$input = $request->all();
			
			if($request->hasFile('file')) {
				/*
				$rules = array(
					'file' => 'mimes:jpg,jpeg,bmp,png,pdf|max:3000',
				);
				$validation = Validator::make($input, $rules);
				if ($validation->fails()) {
					return response()->json($validation->errors()->first(), 400);
				}
				*/
				$file = $request->file('file');
				
				// print_r($file);
				
				$folder = storage_path('uploads');
				$filename = $file->getClientOriginalName();
	
				$date_append = date("Y-m-d-His-");
				$upload_success = $request->file('file')->move($folder, $date_append.$filename);
				
				if( $upload_success ) {
	
					// Get public preferences
					// config("lara.uploads.default_public")
					$public = $request->get('public');
					if(isset($public)) {
						$public = true;
					} else {
						$public = false;
					}
	
					$upload = Upload::create([
						"name" => $filename,
						"path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
						"extension" => pathinfo($filename, PATHINFO_EXTENSION),
						"caption" => "",
						"hash" => "",
						"public" => $public,
						"user_id" => Auth::user()->id
					]);
					// apply unique random hash to file
					while(true) {
						$hash = strtolower(\Str::random(20));
						if(!Upload::where("hash", $hash)->count()) {
							$upload->hash = $hash;
							break;
						}
					}
					$upload->save();
	
					return response()->json([
						"status" => "success",
						"upload" => $upload
					], 200);
				} else {
					return response()->json([
						"status" => "error"
					], 400);
				}
			} else {
				return response()->json('error: upload file not found.', 400);
			}
		} else {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access"
			]);
		}
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

    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $crud = $this->crud;
        $listing_cols = Module::getListingColumns('Uploads');
        
        if(isset($request->filter)) {
			$values = DB::table('uploads')->select($listing_cols)->whereNull('deleted_at')->where($request->filter);
		} else {
			$values = DB::table('uploads')->select($listing_cols)->whereNull('deleted_at');
		}
        
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        
        $fields_popup = Field::getFields('Uploads');
        
        // array_splice($listing_cols, 2, 0, "index_name");
        
        for($i = 0; $i < count($data->data); $i++) {
            $data->data[$i] = collect($data->data[$i])->values()->all();
            $upload = Upload::find($data->data[$i][0]);
            // array_splice($data->data[$i], 2, 0, true);
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                if(isset($data->data[$i][$j]) && $data->data[$i][$j]) {
                    if(isset($fields_popup[$col]) && $fields_popup[$col]->field_type_str == "Date_picker") {
                        $data->data[$i][$j] = \CustomHelper::date_format($data->data[$i][$j]);
                    }
                    if(isset($fields_popup[$col]) && $fields_popup[$col] != null && \Str::startsWith($fields_popup[$col]->json_values, "@")) {
                        $data->data[$i][$j] = Field::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                    }
                    if($col == $crud->module->represent_attr) {
                        $data->data[$i][$j] = '<a href="' . url($crud->route .'/'. $upload->id) . '">' . $data->data[$i][$j] . '</a>';
                    }
                }
            }
            
            if ($crud->buttons->where('stack', 'line')->count()) {
                $crud->datatable = true;
                $output = '';
                
                $output .= \View::make('crud.inc.button_stack', ['stack' => 'line'])
                ->with('crud', $crud)
                ->with('entry', $upload)
                ->render();

                $data->data[$i][] = (string)$output;
            }
        }
        $out->setData($data);
        return $out;
    }
}

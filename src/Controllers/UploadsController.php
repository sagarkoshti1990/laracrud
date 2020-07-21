<?php

namespace Sagartakle\Laracrud\Controllers;

use Auth;
use File;
use Sagartakle\Laracrud\Models\Field;
use Sagartakle\Laracrud\Models\Module;
use Sagartakle\Laracrud\Models\Upload;
use Illuminate\Http\Request;
use Sagartakle\Laracrud\Controllers\StlcController;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;

class UploadsController extends StlcController
{
    function __construct() {
        $this->crud = Module::make('Uploads',['setModel' => Upload::class,'route_prefix' => config('stlc.stlc_route_prefix', 'developer')]);
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
			if(isset($request->s) && $request->s != 'full') {
				$size = $request->s ?? "150";
			}
			if(isset($size) && in_array($upload->extension,['jpg','JPG','jpeg','JPEG','PNG','png'])) {
				$arrsize = explode("X",$size);
				if(isset($arrsize) && count($arrsize) > 1) {
					$size = "-".$arrsize[0].'x'.$arrsize[1];
				} else {
					$arrsize[0] = $size;
					$arrsize[1] = $request->x ?? $size;
					$size = "-".$arrsize[0].'x'.$arrsize[1];
				}
				$thumbpath = storage_path("thumbnails".DIRECTORY_SEPARATOR.$size.basename($upload->path));
				
				if(File::exists($thumbpath)) {
					$path = $thumbpath;
				} else {
					// Create Thumbnail
					$item = \CustomHelper::createThumbnail($upload->path, $thumbpath, $arrsize[0] ?? $size, $arrsize[1] ?? $size, [0,0,0]);
					if (($item instanceof \Illuminate\Http\RedirectResponse) || ($item instanceof \Illuminate\Http\JsonResponse)) {
						// return $item;
					} else {
						$path = $thumbpath;
					}
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
	public function upload_files(Request $request)
	{
		if(Module::hasAccess("Uploads", "create") || true) {
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
				// create the file receiver
				$receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));
			
				// check if the upload is success, throw exception or return response you need
				if ($receiver->isUploaded() === false) {
					throw new UploadMissingFileException();
				}
			
				// receive the file
				$save = $receiver->receive();
			
				// check if the upload has finished (in chunk mode it will send smaller files)
				if ($save->isFinished()) {
					// save the file and return any response you need, current example uses `move` function. If you are
					// not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
					return $this->saveFile($save->getFile());
				}
			
				// we are in chunk mode, lets send the current progress
				/** @var AbstractHandler $handler */
				$handler = $save->handler();
			
				return response()->json([
					"done" => $handler->getPercentageDone(),
				]);
				
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
	 * Saves the file to S3 server
	 *
	 * @param UploadedFile $file
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function saveFileToS3($file)
	{
		$fileName = $this->createFilename($file);

		$disk = Storage::disk('s3');
		// It's better to use streaming Streaming (laravel 5.4+)
		$disk->putFileAs('photos', $file, $fileName);

		// for older laravel
		// $disk->put($fileName, file_get_contents($file), 'public');
		$mime = str_replace('/', '-', $file->getMimeType());

		// We need to delete the file when uploaded to s3
		unlink($file->getPathname());

		return response()->json([
			'path' => $disk->url($fileName),
			'name' => $fileName,
			'mime_type' =>$mime
		]);
	}

	/**
	 * Saves the file
	 *
	 * @param UploadedFile $file
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function saveFile(UploadedFile $file)
	{
		$folder = storage_path('uploads');
		$filename = $file->getClientOriginalName();
		$date_append = date("Y-m-d-His-");
		$upload_success = $file->move($folder, $date_append.$filename);
		
		if( $upload_success ) {
			// Get public preferences
			// $public = $request->get('public');
			if(isset($public)) {
				$public = true;
			} else {
				$public = true;
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

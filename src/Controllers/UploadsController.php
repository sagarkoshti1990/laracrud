<?php

namespace Sagartakle\Laracrud\Controllers;

use Auth;
use File;
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
        $this->crud = \Module::make('Uploads',['setModel' => config('stlc.upload_model'),'route_prefix' => config('stlc.stlc_route_prefix', 'developer')]);
    }
	
	/**
	 * Get file
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function get_file(Request $request, $hash, $name)
	{
		$upload = config('stlc.upload_model')::where("hash", $hash)->first();
		
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
		if(!$upload->public && !isset(\Module::user()->id)) {
			return response()->json([
				'status' => "failure",
				'message' => "Unauthorized Access 2",
			]);
		}

		// if($upload->public || Entrust::hasRole('SUPER_ADMIN') || \Module::user()->id == $upload->user_id) {
			
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
		if($this->crud->hasAccess('create') || true) {
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

			$upload = config('stlc.upload_model')::create([
				"name" => $filename,
				"path" => $folder.DIRECTORY_SEPARATOR.$date_append.$filename,
				"extension" => pathinfo($filename, PATHINFO_EXTENSION),
				"caption" => "",
				"hash" => "",
				"public" => $public,
				"context_id" => (\Module::user() !== null) ? \Module::user()->id : '',
				"context_type" => (\Module::user() !== null) ? get_class(\Module::user()) : ''
			]);
			// apply unique random hash to file
			while(true) {
				$hash = strtolower(\Str::random(20));
				if(!config('stlc.upload_model')::where("hash", $hash)->count()) {
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
		if($this->crud->hasAccess('view')) {
			$select = ['id','name','extension','hash','public','caption'];
			if(isset($response->file_type) && $response->file_type == "image") {
				$uploads = config('stlc.upload_model')::select($select)->whereIn('extension',["jpg", "jpeg", "png", "gif", "bmp"])->paginate(config('stlc.file_modal_paginate_count',18));
			} else if(isset($response->file_type) && in_array($response->file_type, ['file','files'])){
				$uploads = config('stlc.upload_model')::select($select)->paginate(config('stlc.file_modal_paginate_count',18));
			} else if(isset($response->file_type)){
				$uploads = config('stlc.upload_model')::select($select)->where('extension',$response->file_type)->paginate(config('stlc.file_modal_paginate_count',18));
			} else {
				$uploads = config('stlc.upload_model')::select($select)->paginate(config('stlc.file_modal_paginate_count',18));
			}
			return response()->json([
				'uploads' => $uploads,
				'link' => (string)$uploads->links()
			]);
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
		if($this->crud->hasAccess("delete")) {
			$file_id = $request->get('file_id');
			
			$upload = config('stlc.upload_model')::find($file_id);
			if(isset($upload->id)) {
				// if($upload->user_id == \Module::user()->id || Entrust::hasRole('SUPER_ADMIN')) {
	
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

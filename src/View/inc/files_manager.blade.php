<div class="modal fade" id="fm" role="dialog" aria-labelledby="fileManagerLabel">
	<input type="hidden" id="image_selecter_origin" value="">
	<input type="hidden" id="image_selecter_origin_type" value="">
	<input type="hidden" id="image_selecter_extension_type" value="">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header {{ config('stlc.navbar') }}">
				<h4 class="modal-title col-12 col-sm-6" id="fileManagerLabel">Select File</h4>
				<input type="search" class="form-control col-sm-5" placeholder="Search file name">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body py-0">
				<div class="row">
					<div class="col-12 col-sm-2 col-md-2 col-lg-2 col-xl-2 pr-xs-0">
						<div class="fm_folder_selector text-center p-4 my-2">
							<form action="{{ url(config('stlc.stlc_route_prefix') . '/upload_files')}}" id="fm_dropzone" enctype="multipart/form-data" method="POST">
								{{ csrf_field() }}
								<div class="row">
									<div class="col-xs-12 col-sm-12 col-md-12">
										<div class="dz-message p-4 border"><i class="fa fa-cloud-upload-alt fa-4x text-primary"></i><br>Drop files here</div>
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12 float-right">
										<label class="fm_folder_title my-5">Is {{ trans('stlc.public') }}
											{{ Form::checkbox("public",1,true) }}
										</label>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="col-12 col-sm-10 col-md-10 col-lg-10 col-xl-10 fm_file_selector" style="border-left:1px solid #3c8dbc;">
						<ul class="list-inline-item p-3"></ul>
					</div>
				</div>
			</div>
			<div class="modal-footer p-0"></div>
		</div>
	</div>
</div>
@push('after_scripts')
	<link rel="stylesheet" href="{{ asset('node_modules/cropperjs/dist/cropper.min.css') }}">
	{{-- <script src="{{ asset('node_modules/dropzone/dist/dropzone.js') }}"></script> --}}
	<script src="{{ asset('node_modules/dropzone/dist/min/dropzone.min.js') }}"></script>
	<script src="{{ asset('node_modules/dropzone/dist/dropzone-amd-module.js') }}"></script>
	<script src="{{ asset('node_modules/cropperjs/dist/cropper.js') }}"></script>
	<style>
		.crop-editor{position:fixed;left:0;right:0;top:0;bottom:0;z-index:9999;background-color: #000;}
		.upload-confirm-btn,.upload-close-btn{position:absolute;left:15px;top:15px;z-index:9999;}
		.upload-close-btn{right: 15px;left: auto;}
		.uploaded_image i.fa-times,a.uploaded_file i.fa.fa-times,a.uploaded_file2 i.fa.fa-times {
			background: #f10000;display: block;position: absolute;top: -5px;right: -5px;color: #FFF;
			padding: 3px;border-radius: 50%;text-align: center;font-size: 10px;cursor: pointer;z-index: 999;
		}
	</style>
<!-- ================= File Manager ================= -->
<script>
	Dropzone.autoDiscover = false;
	var bsurl = $('body').attr("bsurl");
	var prefixRoute = $('body').attr("prefixRoute");
	var cntFiles = null;
	var fm_dropzone = null;
	var acceptedFiles = "audio/*,image/*,application/pdf,application/docx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,video/*";
	$(document).ready(function() {
		function set_file(upload) {
			type = $("#image_selecter_origin_type").val();
			// upload = JSON.parse(upload);
			// console.log("upload sel: "+upload+" type: "+type);
			if(type == "file" || type == "image") {
				$hinput = $("input[name="+$("#image_selecter_origin").val()+"]");
				$hinput.val(upload.id).trigger('change');
				$hinput.closest(".btn-group").find("#"+$("#image_selecter_origin").val()+"-error").remove();
				$hinput.closest(".btn-group").find("a").addClass("d-none");
				if($hinput.closest(".btn-group").find(".uploaded_file").length) {
					$hinput.closest(".btn-group").find(".uploaded_file").remove();
				}
				$hinput.closest(".btn-group").append("<a class='uploaded_file text-wrap' upload_id='"+upload.id+"' title='"+upload.name+"' target='_blank' href='"+bsurl+"/files/"+upload.hash+"/"+upload.name+"'>"+htmlFile(upload)+"<i title='Remove File' class='fa fa-times'></i></a>");
			} else if(type == "files") {
				$hinput = $(`:input[name="${$("#image_selecter_origin").val()}[]"]`);
				if(isset($hinput.val()) && $hinput.val() != "") {
					var hiddenFIDs = $hinput.val();
				} else {
					var hiddenFIDs = [];
				}
				// check if upload_id exists in array
				var upload_id_exists = false;
				for (var key in hiddenFIDs) {
					if (hiddenFIDs.hasOwnProperty(key)) {
						var element = hiddenFIDs[key];
						if(element == upload.id) {
							upload_id_exists = true;
						}
					}
				}
				if(!upload_id_exists) {
					hiddenFIDs.push(upload.id);
					$hinput.closest(".btn-group").find("div.uploaded_files").append("<a class='uploaded_file2 d-inline-block position-relative my-1 mr-2 align-top text-wrap' upload_id='"+upload.id+"' title='"+upload.name+"' target='_blank' href='"+bsurl+"/files/"+upload.hash+"/"+upload.name+"'>"+htmlFile(upload)+"<i title='Remove File' class='fa fa-times'></i></a>");
				}
				var options_html = "";
				hiddenFIDs.forEach((value,index) => {
					options_html += `<option value="${value}" selected>${value}</option>`; 
				});
				$hinput.html(options_html).trigger('change');
			}
			$hinput.closest(".btn-group").find("a.btn.btn-default.btn-labeled").attr('disabled', false).find('.btn-label').html('<i class="fa fa-cloud-upload-alt"></i>');
		}
		function showLAFM(type, btn, extension = "") {
			$("#image_selecter_origin_type").val(type);
			$("#image_selecter_extension_type").val(extension);
			$("#image_selecter_origin").val($(btn).attr("selecter"));
			var image_public = $(btn).attr("image_public");
			if(typeof image_public != "undefined" && image_public == "0") {
				$(`:input[name="public"]`).prop('checked',false);
			}
			if(isset(extension) && extension == 'image') {
				acceptedFiles = "image/*";
			} else if(isset(extension) && extension == 'pdf') {
				acceptedFiles = "application/pdf";
			} else if(isset(extension) && extension == 'xls' || extension == 'xlsx') {
				acceptedFiles = "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
			} else if(isset(extension) && extension == 'csv') {
				acceptedFiles = '.csv';
			} else if(isset(extension) && extension == 'doc') {
				acceptedFiles = 'application/docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,text/plain,application/msword';
			} else if(isset(extension) && extension == 'audio') {
				acceptedFiles = 'audio/*';
			} else if(isset(extension) && extension == 'video') {
				acceptedFiles = 'video/*';
			} else {
				acceptedFiles = "audio/*,image/*,application/pdf,application/docx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/docx,text/plain,application/msword,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,video/*";
			}
			var ratio = $(btn).attr("ratio");
			if(typeof ratio != 'undefined' && ratio == "") {
				var ratio = $(btn).closest('.btn-group').find(':input[type="hidden"]').first().attr("ratio");
			}
			if(typeof ratio != 'undefined' && ratio != "") {
				if(typeof ratio == 'string') {
					var format = /[X,x]/;
					if(format.test(ratio)){
						var string = ratio.split("X");
						if(typeof string[1] == "undefined") {
							var string = ratio.split("x");
						}
						if(typeof string[1] != "undefined") {
							string1 = parseInt(string[0]);
							if(typeof string[1] != "undefined") {
								string2 = parseInt(string[1]);
								if(typeof string1 == 'number' && typeof string2 == 'number') {
									aspectRatio = (string1 / string2);
								}
							}
						}
					} else {
						console.log(format);
					}
				}else if(typeof ratio == 'number') {
					aspectRatio = ratio;
				}
				console.log(aspectRatio);
			}
			@if(config('stlc.file_manager_modal',false))
				$(".fm_file_selector ul").empty();
				loadFMFiles(extension);
				$("#fm").modal('show');
			@else
				$('#fm_dropzone .dz-message').trigger('click');
			@endif

			fm_dropzone.hiddenFileInput.accept = acceptedFiles;
			// Dropzone.Options.clickable = "#fm_dropzone .dz-message";
		}
		function getLI(upload) {
			return '<li class="list-inline-item"><a class="fm_file_sel d-inline-block position-relative my-1 align-top" data-toggle="tooltip" data-placement="top" title="'+upload.name+'" upload=\''+JSON.stringify(upload)+'\'>'+htmlFile(upload)+'</a></li>';
		}
		function loadFMFiles(type = "",url1 = "") {
			if(isset(url1) && url1 != "") {
			} else if(isset(type) && type != "") {
				url1 = "{{ url(config('stlc.stlc_route_prefix')) }}/uploaded_files?file_type="+type;
			} else {
				url1 = "{{ url(config('stlc.stlc_route_prefix')) }}/uploaded_files";
			}
			// load uploaded files
			$.ajax({
				dataType: 'json',
				url: url1,
				success: function ( json ) {
					// console.log(json);
					cntFiles = json.uploads.data;
					$('#fm .modal-footer').html(json.link);
					$(".fm_file_selector ul").empty();
					if(cntFiles.length) {
						for (var index = 0; index < cntFiles.length; index++) {
							var element = cntFiles[index];
							var li = getLI(element);
							$(".fm_file_selector ul").append(li);
						}
					} else {
						$(".fm_file_selector ul").html("<div class='text-center text-danger' style='margin-top:40px;'>No Files</div>");
					}
				}
			});
		}
		var aspectRatio = '1';
		var file_size_limit = "{{ config('stlc.file_upload_size') }}";
		var fm_dropzone = new Dropzone("#fm_dropzone", {
			maxFilesize: file_size_limit,
			acceptedFiles : acceptedFiles,
			chunking: true,
			chunkSize: 1000000,
			transformFile: function(file, done) {
				if(typeof file.type == 'string' && file.type.includes('image/')) {
					var myDropZone = this;
					var editor = document.createElement('div');
					editor.className = 'crop-editor';
					var confirm = document.createElement('button');
					confirm.textContent = 'Confirm';
					confirm.className = 'btn btn-success upload-confirm-btn';
					confirm.addEventListener('click', function() {
						var canvas = cropper.getCroppedCanvas();
						canvas.toBlob(function(blob) {
							myDropZone.createThumbnail(
								blob,
								myDropZone.options.thumbnailWidth,
								myDropZone.options.thumbnailHeight,
								myDropZone.options.thumbnailMethod,
								false, 
								function(dataURL) {
									myDropZone.emit('thumbnail', file, dataURL);
									done(blob);
								}
							);
						});
						editor.parentNode.removeChild(editor);
					});
					
					var close = document.createElement('button');
					close.textContent = 'close';
					close.className = 'btn btn-danger upload-close-btn';
					close.addEventListener('click', function() {
						editor.parentNode.removeChild(editor);
					});
					editor.appendChild(confirm);
					editor.appendChild(close);
					var image = new Image();
					image.src = URL.createObjectURL(file);
					editor.appendChild(image);
					document.body.appendChild(editor);
					var cropper = new Cropper(image, {
						aspectRatio: aspectRatio
					});
				} else {
					done(file);
				}
			},
			init: function() {
				this.on("complete", function(file) {
					if(file.size > (1024 * 1024 * file_size_limit)) {
						this.removeFile(file);
						sweetAlert("{{ trans('stlc.file_size_titel') }}","{{ trans('stlc.file_size_text') }}","warning");
					};
					this.removeFile(file);
				});
				this.on('uploadprogress', function(file, progress, bytesSent) {
					if(typeof file != 'undefined' && typeof file.xhr != "undefined" && typeof file.xhr.responseText != "undefined") {
						if(file.xhr.responseText != "") {
							var responsetext = JSON.parse(file.xhr.responseText);
							var progress = responsetext.done;
						}
					}
					if (file.previewElement) {
						for (var _iterator8 = file.previewElement.querySelectorAll("[data-dz-uploadprogress]"), _isArray8 = true, _i8 = 0, _iterator8 = _isArray8 ? _iterator8 : _iterator8[Symbol.iterator]();;) {
							var _ref7;
							if (_isArray8) {
								if (_i8 >= _iterator8.length) break;
								_ref7 = _iterator8[_i8++];
							} else {
								_i8 = _iterator8.next();
								if (_i8.done) break;
								_ref7 = _i8.value;
							}
							var node = _ref7;
							node.nodeName === 'PROGRESS' ? node.value = progress : node.style.width = progress + "%";
						}
						set_file_progress(Math.round(parseInt(progress)));
					}
				});
				this.on("success", function(file, response) {
					if(typeof file != 'undefined' && typeof file.xhr != "undefined" && typeof file.xhr.responseText != "undefined") {
						response = JSON.parse(file.xhr.responseText);
						if(response.status == 'success') {
							set_file(response.upload);
							set_file_progress(100,true);
						}
					} else {
						if(response.status == 'success') {
							set_file(response.upload);
							set_file_progress();
						}
					}
				});
				this.on('error', function(file, response) {
					$(file.previewElement).find('.dz-error-message').text(response);
				});
			}
		});

		function set_file_progress(progress = 100,remove = false) {
			var type = $("#image_selecter_origin_type").val();
			@if(config('stlc.file_manager_modal',false))
				$("#fm").modal('hide');
			@endif
			if(type == "files") {
				$hinput = $(`:input[name="${$("#image_selecter_origin").val()}[]"]`);
			} else {
				$hinput = $("input[name="+$("#image_selecter_origin").val()+"]");
			}
			var progress_html = $hinput.closest('.form-group').find('.progress');
			if(remove) {
				$(progress_html).remove();
			} else if(typeof progress_html != 'undefined' && progress_html.length > 0) {
				$(progress_html).find(".progress-bar").css('width',progress + "%").attr('aria-valuenow',progress).text(progress + "%");
			} else {
				$hinput.closest('.form-group').append(`
					<div class="progress">
						<div class="progress-bar" role="progressbar" style="width: ${progress}%;" aria-valuenow="${progress}" aria-valuemin="0" aria-valuemax="100">${progress}</div>
					</div>
				`);
			}
		}

		function htmlFile(upload) {
			var image = "";
			if($.inArray(upload.extension, ["jpg","JPG","jpeg", "png", "gif", "bmp"]) > -1) {
				image = '<img src="'+bsurl+'/files/'+upload.hash+'/'+upload.name+'?s=100" class="card-img-top">';
			} else if($.inArray(upload.extension, ["ogg",'wav','mp3']) > -1) {
				image = `<i class="far fa-file-audio fa-7x text-warning"></i>`;
			} else if($.inArray(upload.extension, ["mp4","WEBM","MPEG","AVI","WMV","MOV","FLV","SWF"]) > -1) {
				image = `<i class="far fa-file-video fa-7x text-success"></i>`;
			} else {
				switch (upload.extension) {
					case "pdf":
					image = '<i class="far fa-file-pdf fa-7x text-danger"></i>';
					break;
				case "xls":
					image = '<i class="far fa-file-excel fa-7x text-success"></i>';
					break;
				case "docx":
					image = '<i class="far fa-file-word fa-7x"></i>';
					break;
				case "xlsx":
					image = '<i class="far fa-file-excel fa-7x text-success"></i>';
					break;
				case "csv":
					image = '<span class="fa-stack" style="color: #31A867 !important;">';
					image += '<i class="far fa-file-alt fa-stack-2x"></i>';
					image += '<strong class="fa-stack-1x">CSV</strong>';
					image += '</span>';
					break;
				default:
					image = '<i class="far fa-file-alt fa-7x"></i>';
					break;
				}
			}
			return `<div class="card text-center m-0">
					${image}
				<div class="card-body p-1">
					<p class="card-text">${upload.name.substring(0,10)}${upload.name.length > 10 ? '..' : ""}</p>
				</div>
			</div>`;
		}

		$("#fm input[type=search]").keyup(function () {
			var sstring = $(this).val().trim();
			if(sstring != "") {
				$(".fm_file_selector ul").empty();
				for (var index = 0; index < cntFiles.length; index++) {
					var upload = cntFiles[index];
					if(upload.name.toUpperCase().includes(sstring.toUpperCase())) {
						$(".fm_file_selector ul").append(getLI(upload));
					}
				}
			} else {
				loadFMFiles($("#image_selecter_extension_type").val());
			}
		});
		$('#fm .modal-footer').on('click','a.page-link',function(e){
			e.preventDefault();
			loadFMFiles($("#image_selecter_extension_type").val(),this.href);
		});
		$(".btn_upload_file").on("click", function() {
			if(isset($(this).attr("extension"))) {
				var extension = $(this).attr("extension");
			} else {
				var extension = $(this).attr("file_type");
			}
			var type = $(this).attr("file_type");
			showLAFM(type, this, extension);
			$(this).attr('disabled', true).find('.btn-label').html('<i class="fa fa-circle-notch fa-spin"></i>');
		});

		$("a.profile-pic[file_type='image']").on("click", function() {
			$('.dz-message').attr('selecter', $(this).attr("selecter"));
			$('.dz-message').trigger('click');
		});
		
		$("body").on("click", '.uploaded_file i.fa.fa-times', function(e) {
			e.preventDefault();
			$(this).closest(".btn-group").find('.btn_upload_file').removeClass("d-none");
			$(this).closest(".btn-group").find('input[type="hidden"]').val(null).trigger('change');
			$(this).closest('a').remove();
		});

		$("body").on("click", ".uploaded_file2 i.fa.fa-times",function(e) {
			e.preventDefault();
			var upload_id = $(this).closest(".uploaded_file2").attr("upload_id");
			var $hiddenFIDs = $(this).closest(".btn-group").find('select');
			var hiddenFIDs = $hiddenFIDs.val();
			
			var options_html = "";
			hiddenFIDs.forEach((value,index) => {
				if(upload_id != value) {
					console.log(value,upload_id);
					options_html += `<option value="${value}" selected>${value}</option>`; 
				}
			});
			$hiddenFIDs.html(options_html);
			$(this).closest('a').remove();
		});

		$('.fm_file_selector').on('click','.fm_file_sel',function() {
			$("#fm").modal('hide');
			set_file(JSON.parse($(this).attr('upload')));
		});
	});
</script>
@endpush
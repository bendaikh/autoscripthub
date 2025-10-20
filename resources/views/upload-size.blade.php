@php
if($demo_mode == 'on'){ $maxupsizee = 1; } else { $maxupsizee = $available_storage; }
@endphp
<script>
    var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

    Dropzone.autoDiscover = false;
    var myDropzone = new Dropzone(".dropzone",{ 
        maxFilesize: {{ $maxupsizee }},  // 1000 mb
        acceptedFiles: "{{ $addition_settings->item_file_extension }}",
		addRemoveLinks: true,
		removedfile: function(file) 
            {
                var name = file.upload.filename;
                $.ajax({
                    headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            },
                    type: 'POST',
                    url: '{{ url("/file-delete") }}',
                    data: {filename: name, item_token: '{{ $item_token }}'},
                    success: function (data){
                        console.log("File deleted successfully!!");
						$('#display_message').html(data.record);
						
						
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
		/*maxFiles: 1,*/
		/*addRemoveLinks: true,*/
    });
    myDropzone.on("sending", function(file, xhr, formData) {
	   
       formData.append("_token", CSRF_TOKEN);
	   
	   
         
    }); 
	myDropzone.on("error", function(file, response) {
    console.log(response);
});

// on success
myDropzone.on("success", function(file, response) {
    // Store current values BEFORE any HTML replacement
	var currentFileType = $('#file_type1').val();
	var currentFileLink = $('#item_file_link1').val();
	var currentVideoUrl = $('#video_url1').val();
	var currentVideoPreviewType = $('#video_preview_type1').val();
	
    // get response from successful ajax request
    $('#hide_message').hide();
	$('#display_message').html(response.record);
	
	// Use setTimeout to ensure the HTML replacement is complete before restoring values
	setTimeout(function() {
		// Prefer mapping values into newly injected fields (suffix 2) if they exist
		if ($('#file_type2').length) {
			if (currentFileType && currentFileType !== '') {
				$("#file_type2").val(currentFileType).trigger('change');
			}
		} else if (currentFileType && currentFileType !== '') {
			$("#file_type1").val(currentFileType).trigger('change');
		}
		
		if ($('#item_file_link2').length) {
			if (currentFileLink && currentFileLink !== '') {
				$('#item_file_link2').val(currentFileLink);
			}
		} else if (currentFileLink && currentFileLink !== '') {
			$('#item_file_link1').val(currentFileLink);
		}
		
		if ($('#video_preview_type2').length) {
			if (currentVideoPreviewType && currentVideoPreviewType !== '') {
				$('#video_preview_type2').val(currentVideoPreviewType).trigger('change');
			}
			// Only reset video url if we had one
			$('#video_url2').val('');
		} else {
			$('#video_url1').val('');
			$("#video_preview_type1").val('');
		}
	}, 100);
	
    // submit the form after images upload
    // (if u want yo submit rest of the inputs in the form)
    //document.getElementById("dropzone-form").submit();
});
</script>
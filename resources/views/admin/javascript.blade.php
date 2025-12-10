<script src="{{ asset('admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('admin/vendors/popper.js/dist/umd/popper.min.js') }}"></script>
<script src="{{ asset('admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/main.js') }}"></script>
<script src="{{ asset('admin/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('admin/vendors/chart.js/dist/Chart.bundle.min.js') }}"></script>
<!--<script src="{{ asset('admin/assets/js/init-scripts/chart-js/chartjs-init.js') }}"></script>-->
<script src="{{ asset('admin/assets/js/dashboard.js') }}"></script>
<script src="{{ asset('admin/assets/js/widgets.js') }}"></script>
<script src="{{ asset('admin/vendors/jqvmap/dist/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
<script src="{{ asset('admin/vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
<script>
        (function($) {
            'use strict';
            // Only initialize vmap if element exists
            if (jQuery('#vmap').length > 0) {
                jQuery('#vmap').vectorMap({
                    map: 'world_en',
                    backgroundColor: null,
                    color: '#ffffff',
                    hoverOpacity: 0.7,
                    selectedColor: '#1de9b6',
                    enableZoom: true,
                    showTooltip: true,
                    values: sample_data,
                    scaleColors: ['#1de9b6', '#03a9f5'],
                    normalizeFunction: 'polynomial'
                });
            }
        })(jQuery);
	</script>
<script src="{{ asset('admin/vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
@if($view_name != 'admin.upgrade')
<script src="{{ asset('admin/vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
@endif
<script src="{{ asset('admin/vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/vendors/jszip/dist/jszip.min.js') }}"></script>
<script src="{{ asset('admin/vendors/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin/vendors/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin/vendors/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('admin/assets/js/init-scripts/data-table/datatables-init.js') }}"></script>
<?php /*?><script src="{{ asset('vendor/unisharp/laravel-ckeditor/ckeditor.js') }}"></script>
	<script>
        CKEDITOR.replace( 'summary-ckeditor' );
		 CKEDITOR.replace( 'summary-ckeditor2' );
		CKEDITOR.replace( 'summary-ckeditor3' );
		
    </script><?php */?>
<script src="{{url('vendor/tinymce/jquery.tinymce.min.js')}}"></script>
<script src="{{url('vendor/tinymce/tinymce.min.js')}}"></script>
<script>
  tinymce.init({
    
	selector: '#summary-ckeditor', 
    
	
	image_class_list: [
            {title: 'Responsive', value: 'img-fluid'},
            ],
			width: '100%',
            height: 480,
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
            },
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste imagetools"
            ],
            toolbar: [
		'newdocument | print preview | searchreplace | undo redo | link unlink anchor image media | alignleft aligncenter alignright alignjustify | code',
		'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor',
		'removeformat | hr pagebreak | charmap subscript superscript insertdatetime | bullist numlist | outdent indent blockquote | table'
	    ],
            menubar : false,
            image_title: true,
            automatic_uploads: true,
            images_upload_url: "{{ url('/admin/upload') }}",
            file_picker_types: 'image',
			relative_urls : false,
			remove_script_host : false,
			convert_urls : false,
			branding: false,
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                };
                input.click();
            }
    
 
  
  });
  
  tinymce.init({
    
	selector: '#summary-ckeditor2', 
    
		image_class_list: [
            {title: 'Responsive', value: 'img-fluid'},
            ],
			width: '100%',
            height: 480,
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
            },
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste imagetools"
            ],
            toolbar: [
		'newdocument | print preview | searchreplace | undo redo | link unlink anchor image media | alignleft aligncenter alignright alignjustify | code',
		'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor',
		'removeformat | hr pagebreak | charmap subscript superscript insertdatetime | bullist numlist | outdent indent blockquote | table'
	    ],
            menubar : false,
            image_title: true,
            automatic_uploads: true,
            images_upload_url: "{{ url('/admin/upload') }}",
            file_picker_types: 'image',
			relative_urls : false,
			remove_script_host : false,
			convert_urls : false,
			branding: false,
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                };
                input.click();
            }
    
    
 
  
  });
  
  tinymce.init({
    
	selector: '#summary-ckeditor3', 
    
		image_class_list: [
            {title: 'Responsive', value: 'img-fluid'},
            ],
			width: '100%',
            height: 480,
            setup: function (editor) {
                editor.on('init change', function () {
                    editor.save();
                });
            },
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste imagetools"
            ],
            toolbar: [
		'newdocument | print preview | searchreplace | undo redo | link unlink anchor image media | alignleft aligncenter alignright alignjustify | code',
		'formatselect fontselect fontsizeselect | bold italic underline strikethrough | forecolor backcolor',
		'removeformat | hr pagebreak | charmap subscript superscript insertdatetime | bullist numlist | outdent indent blockquote | table'
	    ],
            menubar : false,
            image_title: true,
            automatic_uploads: true,
            images_upload_url: "{{ url('/admin/upload') }}",
            file_picker_types: 'image',
			relative_urls : false,
			remove_script_host : false,
			convert_urls : false,
			branding: false,
            file_picker_callback: function(cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function() {
                    var file = this.files[0];

                    var reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                };
                input.click();
            }
    

    
 
  
  });
  
  </script>
<script src="{{ asset('admin/assets/js/custom.js') }}"></script>
<script type="text/javascript">
	$(document).ready(function(){
	'use strict';
	$("#subcategory_allow_seo").change(function () {
            if ($(this).val() == "1") {
                $("#ifseo").show();
            } else {
                $("#ifseo").hide();
            }
        });	
	$("#category_allow_seo").change(function () {
            if ($(this).val() == "1") {
                $("#ifseo").show();
            } else {
                $("#ifseo").hide();
            }
        });		
     $("#page_allow_seo").change(function () {
            if ($(this).val() == "1") {
                $("#ifseo").show();
            } else {
                $("#ifseo").hide();
            }
        });
	$("#post_allow_seo").change(function () {
            if ($(this).val() == "1") {
                $("#ifseo").show();
            } else {
                $("#ifseo").hide();
            }
        });	
	$("#site_extra_fee_type").change(function () {
            if ($(this).val() == "fixed") {
                $("#iffixed").show();
				$("#ifpercentage").hide();
            } else {
                $("#ifpercentage").show();
				$("#iffixed").hide();
            }
        });
	$("#per_sale_referral_commission_type").change(function () {
            if ($(this).val() == "fixed") {
                $("#nfixed").show();
				$("#npercentage").hide();
            } else {
                $("#npercentage").show();
				$("#nfixed").hide();
            }
        });		
		
});
</script>
<script src="{{ asset('admin/dragdrop/js/jquery.filer.min.js') }}" type="text/javascript"></script>
<?php /*?><script src="{{ asset('resources/views/admin/template/dragdrop/js/custom.js') }}" type="text/javascript"></script><?php */?>
<script src="{{ asset('theme/validate/jquery.bvalidator.min.js') }}"></script>
<script src="{{ asset('theme/validate/themes/presenters/default.min.js') }}"></script>
<script src="{{ asset('theme/validate/themes/red/red.js') }}"></script>
<link href="{{ asset('theme/validate/themes/red/red.css') }}" rel="stylesheet" />
<script type="text/javascript">
    $(document).ready(function () {
        'use strict';
		var options = {
		
		offset:              {x:5, y:-2},
		position:            {x:'left', y:'center'},
        themes: {
            'red': {
                 showClose: true
            },
		
        }
    };

    $('#item_form').bValidator(options);
	$('#profile_form').bValidator(options);
	$('#comment_form').bValidator(options);
	$('#support_form').bValidator(options);
	$('#order_form').bValidator(options);
	$('#checkout_form').bValidator(options);
	$('#setting_form').bValidator(options);
	$('#category_form').bValidator(options);
    });
</script>
<script src="{{ asset('admin/font-select/jquery.fontselect.js') }}"></script>
    <script>
      $(function(){
        
		$('#theme_font_family').fontselect().change(function(){
        
          // replace + signs with spaces for css
          var font = $(this).val().replace(/\+/g, ' ');
          
          // split font into family and weight
          font = font.split(':');
          
          // set family on paragraphs 
          $('#paragraph7').css('font-family', font[0]);
        });
		
	});
</script>
<script type="text/javascript" src="{{ asset('admin/datepicker/picker.js') }}"></script>
<script>
  $( function() {
  'use strict'; 
    $( "#site_flash_end_date" ).datepicker({ minDate: 0, dateFormat: 'yy-mm-dd' });
	$( "#site_free_end_date" ).datepicker({ minDate: 0, dateFormat: 'yy-mm-dd' });
	$( "#from_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#to_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
	$( "#pr_promo_date" ).datepicker({ minDate: 0, dateFormat: 'yy-mm-dd' });
  } );
  
  
  $(document).ready(function(){
    'use strict';
    $('#select_all').on('click',function(){
        if(this.checked){
            $('.checkbox').each(function(){
                this.checked = true;
            });
        }else{
             $('.checkbox').each(function(){
                this.checked = false;
            });
        }
    });
    
    $('.checkbox').on('click',function(){
        if($('.checkbox:checked').length == $('.checkbox').length){
            $('#select_all').prop('checked',true);
        }else{
            $('#select_all').prop('checked',false);
        }
    });
});
$(document).ready(function(){
	'use strict';
	$("#hint_comma").hide();
	$("#hint_line").hide();	
    $('#subscr_item_level').on('change', function() {
      if ( this.value == 'limited')
      
      {
        $("#limit_item").show();
		
      }
	  
      else
      {
        $("#limit_item").hide();
      }
    });
	$('#subscr_space_level').on('change', function() {
      if ( this.value == 'limited')
      
      {
        $("#limit_space").show();
		
      }
	  
      else
      {
        $("#limit_space").hide();
      }
    });
	$('#seller_money_back').on('change', function() {
      if ( this.value == '1')
      {
        $("#back_money").show();
      }
      else
      {
        $("#back_money").hide();
      }
    });
	
	$('#file_type1').on('change', function() {
      if ( this.value == 'file')
      {
        $("#main_file").show();
		$("#main_link").hide();
		$("#main_delimiter").hide();
		$("#main_serials").hide();
		
      }
      else if(this.value == 'link')
      {
        $("#main_file").hide();
		$("#main_link").show();
		$("#main_delimiter").hide();
		$("#main_serials").hide();
      }
	  else if(this.value == 'serial')
	  {
	    $("#main_file").hide();
		$("#main_link").hide();
		$("#main_delimiter").show();
		$("#main_serials").show();
		$("#free_download option[value='0']").prop('selected', true); 
		$("#item_support option[value='1']").prop('selected', true);
	  }
	  else
	  {
	    $("#main_file").hide();
		$("#main_link").hide();
		$("#main_delimiter").hide();
		$("#main_serials").hide();
	  }
    });
	$('#item_delimiter1').on('change', function() {
      if ( this.value == 'comma')
      {
	     $("#hint_comma").show();
		 $("#hint_line").hide();
	  }
	  else if ( this.value == 'newline')
	  {
	     $("#hint_comma").hide();
		 $("#hint_line").show();
	  }
	  else
	  {
	     $("#hint_comma").hide();
		 $("#hint_line").hide();
	  }
	 }); 
	$('#free_download').on('change', function() {
      if ( this.value == '0')
      {
	    $("#item_support option[value='1']").prop('selected', true);
        $("#pricebox").show();
		$("#pricebox_left").show();
		$("#pricebox_right").show();
		$("#subscription_box").show();
      }
      else
      {
	    
		$("#item_support option[value='0']").prop('selected', true); 
		$("#pricebox").hide();
		$("#pricebox_left").hide();
		$("#pricebox_right").hide();
		$("#subscription_box").hide();
		
      }
    });
	$('#item_support').on('change', function() {
      if ( this.value == '1')
      {
	    $("#free_download option[value='0']").prop('selected', true); 
        $("#pricebox").show();
		$("#pricebox_left").show();
		$("#pricebox_right").show();
		$("#subscription_box").show();
      }
      else
      {
	    
		$("#free_download option[value='1']").prop('selected', true);  
        $("#pricebox").hide();
		$("#pricebox_left").hide();
		$("#pricebox_right").hide();
		$("#subscription_box").hide();
      }
    });
	$("#watermark_repeat").change(function () {
		
		    if ($(this).val() == "0") 
			{
                
				$("#ifwatermark").show();
				
            }
			else
			{
			   $("#ifwatermark").hide();
			}
		
		});
	
	$('#highlight_pack').on('change', function() {
      if ( this.value == '1')
      {
	    
        $("#highbox1").show();
		
      }
      else
      {
	    
		$("#highbox1").hide();
		
      }
    });
	
});
</script>
<script src="{{ asset('admin/dropzone/min/dropzone.min.js')}}" type="text/javascript"></script>
<!-- version links manager -->
<script src="{{ asset('theme/js/version-links.js') }}" type="text/javascript"></script>
<script src="{{ asset('admin/lazy/jquery.lazyload.js?v=1.9.1') }}"></script>
<script type="text/javascript" charset="utf-8">
  $(function() {
     'use strict';
     $("img.lazy").lazyload();

  });
</script>
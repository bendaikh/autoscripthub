<!doctype html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en">
<!--<![endif]-->

<head>
    
    @include('admin.stylesheet')
</head>

<body>
    
    @include('admin.navigation')

    <!-- Right Panel -->
    @if(in_array('items',$avilable))
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       

        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1>{{ __('Items') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        
                        <ol class="breadcrumb text-right">
                            
                            
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
         @include('admin.warning')
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-md-12 text-right">
                    <form action="{{ route('admin.items') }}" method="post" id="setting_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group m-moves">
                    <input id="search" name="search" type="text" class="move-bars" value="{{ $search }}" placeholder="{{ __('Item Name') }}">
                    
                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Search
                    </button>
                    
                    </div>
                    </form>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12  text-right">
                    @if($demo_mode == 'on')
                     @include('admin.demo-mode')
                     @else
                     <form action="{{ route('admin.trashs') }}" method="post" id="category_form" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     @endif
                    <div class="m-moves mb-3">
                    <a href="{{ url('/admin/products-import-export') }}" class="btn btn-primary btn-sm"><i class="fa fa-file-excel-o"></i> {{ __('Product Import / Export') }}</a>&nbsp;
                            <a href="{{ url('/admin/trash-items') }}" class="btn btn-danger btn-sm"><i class="fa fa-location-arrow"></i> {{ __('Trash Items') }}</a>
                            &nbsp;
                            <a onClick="myFunction()" class="btn btn-success btn-sm dropbtn text-white"><i class="fa fa-plus"></i> {{ __('Add Item') }}</a>
                            <div id="myDropdown" class="dropdown-content">
                                @foreach($viewitem['type'] as $item_type)
                                @php $encrypted = $encrypter->encrypt($item_type->item_type_id); @endphp
                                <a href="{{ URL::to('/admin/upload-item') }}/{{ $encrypted }}">{{ $item_type->item_type_name }}</a>
                                @endforeach
                            </div>
                            <input type="submit" value="{{ __('Trash All') }}" name="submit" class="btn btn-danger btn-sm ml-1" id="checkBtn" onClick="return confirm('{{ __('Are you sure you want to remove') }}?');">
                       </div> 
                       
                    <div class="col-md-12 text-left">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Items') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="example" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>{{ __('Sno') }}</th>
                                            <th width="50">{{ __('Item Image') }}</th>
                                            <th width="100">{{ __('Item Name') }}</th>
                                            <th  width="100">{{ __('Featured Item') }}?</th>
                                            <th  width="40">{{ __('Free Item') }}?</th>
                                            <th>{{ __('Flash Request') }}?</th>
                                            <th>{{ __('Subscription Item') }}?</th>
                                            <th>{{ __('Comments') }}</th>
                                            <th>{{ __('Vendor') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($itemData['item'] as $item)
                                        <tr class="allChecked">
                                            <td><input type="checkbox" name="item_id[]" value="{{ $item->item_token }}"/>
                                            </td>
                                            <td>{{ $no }}</td>
                                            <td>@if($item->item_thumbnail != '') <img class="lazy" width="50" height="50" src="{{ Helper::Image_Path($item->item_thumbnail,'no-image.png') }}"  alt="{{ $item->item_name }}"/>@else <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $item->item_name }}" />  @endif</td>
                                            <td><a href="{{ url('/item') }}/{{ $item->item_slug }}" target="_blank" class="black-color">{{ mb_substr($item->item_name, 0, 50, 'UTF-8') }}</a></td>
                                            <td>@if($item->item_featured == 'no') {{ __('No') }} @else {{ __('Yes') }} @endif <a href="items/{{ $item->item_featured }}/{{ $item->item_token }}" style="font-size:12px; color:#0000FF; text-decoration:underline;">{{ __('Can you change') }}</a></td>
                                            <td>@if($item->free_download == 1) <span class="badge badge-success">{{ __('Yes') }}</span> @else <span class="badge badge-danger">{{ __('No') }}</span> @endif</td>
                                            <td>@if($item->item_flash_request == 1) @if($item->item_flash == 0) <span class="badge badge-danger">{{ __('Waiting for approval') }}</span> @else <span class="badge badge-success">{{ __('Approved') }}</span> @endif @else <span>---</span> @endif</td>
                                            <td>@if($item->subscription_item == 1) <span class="badge badge-success">{{ __('Yes') }}</span> @else <span class="badge badge-danger">{{ __('No') }}</span> @endif</td>
                                            <td><a href="{{ url('/admin') }}/item-comment/{{ $item->item_id }}" class="blue-color">{{ __('Comments') }} [{{ $comments->has($item->item_id) ? count($comments[$item->item_id]) : 0 }}]</a></td>
                                            <td><a href="{{ url('/user') }}/{{ $item->username }}" target="_blank" class="black-color">{{ $item->username }}</a></td>
                                            <td>@if($item->item_status == 1) <span class="badge badge-success">{{ __('Approved') }}</span> @elseif($item->item_status == 2) <span class="badge badge-danger">{{ __('Rejected') }}</span> @else <span class="badge badge-warning">{{ __('UnApproved') }}</span> @endif</td>
                                            <td>
                                            <a href="{{ url('/admin') }}/edit-item/{{ $item->item_token }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i>&nbsp; {{ __('Edit') }}</a> 
                                            @if(isset($item->file_type) && $item->file_type == 'link')
                                            <button type="button" class="btn btn-info btn-sm manage-versions-btn" data-item-token="{{ $item->item_token }}" data-item-name="{{ $item->item_name }}">
                                                <i class="fa fa-list"></i>&nbsp;{{ __('Versions') }}
                                            </button>
                                            @endif
                                            @if($demo_mode == 'on') 
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;{{ __('Trash') }}</a>
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-primary btn-sm"><i class="fa fa-download"></i>&nbsp;{{ __('Download Item') }}</a>
                                            @else
                                            <a href="{{ url('/admin') }}/items/{{ $item->item_token }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ __('Are you sure you want to remove') }}?');"><i class="fa fa-trash"></i>&nbsp;{{ __('Trash') }}</a>
                                            <a href="{{ url('/admin') }}/download/{{ $item->item_token }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i>&nbsp;{{ __('Download') }}</a>
                                            @endif
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                   @endforeach  
                                    </tbody>
                                </table>
                                <div>
                                {{ $itemData['item']->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                   </form>
                   </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->


    </div><!-- /#right-panel -->
    @else
    @include('admin.denied')
    @endif
    <!-- Right Panel -->

    <!-- Version Management Modal -->
    <div class="modal fade" id="versionsModal" tabindex="-1" role="dialog" aria-labelledby="versionsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="versionsModalLabel">
              <i class="fa fa-list"></i> Manage Versions: <span id="modal-item-name"></span>
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="modal-item-token" value="">
            
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> Add or edit version links for this item. Leave version name empty if not needed.
            </div>
            
            <div id="modal-versions-container">
              <!-- Version blocks will be dynamically added here -->
            </div>
            
            <div class="text-center mt-3">
              <button type="button" class="btn btn-primary" id="add-modal-version-btn">
                <i class="fa fa-plus"></i> Add Version
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              <i class="fa fa-times"></i> Close
            </button>
            <button type="button" class="btn btn-success" id="save-versions-btn">
              <i class="fa fa-save"></i> Save Versions
            </button>
          </div>
        </div>
      </div>
    </div>

   @include('admin.javascript')
   <script type="text/javascript">
      $(document).ready(function () { 
    var oTable = $('#example').dataTable({
        /*stateSave: true,*/	
		searching: false,												  
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        paging: false,
    });

    var allPages = oTable.fnGetNodes();

    $('body').on('click', '#selectAll', function () {
        if ($(this).hasClass('allChecked')) {
            $('input[type="checkbox"]', allPages).prop('checked', false);
        } else {
            $('input[type="checkbox"]', allPages).prop('checked', true);
        }
        $(this).toggleClass('allChecked');
    })
});

$(document).ready(function () {
    $('#checkBtn').click(function() {
      checked = $("input[type=checkbox]:checked").length;

      if(!checked) {
        alert("You must check at least one checkbox.");
        return false;
      }

    });
	
	
	
	});
	
	// Version Management Modal JavaScript
	$(document).ready(function() {
	    // Open modal when "Versions" button is clicked
	    $('.manage-versions-btn').on('click', function() {
	        var itemToken = $(this).data('item-token');
	        var itemName = $(this).data('item-name');
	        
	        $('#modal-item-token').val(itemToken);
	        $('#modal-item-name').text(itemName);
	        
	        // Load existing versions
	        loadVersions(itemToken);
	        
	        // Show modal - Simple and compatible
	        $('#versionsModal').fadeIn().css('display', 'block').addClass('show');
	        $('body').addClass('modal-open');
	        $('.modal-backdrop').remove();
	        $('<div class="modal-backdrop fade show"></div>').appendTo('body');
	    });
	    
	    // Add version block button
	    $('#add-modal-version-btn').on('click', function() {
	        addVersionBlock();
	    });
	    
	    // Save versions button
	    $('#save-versions-btn').on('click', function() {
	        saveVersions();
	    });
	    
	    // Remove version block (delegated event)
	    $('#modal-versions-container').on('click', '.remove-modal-version-btn', function() {
	        var container = $('#modal-versions-container');
	        var blocks = container.find('.modal-version-block');
	        
	        if(blocks.length > 1) {
	            $(this).closest('.modal-version-block').remove();
	        } else {
	            // Clear values if it's the last block
	            $(this).closest('.modal-version-block').find('input').val('');
	        }
	    });
	    
	    // Close button handlers
	    $('#versionsModal .close, #versionsModal [data-dismiss="modal"]').on('click', function() {
	        $('#versionsModal').fadeOut().removeClass('show');
	        $('body').removeClass('modal-open');
	        $('.modal-backdrop').remove();
	    });
	    
	    // Close on backdrop click
	    $(document).on('click', '.modal-backdrop', function() {
	        $('#versionsModal').fadeOut().removeClass('show');
	        $('body').removeClass('modal-open');
	        $('.modal-backdrop').remove();
	    });
	});
	
	function loadVersions(itemToken) {
	    // Show loading state
	    $('#modal-versions-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
	    
	    // AJAX request to get versions
	    $.ajax({
	        url: '{{ url("/admin/get-item-versions") }}/' + itemToken,
	        type: 'GET',
	        success: function(response) {
	            $('#modal-versions-container').empty();
	            
	            if(response.success && response.versions && response.versions.length > 0) {
	                // Display existing versions
	                response.versions.forEach(function(version) {
	                    addVersionBlock(version.version, version.url);
	                });
	            } else {
	                // Add one empty block
	                addVersionBlock('', '');
	            }
	        },
	        error: function() {
	            $('#modal-versions-container').html('<div class="alert alert-danger">Error loading versions</div>');
	            addVersionBlock('', '');
	        }
	    });
	}
	
	function addVersionBlock(versionName = '', versionUrl = '') {
	    var blockHtml = `
	        <div class="modal-version-block" style="margin-bottom: 15px; padding: 15px; border: 1px solid #e0e0e0; border-radius: 4px; background-color: #f9f9f9;">
	            <div class="row">
	                <div class="col-md-4">
	                    <div class="form-group">
	                        <label>Version Name</label>
	                        <input type="text" class="form-control version-name-input" placeholder="e.g. v1.0, v2.0" value="${escapeHtml(versionName)}">
	                    </div>
	                </div>
	                <div class="col-md-7">
	                    <div class="form-group">
	                        <label>File URL <span style="color: red;">*</span></label>
	                        <input type="text" class="form-control version-url-input" placeholder="https://example.com/file.zip" value="${escapeHtml(versionUrl)}">
	                    </div>
	                </div>
	                <div class="col-md-1">
	                    <label>&nbsp;</label>
	                    <button type="button" class="btn btn-danger btn-block remove-modal-version-btn" title="Remove this version">
	                        <i class="fa fa-trash"></i>
	                    </button>
	                </div>
	            </div>
	        </div>
	    `;
	    
	    $('#modal-versions-container').append(blockHtml);
	}
	
	function saveVersions() {
	    var itemToken = $('#modal-item-token').val();
	    var versions = [];
	    
	    // Collect all version data
	    $('#modal-versions-container .modal-version-block').each(function() {
	        var versionName = $(this).find('.version-name-input').val();
	        var versionUrl = $(this).find('.version-url-input').val();
	        
	        if(versionUrl.trim() !== '') {
	            versions.push({
	                version: versionName.trim(),
	                url: versionUrl.trim()
	            });
	        }
	    });
	    
	    // Validate
	    if(versions.length === 0) {
	        alert('Please add at least one version with a URL');
	        return;
	    }
	    
	    // Disable save button
	    var saveBtn = $('#save-versions-btn');
	    saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
	    
	    // AJAX request to save
	    $.ajax({
	        url: '{{ url("/admin/save-item-versions") }}',
	        type: 'POST',
	        data: {
	            _token: '{{ csrf_token() }}',
	            item_token: itemToken,
	            versions: versions
	        },
	        success: function(response) {
	            if(response.success) {
	                alert('Versions saved successfully!');
	                // Close modal - Simple and compatible
	                $('#versionsModal').fadeOut().removeClass('show');
	                $('body').removeClass('modal-open');
	                $('.modal-backdrop').remove();
	                // Optionally reload the page
	                // location.reload();
	            } else {
	                alert('Error: ' + (response.message || 'Failed to save versions'));
	            }
	        },
	        error: function() {
	            alert('Error saving versions. Please try again.');
	        },
	        complete: function() {
	            saveBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Save Versions');
	        }
	    });
	}
	
	function escapeHtml(text) {
	    if(!text) return '';
	    var map = {
	        '&': '&amp;',
	        '<': '&lt;',
	        '>': '&gt;',
	        '"': '&quot;',
	        "'": '&#039;'
	    };
	    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
	}

</script>

</body>

</html>

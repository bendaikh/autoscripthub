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
                        <h1>{{ __('Trash Items') }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        
                        <ol class="breadcrumb text-right">
                            <a href="{{ url('/admin/items') }}" class="btn btn-success btn-sm"><i class="fa fa-chevron-left"></i> {{ __('Back') }}</a>
                            
                            
                            
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
                    <form action="{{ route('admin.trash-items') }}" method="post" id="setting_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                    <input id="search" name="search" type="text" class="move-bars" value="{{ $search }}" placeholder="{{ __('Item Name') }}">
                    
                    <button type="submit" name="submit" class="btn btn-primary btn-sm">
                    <i class="fa fa-dot-circle-o"></i> Search
                    </button>
                    
                    </div>
                    </form>
                    </div>
                    <div class="col-md-12">
                     @if($demo_mode == 'on')
                     @include('admin.demo-mode')
                     @else
                     <form action="{{ route('admin.all-trash') }}" method="post" id="category_form" enctype="multipart/form-data">
                     {{ csrf_field() }}
                     @endif
                     <div align="right">
                     <input type="submit" value="{{ __('Delete All Permanently') }}" name="submit" class="btn btn-danger btn-sm ml-1" id="checkBtn" onClick="return confirm('{{ __('Are you sure you want to permanently delete') }}?');">
                     </div>
                    
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Trash Items') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="example" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>{{ __('Sno') }}</th>
                                            <th>{{ __('Item Image') }}</th>
                                            <th>{{ __('Item Name') }}</th>
                                            <th>{{ __('Free Item') }}?</th>
                                            <th>{{ __('Flash Request') }}?</th>
                                            <th>{{ __('Vendor') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($itemData['item'] as $item)
                                        <tr>
                                            <td><input type="checkbox" name="item_id[]" value="{{ $item->item_token }}"/></td>
                                            <td>{{ $no }}</td>
                                            <td>@if($item->item_thumbnail != '') <img class="lazy" width="50" height="50" src="{{ Helper::Image_Path($item->item_thumbnail,'no-image.png') }}"  alt="{{ $item->item_name }}"/>@else <img class="lazy" width="50" height="50" src="{{ url('/') }}/public/img/no-image.png"  alt="{{ $item->item_name }}" />  @endif</td>
                                            <td><a href="{{ url('/item') }}/{{ $item->item_slug }}" target="_blank" class="black-color">{{ mb_substr($item->item_name, 0, 50, 'UTF-8') }}</a></td>
                                            <td>@if($item->free_download == 1) <span class="badge badge-success">{{ __('Yes') }}</span> @else <span class="badge badge-danger">{{ __('No') }}</span> @endif</td>
                                            <td>@if($item->item_flash_request == 1) @if($item->item_flash == 0) <span class="badge badge-danger">{{ __('Waiting for approval') }}</span> @else <span class="badge badge-success">{{ __('Approved') }}</span> @endif @else <span>---</span> @endif</td>
                                            <td><a href="{{ url('/user') }}/{{ $item->username }}" target="_blank" class="black-color">{{ $item->username }}</a></td>
                                            <td>
                                            @if($demo_mode == 'on')
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-success btn-sm"><i class="fa fa-undo"></i>&nbsp;{{ __('Restore') }}</a> 
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;{{ __('Delete Permanently') }}</a>
                                            
                                            @else
                                            <a href="{{ url('/admin') }}/restore-items/{{ $item->item_token }}" class="btn btn-success btn-sm"><i class="fa fa-undo"></i>&nbsp; {{ __('Restore') }}</a> 
                                            <a href="{{ url('/admin') }}/delete-items/{{ $item->item_token }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ __('Are you sure you want to permanently delete') }}?');"><i class="fa fa-trash"></i>&nbsp;{{ __('Delete Permanently') }}</a>
                                            @endif</td>
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

</script>

</body>

</html>

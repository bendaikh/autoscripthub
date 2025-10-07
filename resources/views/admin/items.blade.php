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
                                            <td><a href="{{ url('/admin') }}/edit-item/{{ $item->item_token }}" class="btn btn-success btn-sm"><i class="fa fa-edit"></i>&nbsp; {{ __('Edit') }}</a> 
                                            @if($demo_mode == 'on') 
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>&nbsp;{{ __('Trash') }}</a>
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-primary btn-sm"><i class="fa fa-download"></i>&nbsp;{{ __('Download Item') }}</a>
                                            @else
                                            <a href="{{ url('/admin') }}/items/{{ $item->item_token }}" class="btn btn-danger btn-sm" onClick="return confirm('{{ __('Are you sure you want to remove') }}?');"><i class="fa fa-trash"></i>&nbsp;{{ __('Trash') }}</a>
                                            <a href="{{ url('/admin') }}/download/{{ $item->item_token }}" class="btn btn-primary btn-sm"><i class="fa fa-download"></i>&nbsp;{{ __('Download') }}</a>
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

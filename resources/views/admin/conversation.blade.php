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
    @if(in_array('settings',$avilable))
    <div id="right-panel" class="right-panel">

        
                       @include('admin.header')
                       
                       
        <div class="breadcrumbs">
            <div class="col-sm-4">
                <div class="page-header float-left">
                    <div class="page-title">
                        <h1 class="textformat">{{ __('Conversation') }} {{ __('With') }} {{ $user_details->username }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="page-header float-right">
                    <div class="page-title">
                        <ol class="breadcrumb text-right">
                           <a href="javascript: window.history.back();" class="btn btn-success btn-sm"><i class="fa fa-chevron-left"></i> {{ __('Back') }}</a>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
         @include('admin.warning')
        <div class="content mt-3">
            <div class="animated fadeIn">
                <div class="row">

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <strong class="card-title">{{ __('Conversation') }}</strong>
                            </div>
                            <div class="card-body">
                                <table id="bootstrap-data-table-export" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Sno') }}</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Photo') }}</th>
                                            <th>{{ __('Conversation Message') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($other_user['details'] as $user)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $user->username }}</td>
                                            <td>@if($user->user_photo!='')
                                                <img height="50" src="{{ url('/') }}/public/storage/users/{{ $user->user_photo }}"  height="50" width="50"/>@else <img height="50" width="50" src="{{ url('/') }}/public/img/no-user.png" />  @endif</td>
                                            
                                            <td>
                                            @if($demo_mode == 'on') 
                                            <a href="{{ url('/admin') }}/demo-mode" class="btn btn-primary btn-sm"><i class="fa fa-comments-o"></i>&nbsp;{{ __('Conversation Message') }}</a>
                                            @else
                                            <a href="{{ url('/admin') }}/messages/{{ $user->username }}/{{ $user_details->username }}" class="btn btn-primary btn-sm"><i class="fa fa-comments-o"></i>&nbsp;{{ __('Conversation Message') }} ({{ Helper::MsgCount($user->id,$user_details->id) }})</a>@endif
                                            </td>
                                        </tr>
                                        @php $no++; @endphp
                                   @endforeach     
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
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


</body>

</html>

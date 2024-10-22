@extends('layouts.app')

@section('body_class','nav-md')
@include('admin.layouts.flash-messages')
@section('page')
    <div class="container body">
        <div class="main_container">
            @section('header')
                @include('admin.sections.navigation')
                @include('admin.sections.header')
            @show

            @yield('left-sidebar')

            <div class="right_col" role="main">
                <div class="page-title">
                    <div class="title_left">
                        <h1 class="h3">@yield('title')</h1>
                    </div>
                    @if(Breadcrumbs::exists())
                        <div class="title_right">
                            <div class="pull-right">
                                {!! Breadcrumbs::render() !!}
                            </div>
                        </div>
                    @endif
                </div>
                @yield('content')
            </div>

            <footer>
                @include('admin.sections.footer')
            </footer>
        </div>
    </div>
@stop

@section('styles')
    {{ Html::style(mix('assets/admin/css/admin.css')) }}
@endsection

@section('scripts')
    {{ Html::script(mix('assets/admin/js/admin.js')) }}
    <script src="{{asset('vendors/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js') }}"></script>
    <script src="{{asset('vendors/datatables.net-scroller/js/dataTables.scroller.min.js') }}"></script> 
@endsection
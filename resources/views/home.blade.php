@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="javascript:void(0)" class="btn btn-primary btn-sm">Send Email</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script type="text/javascript">
        $(function(){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('.btn-sm').click(function(){
                $.ajax({
                    url: 'send-notification',
                    type: 'POST',
                    dataType: 'json'
                })
                .done(function() {
                    console.log("success");
                })
                .fail(function() {
                    console.log("error");
                });
            });
        })
    </script>
@endpush

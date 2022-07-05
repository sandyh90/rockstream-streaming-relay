@extends('layouts.main')

@section('title','Livestream Analytics')

@section('head-content')
<!-- Seperate Addons CSS -->
<link rel="stylesheet" href="{{ asset('assets/vendor/toastr/toastr.min.css') }}">
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="fs-4 fw-light my-2"><span class="bi bi-activity me-1"></span>Livestream Analytics
            </div>
            <div class="d-flex justify-content-between flex-wrap my-2">
                <button class="btn btn-primary live-analytics-data-refresh">
                    <span class="bi bi-arrow-clockwise me-1"></span>Refresh
                </button>
                <div class="form-check form-switch">
                    <input class="form-check-input auto-refresh-analytics-switch" type="checkbox" role="switch"
                        id="auto-refresh-analytics">
                    <label class="form-check-label auto-refresh-analytics-text" for="auto-refresh-analytics">Auto
                        Refresh</label>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped live-analytics-data" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Live Address</th>
                            <th>Client</th>
                            <th>Metadata</th>
                            <th>Connection</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-content')
<!-- Seperate Addons Javascript-->
<script src="{{ asset('assets/vendor/toastr/toastr.min.js') }}"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $('.live-analytics-data').DataTable({
            ajax: {
                url: "{{ route('analytics.getdata') }}",
                type: 'get',
                async: true,
                processing: true,
                serverSide: true,
                bDestroy: true
            },
            columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex'
            }, {
                data: 'name_address',
                name: 'name_address'
            }, {
                data: 'clients',
                name: 'clients'
            }, {
                data: 'metadata',
                name: 'metadata'
            }, {
                data: 'bandwidth',
                name: 'bandwidth',
                orderable: true,
                searchable: true
            }, ]
        });
    });

    if (document.querySelector("input.auto-refresh-analytics-switch")) {
        let refresh_data;
        document.querySelector("input.auto-refresh-analytics-switch").addEventListener("change", function () {
        let refresh_switch = document.querySelector("input.auto-refresh-analytics-switch").checked;
        var countdown = 10;
        document.querySelector("label.auto-refresh-analytics-text").innerHTML = 'Auto Refresh <strong></strong>';
        if(refresh_switch == true){
            refresh_data = setInterval(function(){
                document.querySelector("label.auto-refresh-analytics-text strong").innerHTML = countdown;
                countdown--;
                
                if(countdown < 0){
                    countdown = 10;
                    document.querySelector("label.auto-refresh-analytics-text strong").innerHTML = countdown;
                    toastr.info('Please wait','Refresh Table', {timeOut: 3000, preventDuplicates: true, progressBar: true});
                    $('.live-analytics-data').DataTable().ajax.reload();
                }
                
                }, 1000);
        }else{
          clearInterval(refresh_data);
        }
        console.log(`Auto refresh state ${refresh_switch}`);
      });
    }

    document.querySelector(".live-analytics-data-refresh").addEventListener("click", function(e) {
        e.preventDefault();
        swal.fire({
            title: "Refresh Table",
            text: "Please wait",
            showConfirmButton: false,
            allowOutsideClick: false,
            timer: 800,
            timerProgressBar: true
        });
        Swal.showLoading();
        $('.live-analytics-data').DataTable().ajax.reload();
    });
</script>
@endsection
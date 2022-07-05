<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel"><span class="bi bi-broadcast me-1"></span>Edit Output
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="form-edit-output-dest">
        <div class="form-group mb-2">
            <label class="form-label">Name Output Destination</label>
            <input type="text" class="form-control" name="name_output_dest" placeholder="Name Output Destination"
                value="{{ $output_dest_data->name_stream_dest }}">
        </div>
        <div class="form-group mb-2">
            <label class="form-label">Platform Output</label>
            <select class="form-select" name="platform_output_dest">
                @foreach(\App\Component\ServicesCore::getServices() as $key => $value)
                <option value="{{ $value['id'] }}" {{ $output_dest_data->platform_dest == $key ? 'selected' : '' }}>{{
                    $value['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="output-dest-selector d-none">
            <div class="form-group mb-2">
                <label class="form-label">RTMP Output Server</label>
                <select class="form-select d-none" name="select_endpoint_server" data-width="100%">
                </select>
                <input type="text" class="form-control d-none" name="rtmp_output_server"
                    placeholder="rtmp://xxx.xxx.xxx.xxx/live">
                <div class="form-text small text-muted">*For now we currently not support rtmps:// url server</div>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label">RTMP Stream Key</label>
            <div class="input-group" x-data="{ input: 'password' }">
                <input type="password" class="form-control" name="rtmp_stream_key"
                    value="{{ $output_dest_data->key_stream_dest }}" x-bind:type="input">
                <button type="button" class="input-group-text btn-toggle-stream-key" data-bs-toggle="tooltip"
                    data-bs-original-title="Show Stream Key"
                    x-on:click="input = (input === 'password') ? 'text' : 'password'"><span
                        :class="{'bi bi-eye-slash' : input != 'password','bi bi-eye': input != 'text'}"></span></button>
            </div>
        </div>
        <div class="form-group mb-2">
            <label class="form-label me-1">Status Output</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="edit-status-output-dest-1" value="1"
                    name="status_output_dest" {{ $output_dest_data->active_stream_dest == TRUE ? 'checked' : '' }}>
                <label class="form-check-label" for="edit-status-output-dest-1">Enable</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" id="edit-status-output-dest-2" value="0"
                    name="status_output_dest" {{ $output_dest_data->active_stream_dest == FALSE ? 'checked' : '' }}>
                <label class="form-check-label" for="edit-status-output-dest-2">Disable</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-edit-output-dest"><span
                class="bi bi-save me-1"></span>Save</button>
    </form>
    <div class="my-2 edit-output-dest-info-data"></div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
<script>
    fetchEndpointServer();

    $("select[name='platform_output_dest']").on('change',function(){
        fetchEndpointServer();
    });
    function fetchEndpointServer(){
        var selected_url_rtmp ="{{ $output_dest_data->url_stream_dest }}";
        var id_platform = $("select[name='platform_output_dest'] option:selected").val();
        if (id_platform === null) return;

        $.ajax({
            type: 'POST',
            url: "{{ route('outputdest.fetch_endpoint') }}",
            dataType: 'json',
            async: true,
            data: { id_platform : id_platform },
            beforeSend: function() {
                $(".form-edit-output-dest .output-dest-selector").prepend("<div class='fetching-data-endpoint text-center'><span class='spinner-border spinner-border-sm me-1'></span>Fetching Endpoint</div>").removeClass("d-none");
                $(".form-edit-output-dest select[name='select_endpoint_server']").empty().addClass("d-none");
                $(".btn-edit-output-dest").html("<span class='spinner-border spinner-border-sm me-1'></span>Please Wait").attr("disabled", true);
            },
            error: function() {
                $(".btn-edit-output-dest").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
            }
        }).done(function(data){
            $(".form-edit-output-dest .output-dest-selector .fetching-data-endpoint").remove();
            $(".btn-edit-output-dest").html("<span class='bi bi-save me-1'></span>Save").attr("disabled", false);
            if(data.manual_input == true){
                $(".form-edit-output-dest input[name='rtmp_output_server']").removeClass("d-none");
                $(".form-edit-output-dest select[name='select_endpoint_server']").addClass("d-none");
                $(".form-edit-output-dest input[name='rtmp_output_server']").val(selected_url_rtmp);
            }else{
                $(".form-edit-output-dest input[name='rtmp_output_server']").addClass("d-none");
                $(".form-edit-output-dest select[name='select_endpoint_server']").removeClass("d-none");
            }
            var list_server = '';
            $.each(data.services, function(i, item) {
                list_server += `<option value='${data.services[i].rtmp_address}' ${selected_url_rtmp == data.services[i].rtmp_address ? 'selected': ''}>${data.services[i].name_endpoint}</option>`;
            });
            $(".form-edit-output-dest select[name='select_endpoint_server']").html(list_server);
        });
    };
</script>
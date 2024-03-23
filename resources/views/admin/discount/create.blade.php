@extends('admin.layouts.app')

@section('content')

<section class="content mt-4">
    <div class="container-fluid">
        <form action="" method="post" id="discountForm" name="discountForm">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-book"></i> <b>Create Coupon Code</b>
                    <a href="{{ route('discounts.index') }}" class="btn btn-primary float-right ml-3">Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code">Code</label>
                                <input type="text" name="code" id="code" class="form-control" placeholder="Coupon code">
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Coupon code Name">
                                <p class="error"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses">Max Uses</label>
                                <input type="number" name="max_uses" id="max_uses" class="form-control" placeholder="Coupon code Max Uses" >
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_uses_user">Max Uses Users</label>
                                <input type="text" name="max_uses_user" id="max_uses_user" class="form-control" placeholder="Coupon code Max Uses Users" >
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control" >
                                    <option value="percent">Percent</option>
                                    <option value="fixed">Fixed</option>
                                </select>
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount">Discount Amount</label>
                                <input type="text" name="discount_amount" id="discount_amount" class="form-control" placeholder="Discount Amount">
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_amount">Min Amount</label>
                                <input type="text" name="min_amount" id="min_amount" class="form-control" placeholder="Min Amount">
                                <p class="error"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_at">Start_at</label>
                                <input type="text" autocomplete="off" name="start_at" id="start_at" class="form-control" placeholder="Start_at">
                                <p class="error"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expires_at">Expires_at</label>
                                <input type="text" autocomplete="off" name="expires_at" id="expires_at" class="form-control" placeholder="Expires_at">
                                <p class="error"></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description"  rows="5"></textarea>
                                <p class="error"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button class="btn btn-primary" type="submit">Create</button>
                <a href="{{ route('discounts.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection

@section('add_script')
    <script>
        $(document).ready(function(){
            $('#start_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
        });
        $(document).ready(function(){
            $('#expires_at').datetimepicker({
                // options here
                format:'Y-m-d H:i:s',
            });
        });
        $('#discountForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                'url':'{{ route("discounts.store") }}',
                'type':'post',
                'data': element.serializeArray(),
                'dataType': 'json',
                success : function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if (response['status'] == true) {
                        window.location.href = "{{ route('discounts.index') }}";
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],select,textarea,input[type='number']").removeClass('is-invalid');
                    } else {
                        var errors = response['errors'];
                        $(".error").removeClass('invalid-feedback').html('');
                        $("input[type='text'],select,textarea,input[type='number']").removeClass('is-invalid');
                        $.each(errors, function(key, value) {
                            $(`#${key}`)
                                .addClass('is-invalid')
                                .siblings('p')
                                .addClass('invalid-feedback')
                                .html(value);
                        });
                    }

                },error:function(jqXHR, exception){
                    console.log("Something went wrong");
                }
            });
        });

        $("#name").change(function(){
            element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                'url':'{{ route("slug") }}',
                'type':'get',
                'data': {title: element.val()},
                'dataType': 'json',
                success : function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response["status"] == true){
                        $("#slug").val(response["slug"])
                    }
                }
            });
        });


    </script>
@endsection


@extends('admin.layouts.app')

@section('content')

<section class="content mt-4">
    <div class="container-fluid">
        <form action="" method="pst" id="brandForm" name="brandForm">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-book"></i> <b>Edit Brand</b>
                    <a href="{{ route('brands.index') }}" class="btn btn-primary float-right ml-3">Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" value="{{ $brand->name }}" class="form-control" placeholder="Name">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" value="{{ $brand->slug }}" placeholder="Slug" readonly>
                                <p></p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option {{ ($brand->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                    <option {{ ($brand->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button class="btn btn-primary" type="submit">Update</button>
                <a href="{{ route('brands.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection

@section('add_script')
    <script>
        $('#brandForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                url :'{{ route("brands.update",$brand->id) }}',
                type :'patch',
                data : element.serializeArray(),
                dataType : 'json',
                success : function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response['status'] == true){
                        window.location.href='{{ route('brands.index') }}';
                        $("#name").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#slug").removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback').html("");

                    }else{

                        if(response['notFound'] == true){
                            window.location.href='{{ route('brands.index') }}';
                        }

                        var errors = response['errors'];
                        if(errors['name']){
                            $("#name").addClass('is-invalid').siblings('p')
                            .addClass('invalid-feedback').html(errors['name']);
                        }else{
                            $("#name").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if(errors['slug']){
                            $("#slug").addClass('is-invalid').siblings('p')
                            .addClass('invalid-feedback').html(errors['slug']);
                        }else{
                            $("#slug").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }
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
                'url':'{{ route("brands.slug") }}',
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

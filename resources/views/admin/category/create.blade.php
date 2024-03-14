@extends('admin.layouts.app')

@section('content')

<section class="content mt-4">
    <div class="container-fluid">
        <form action="" method="pst" id="categoryForm" name="categoryForm">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-book"></i> <b>Create Category</b>
                    <a href="{{ route('categories.index') }}" class="btn btn-primary float-right ml-3">Back</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Name">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug">Slug</label>
                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="hidden" name="image_id" id="image_id" value="">
                                <label for="image">Image</label>
                                <div id="image" class="dropzone dz-clickable">
                                    <div class="dz-message needsclick">
                                        <br>Drop Files Here/Click to upload <br><br>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Block</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pb-5 pt-3">
                <button class="btn btn-primary" type="submit">Create</button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
        </form>
    </div>
</section>
@endsection

@section('add_script')
    <script>
        $('#categoryForm').submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                'url':'{{ route("categories.store") }}',
                'type':'post',
                'data': element.serializeArray(),
                'dataType': 'json',
                success : function(response){
                    $("button[type=submit]").prop('disabled',false);
                    if(response['status'] == true){
                        window.location.href='{{ route('categories.index') }}';
                        $("#name").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#slug").removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback').html("");

                    }else{
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
                'url':'{{ route("categories.slug") }}',
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

        Dropzone.autoDiscover = false;
        const dropzone = $("#image").dropzone({
            init: function(){
                this.on('addedfile',function(file){
                    if(this.files.length > 1){
                        this.removeFile(this.files[0]);
                    }
                });
            },

            url : "{{ route('categories.temp-images.create') }}",
            type: 'post',
            maxFiles : 1,
            paramName : 'image',
            addRemoveLinks : true,
            acceptedFiles : 'image/jpeg,image/jpg,image/png,image/gif',
            headers : {
                'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')
            },success: function(file,response){
                $("#image_id").val(response.image_id);
                console.log(response);
            }

        })
    </script>
@endsection

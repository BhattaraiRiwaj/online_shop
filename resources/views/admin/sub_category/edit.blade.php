@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Sub Category</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('sub_categories.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <form action="" name="subCategory" id="subCategory">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="name">Category</label>
                                    <select name="category" id="category" class="form-control">
                                        <option value="" readonly><b>Select Category</b></option>
                                        @if ($categories->isNotEmpty())
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ (!empty($category->id == $subCategories->category_id )) ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" id="name" value="{{ $subCategories->name }}" class="form-control"
                                        placeholder="Name">
                                        <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="slug">Slug</label>
                                    <input type="text" name="slug" id="slug" value="{{ $subCategories->slug }}" class="form-control"
                                        placeholder="Slug" readonly>
                                        <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ ($subCategories->status == 1) ? 'selected' : '' }} value="1">Active</option>
                                        <option {{ ($subCategories->status == 0) ? 'selected' : '' }} value="0">Block</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="pb-5 pt-3">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('sub_categories.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
            </div>
            </form>
        </div>
    </section>
@endsection

@section('add_script')
    <script>
   $('#subCategory').submit(function(event){
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled',true);
            $.ajax({
                'url':'{{ route("sub_categories.update",$subCategories->id) }}',
                'type':'patch',
                'data': element.serializeArray(),
                'dataType': 'json',
                success : function(response){
                    $("button[type=submit]").prop('disabled',false);

                    if(response['status'] == true){
                        window.location.href='{{ route('sub_categories.index') }}';
                        $("#name").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#slug").removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback').html("");

                        $("#category").removeClass('is-invalid').siblings('p')
                        .removeClass('invalid-feedback').html("");

                    }else{
                        if(response['notFound'] == true){
                            window.location.href='{{ route('sub_categories.index') }}';
                        }

                        var errors = response['errors'];
                        if(errors['category']){
                            $("#category").addClass('is-invalid').siblings('p')
                            .addClass('invalid-feedback').html(errors['category']);
                        }else{
                            $("#category").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

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


        $("#name").change(function() {
            element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                'url': '{{ route('slug') }}',
                'type': 'get',
                'data': {
                    title: element.val()
                },
                'dataType': 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        $("#slug").val(response["slug"])
                    }
                }
            });
        });
    </script>
@endsection

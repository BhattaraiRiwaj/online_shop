@extends('admin.layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Product</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('products.create') }}" class="btn btn-primary">New Product</a>
                </div>
            </div>
        </div>
    </section>
    <div class="container-fluid">
        @include('admin.message')
        <div class="card">
            <form action="" method="get">
                <div class="card-header">
                    <div class="card-tools">
                        <div class="input-group input-group" style="width: 250px;">
                            <input type="text" value="{{ Request::get('keyword') }}" name="keyword"
                                class="form-control float-right" placeholder="Search">

                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="card-title">
                                <button type="button" onclick="window.location.href='{{ route('products.index') }}'"
                                    class="btn btn-default">Reset</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-body table-responsive p-0">
                @include('admin.product.table')
            </div>
            <div class="card-footer clearfix">
                <ul class="pagination pagination m-0 float-right">
                    {{ $products->links() }}
                </ul>
            </div>
        </div>
    </div>
    </section>
@endsection
@section('add_script')
    <script>
        $("document").ready(function() {
            setTimeout(function() {
                $("#message").remove();
            }, 3000); // 3 secs

        });


        function deleteProduct(id) {
            var url = '{{ route("products.delete", 'ID') }}';
            var newUrl = url.replace("ID", id);
            if (confirm("Are you sure you want to delete")) {
                $.ajax({
                    url: newUrl,
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response["status"]) {
                            window.location.href = "{{ route('products.index') }}";
                        }
                    }
                });
            }
        }
    </script>
@endsection

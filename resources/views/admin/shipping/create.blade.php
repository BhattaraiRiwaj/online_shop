@extends('admin.layouts.app')

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <form action="" method="pst" id="shippingForm" name="shippingForm">
                @include('admin.message')
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-book"></i> <b>Shipping Management</b>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="country">Country</label>
                                    <select name="country" id="country" class="form-control">
                                        <option value="">Select Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="rest_of_world">Rest Of World</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="amount">Amount</label>
                                    <input type="text" name="amount" id="amount" class="form-control" />
                                    <p></p>
                                </div>
                            </div>
                            <div class="pt-2 mt-4">
                                <label for=""></label>
                                <button class="btn btn-primary" type="submit">Create</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($shippingCharges->isNotEmpty())
                                            @foreach ($shippingCharges as $shippingCharge)
                                                <tr>
                                                    <td>{{ $shippingCharge->id }}</td>
                                                    <td>
                                                        {{ ($shippingCharge->country_id == 'rest_of_world') ? ' rest_of_world' : $shippingCharge->name }}
                                                    </td>
                                                    <td>$ {{ $shippingCharge->amount }}</td>
                                                    <td>
                                                        <a href="{{ route('shipping.edit',$shippingCharge->id) }}" class="btn btn-sm btn-info">Edit</a>
                                                        <a href="javascript:void(0)" onclick="destroy('{{ $shippingCharge->id }}')" class="btn btn-sm btn-danger">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@section('add_script')
    <script>
        $('#shippingForm').submit(function(event) {
            event.preventDefault();
            var element = $(this);
            $("button[type=submit]").prop('disabled', true);
            $.ajax({
                'url': '{{ route("shipping.store") }}',
                'type': 'post',
                'data': element.serializeArray(),
                'dataType': 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response['status'] == true) {
                        window.location.href = '{{ route("shipping.create") }}';
                        $("#country").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#amount").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                    } else {
                        var errors = response['error'];
                        if (errors['country']) {
                            $("#country").addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(errors['country']);
                        } else {
                            $("#country").removeClass('is-invalid').siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }

                        if (errors['amount']) {
                            $("#amount").addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(errors['amount']);
                        } else {
                            $("#amount").removeClass('is-invalid').siblings('p')
                                .removeClass('invalid-feedback').html("");
                        }
                    }

                },
                error: function(jqXHR, exception) {
                    console.log("Something went wrong");
                }
            });
        });

        function destroy(id){
            var url = "{{ route('shipping.delete', 'ID') }}";
            var newUrl = url.replace("ID", id);
            if(confirm("Are you Sure You Want To Delete")){
                $.ajax({
                    url: newUrl,
                    type: "delete",
                    data: {id:id},
                    dataType: "json",
                    success: function (response) {
                        if(response.status){
                            window.location.href = "{{ route('shipping.create') }}";
                        }
                    },error:function(jqXHR,exception){
                        console.log("something went wrong");
                    }
                });
            }
        }
    </script>
@endsection

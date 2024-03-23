@extends('admin.layouts.app')

@section('content')

    <section class="content mt-4">
        <div class="container-fluid">
            <form action="" method="pst" id="shippingForm" name="shippingForm">
                @include('admin.message')
                <div class="card">
                    <div class="card-header">
                        <i class="fa fa-book"></i> <b>Edit Shipping Management</b>
                        <a href="{{ route('shipping.create') }}" class="btn btn-md btn-primary float-right">Back</a>
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
                                            <option value="{{ $country->id }}" {{ !empty($shippings->country_id == $country->id) ? 'selected' : ''}}>{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                        <option value="rest_of_world" {{ !empty($shippings->country_id == 'rest_of_world') ? 'selected' : ''}}>Rest Of World</option>
                                    </select>
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="amount">Amount</label>
                                    <input type="text" name="amount" id="amount" value="{{ $shippings->amount }}" class="form-control" />
                                    <p></p>
                                </div>
                            </div>
                            <div class="pt-2 mt-4">
                                <label for=""></label>
                                <button class="btn btn-primary" type="submit">Update</button>
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
                'url': '{{ route("shipping.update",$shippings->id) }}',
                'type': 'patch',
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
    </script>
@endsection

@extends('front.layouts.app')

@section('front-content')
<section class="container">
    <div class="col-md-12">
            <div class="col-md-12 text-center py-5">
                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session()->get('success') }}
                    </div>
                @endif
                <h4>Thank You For Your Purchase.</h4>
                <p>Your Order Id is: {{  $id }}</p>
            </div>
    </div>
</section>
@endsection

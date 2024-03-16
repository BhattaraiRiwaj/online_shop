@if (Session::has('success'))
    <div class="sufee-alert alert with-close alert-success alert-dismissible fade show" id="message">
        <span class="badge badge-pill badge-success">Success! </span>
        {{ Session::get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif

@if (Session::has('error'))
    <div class="sufee-alert alert with-close alert-danger alert-dismissible fade show" id="message">
        <span class="badge badge-pill badge-danger">Error! </span>
        {{ Session::get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif

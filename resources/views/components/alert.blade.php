@if ($message = Session::get('error'))

<div class="alert alert-danger alert-dismissible fade show">
    <strong>{{ $message }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

@endif
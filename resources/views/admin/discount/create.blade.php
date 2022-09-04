@extends('master')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
            <div class="card mt-3">
                <div class="card-header">
                    Insert a new discount
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.discount.store') }}" method="post">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Name</label>
                            <input name="name" type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}" required />
                            @if ($errors->has('name'))
                                <p class="text-danger">{{ $errors->first('name') }}</p>
                            @endif
                        </div>
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Code</label>
                            <input name="code" type="text" class="form-control {{ $errors->has('code') ? 'is-invalid' : '' }}" value="{{ old('code') }}" required />
                            @if ($errors->has('code'))
                                <p class="text-danger">{{ $errors->first('code') }}</p>
                            @endif
                        </div>
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Description</label>
                            <textarea name="description" cols="0" rows="3" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" value="{{ old('description') }}" required></textarea>
                            @if ($errors->has('description'))
                                <p class="text-danger">{{ $errors->first('description') }}</p>
                            @endif
                        </div>
                        <div class="form-group mb-2">
                            <label for="" class="form-label">Percentage</label>
                            <input name="percentage" type="number" class="form-control {{ $errors->has('percentage') ? 'is-invalid' : '' }}" value="{{ old('percentage') }}" min="1" max="100" required />
                            @if ($errors->has('percentage'))
                                <p class="text-danger">{{ $errors->first('percentage') }}</p>
                            @endif
                        </div>
                        <div class="form-group d-flex flex-row-reverse">
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
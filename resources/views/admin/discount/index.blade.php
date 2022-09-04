@extends('master')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-8 offset-2">
            <div class="card mt-5">
                <div class="card-header">
                    Discount
                </div>
                <div class="card-body">
                    @include('components.alert')
                    <div class="row">
                        <div class="col-md-12 d-flex flex-row-reverse">
                            <a href="{{ route('admin.discount.create') }}" class="btn btn-primary btn-sm">Add Discount</a>
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr class="text-center">
                                <th>Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Percentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($discount as $discount)
                            <tr class="text-center">
                                <td>{{ $discount->name }}</td>
                                <td><span class="badge bg-primary">{{ $discount->code }}</span></td>
                                <td>{{ $discount->description }}</td>
                                <td>{{ $discount->percentage }}</td>
                                <td class="d-flex">
                                    <a href="{{ route('admin.discount.edit', $discount->id) }}" class="btn btn-warning mx-2"><i class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('admin.discount.destroy', $discount->id) }}" method="post">
                                        @csrf
                                        @method('DELETE')
                                        <input type="submit" value="delete" class="btn btn-danger">
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">No discount created</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
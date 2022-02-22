@extends('layout')
@section('title', 'Kategorya tahrirlash')
@section('header-text', 'Kategorya tahrirlash')

@section('content')

<div class="container container-bg">
    
    <form method="post" action="{{ route('categories.update', $category->id) }}">
        @method('PUT')
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="mb-3">
                <label for="category" class="form-label">Kategorya nomi*</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category')?? old('category')? : $category->category }}">
            </div>
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button type="submit" class="btn btn-success px-3 py-2 mt-3">Tahrirlash</button>
    </div>
    </form>
            
  

</div>

@endsection
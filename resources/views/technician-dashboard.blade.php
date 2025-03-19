@extends('layouts.main')

@section('content')
    <h1>Dashboard</h1>
    <b>Hello, Technician - {{ Auth::user()->name }}</b>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">On Site Items</h5>
                    <h1>
                        <i class="fas fa-boxes"></i>
                        {{ App\Models\Onsites::where('technicians_id', Auth::user()->id)->sum('quantity') }}
                    </h1>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Damaged Items</h5>
                    <h1>
                        <i class="fas fa-house"></i>
                        {{ App\Models\Damages::where('technicians_id', Auth::user()->id)->sum('quantity') }}
                    </h1>
                </div>
            </div>
        </div>
    </div>
@endsection
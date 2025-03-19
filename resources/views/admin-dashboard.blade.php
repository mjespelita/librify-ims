@extends('layouts.main')

@section('content')
    <h1>Dashboard</h1>
    <b>Hello, Admin</b>
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Items Overview</h5>
                    <div id="pie-chart-item-overview"></div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Sites</h5>
                            <h1>
                                <i class="fas fa-house"></i>
                                {{ App\Models\Sites::count() }}
                            </h1>
                            <a href="{{ url('sites') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Technicians</h5>
                            <h1>
                                <i class="fas fa-users"></i>
                                {{ App\Models\User::whereNot('role', 'admin')->count() }}
                            </h1>
                            <a href="{{ url('technicians') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>
        
                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Item Types</h5>
                            <h1>
                                <i class='fas fa-cogs'></i>
                                {{ App\Models\Types::count() }}
                            </h1>
                            <a href="{{ url('types') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>
        
                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Warehouse Items</h5>
                            <h1>
                                <i class='fas fa-cogs'></i>
                                {{ App\Models\Items::sum('quantity') - App\Models\Onsites::sum('quantity') - App\Models\Damages::sum('quantity') }}
                            </h1>
                            <a href="{{ url('items') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>
        
                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total On Site Item Count</h5>
                            <h1>
                                <i class='fas fa-cogs'></i>
                                {{ App\Models\Onsites::sum('quantity') }}
                            </h1>
                            <a href="{{ url('onsites') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Total Damage Item Count</h5>
                            <h1>
                                <i class='fas fa-exclamation-triangle'></i>
                                {{ App\Models\Damages::sum('quantity') }}
                            </h1>
                            <a href="{{ url('damages') }}">
                                <button class="btn btn-outline-secondary"><i class="fas fa-eye"></i> View Details</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
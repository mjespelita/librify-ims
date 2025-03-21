
@extends('layouts.main')

@section('content')
    <h1>Sites Details</h1>

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class='card'>
                <div class='card-body'>
                    <div class='table-responsive'>
                        <table class='table'>
                            <tr>
                                <th>ID</th>
                                <td>{{ $item->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $item->name }}</td>
                            </tr>
                            <tr>
                                <th>Phonenumber</th>
                                <td>{{ $item->phonenumber }}</td>
                            </tr>
                            <tr>
                                <th>Google Map</th>
                                <td>
                                    <iframe
                                        width="600"
                                        height="450"
                                        src="{{ $item->google_map_link }}"
                                        allowfullscreen>
                                    </iframe>
                                </td>
                            </tr>
        
                            <tr>
                                <th>Added By</th>
                                <td>{{ $item->users->name." (".$item->users->role.")" ?? "no data" }}</td>
                            </tr>
            
                            <tr>
                                <th>Created At</th>
                                <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ Smark\Smark\Dater::humanReadableDateWithDayAndTime($item->updated_at) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold text-success">Items</h5>
                    <div class='table-responsive'>
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Item</th>
                                    <th>Serial #</th>
                                    <th>Type</th>
                                    <th>Technician</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                @forelse(App\Models\Onsites::where('sites_id', $item->id)->get() as $site)
                                    <tr>
                                        <td><b>{{ $site->items->itemId ?? "no data" }}</b></td>
                                        <td>{{ $site->items->name ?? "no data" }}</td>
                                        <td>
                                            @if($site->serial_numbers)
                                                @foreach(explode(',', $site->serial_numbers) as $serial_number)
                                                    <span class="custom-badge">{{ trim($serial_number) }}</span>
                                                @endforeach
                                            @else
                                                <span class="custom-badge no-serial">no serial numbers</span>
                                            @endif
                                        </td>
                                        <td><b class="text-success">{{ $site->types->name ?? "no data" }}</b></td>
                                        <td>{{ $site->technicians->name ?? "no data" }}</td>
                                        <td><b class="text-primary">{{ $site->quantity }}</b></td>
                                        <td>{{ $site->items->unit ?? "no data" }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>No Record...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="fw-bold text-danger">Damages</h5>
                    <div class='table-responsive'>
                        <table class='table table-striped'>
                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Item</th>
                                    <th>Serial #</th>
                                    <th>Type</th>
                                    <th>Technician</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                @forelse(App\Models\Damages::where('sites_id', $item->id)->get() as $site)
                                    <tr>
                                        <td><b>{{ $site->items->itemId ?? "no data" }}</b></td>
                                        <td>{{ $site->items->name ?? "no data" }}</td>
                                        <td>
                                            @if($site->serial_numbers)
                                                @foreach(explode(',', $site->serial_numbers) as $serial_number)
                                                    <span class="custom-badge">{{ trim($serial_number) }}</span>
                                                @endforeach
                                            @else
                                                <span class="custom-badge no-serial">no serial numbers</span>
                                            @endif
                                        </td>
                                        <td><b class="text-success">{{ $site->types->name ?? "no data" }}</b></td>
                                        <td>{{ $site->technicians->name ?? "no data" }}</td>
                                        <td><b class="text-danger">{{ $site->quantity }}</b></td>
                                        <td>{{ $site->items->unit ?? "no data" }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>No Record...</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href='{{ route('sites.index') }}' class='btn btn-primary'>Back to List</a>
@endsection

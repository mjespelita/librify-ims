<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

// end of import

use App\Http\Controllers\LogsController;
use App\Models\Logs;

// end of import

use App\Http\Controllers\TypesController;
use App\Models\Types;

// end of import

use App\Http\Controllers\ItemsController;
use App\Models\Items;

// end of import

use App\Http\Controllers\SitesController;
use App\Models\Sites;

// end of import

use App\Http\Controllers\TechniciansController;
use App\Models\Technicians;

// end of import

use App\Http\Controllers\ItemlogsController;
use App\Models\Itemlogs;

// end of import

use App\Http\Controllers\OnsitesController;
use App\Models\Onsites;
use App\Models\User;

// end of import

use App\Http\Controllers\DamagesController;
use App\Http\Controllers\RandomController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\TechnicianMiddleware;
use App\Models\Damages;

// end of import

use App\Http\Controllers\DeployedtechniciansController;
use App\Models\Deployedtechnicians;

// end of import


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard')->middleware(AuthMiddleware::class);
    
    Route::get('/admin-dashboard', function () {
        return view('admin-dashboard');
    })->middleware(AdminMiddleware::class);

    Route::get('/technician-dashboard', function () {
        return view('technician-dashboard');
    })->middleware(TechnicianMiddleware::class);

    // CUSTOMS

    Route::get('/create-add-item-quantity/{itemId}', [ItemsController::class, 'addItemQuantity'])->middleware(AdminMiddleware::class);
    Route::post('/add-item-quantity-process/{itemId}', [ItemsController::class, 'addItemQuantityProcess'])->middleware(AdminMiddleware::class);
    Route::get('/view-add-item-quantity-logs/{itemId}', [ItemsController::class, 'viewAddItemQuantityLogs'])->middleware(AdminMiddleware::class);
    Route::get('/view-technician-onsite-items/{userId}', [OnsitesController::class, 'viewTechnicianOnsiteItems'])->middleware(AdminMiddleware::class);
    Route::get('/view-technician-damage-items/{userId}', [DamagesController::class, 'viewTechnicianDamageItems'])->middleware(AdminMiddleware::class);

    // technicians

    Route::get('/my-onsite-items/{userId}', [RandomController::class, 'myOnsiteItems']);
    Route::get('/view-my-onsite-items-on-site/{siteId}', [RandomController::class, 'viewMyOnsiteItemsOnSite']);
    Route::get('/my-damaged-items/{userId}', [RandomController::class, 'myDamagedItems']);
    Route::get('/view-my-damaged-items-on-site/{siteId}', [RandomController::class, 'viewMyDamagedItemsOnSite']);
    Route::get('/my-sites', [RandomController::class, 'mySites']);

    // API

    Route::get('/get-item-data-for-graph', function () {
        return response()->json([
            'inWarehousesCount' => Items::sum('quantity') - Onsites::sum('quantity') - Damages::sum('quantity'),
            'onSitesCount' => Onsites::sum('quantity'),
            'damagesCount' => Damages::sum('quantity'),
        ]);
    });

    Route::get('/get-item-data-for-stats', function () {
        $onsiteItems = Onsites::all();
        $damagedItems = Damages::all();
        $final = [];
        $onsiteArray = [];
        $damagesArray = [];

        foreach ($onsiteItems as $key => $onsite) {
            $itemId = $onsite->items->id ?? "";
            $itemName = $onsite->items->name ?? "";
            $count = $onsite['quantity'];

            array_push($onsiteArray, [
                'id' => $itemId,
                'name' => $itemName,
                'count' => $count,
            ]);
        }

        array_push($final, [
            'onsites' => $onsiteArray
        ]);

        foreach ($damagedItems as $key => $damaged) {
            $itemId = $onsite->items->id ?? "";
            $itemName = $damaged->items->name ?? "";
            $count = $damaged['quantity'];

            array_push($damagesArray, [
                'id' => $itemId,
                'name' => $itemName,
                'count' => $count,
            ]);
        }

        array_push($final, [
            'damages' => $damagesArray
        ]);

        return response()->json([
            'final' => $final
        ]);
    });

    Route::get('/get-item-serial-numbers', function () {
        // Fetch all items from the Items model
        $allItems = Items::all();
    
        // Fetch the serial numbers from onsites and damages tables
        $onsites = Onsites::pluck('serial_numbers')->toArray();
        $damages = Damages::pluck('serial_numbers')->toArray();
    
        // Initialize an array to hold all serial numbers from onsites and damages
        $excludedSerialNumbers = [];
    
        // Loop through each record in onsites and damages to extract the serial numbers
        foreach ($onsites as $onsite) {
            // Split the serial numbers string by commas and clean up the whitespace
            $excludedSerialNumbers = array_merge($excludedSerialNumbers, array_map('trim', explode(',', $onsite)));
        }
    
        foreach ($damages as $damage) {
            // Split the serial numbers string by commas and clean up the whitespace
            $excludedSerialNumbers = array_merge($excludedSerialNumbers, array_map('trim', explode(',', $damage)));
        }
    
        // Remove duplicate serial numbers (optional)
        $excludedSerialNumbers = array_unique($excludedSerialNumbers);
    
        // Initialize an array to store serial numbers from allItems that are not in excludedSerialNumbers
        $validSerialNumbers = [];
    
        // Loop through each item in allItems and check if its serial numbers are not in the excluded list
        foreach ($allItems as $item) {
            // Split the serial numbers string by commas and clean up the whitespace
            $itemSerialNumbers = explode(',', $item->serial_numbers);
    
            // Filter the serial numbers to include only those not in the excludedSerialNumbers array
            $validSerialNumbers = array_merge($validSerialNumbers, array_filter($itemSerialNumbers, function ($serialNumber) use ($excludedSerialNumbers) {
                return !in_array(trim($serialNumber), $excludedSerialNumbers);
            }));
        }
    
        // Remove duplicate serial numbers
        $validSerialNumbers = array_unique($validSerialNumbers);
    
        // Return the serial numbers as an array (not an object)
        return response()->json([
            'serial_numbers' => array_values($validSerialNumbers), // Ensure it's an indexed array
        ], 200); // Optionally specify the status code
    });

    Route::get('/get-search-serial-numbers', function () {
        // Fetch all serial numbers from the Items model
    $allItems = Items::all();
    
    // Fetch the serial numbers from the Onsites and Damages tables with their respective sites_ids
    $onsites = Onsites::select('serial_numbers', 'sites_id')->get();
    $damages = Damages::select('serial_numbers', 'sites_id')->get();
    
    // Initialize an array to hold all serial numbers with their links
    $serialNumbersWithLinks = [];
    
    // Process Onsites serial numbers and their corresponding sites_ids
    foreach ($onsites as $onsite) {
        $itemSerialNumbers = array_map('trim', explode(',', $onsite->serial_numbers));
        
        foreach ($itemSerialNumbers as $serialNumber) {
            $serialNumbersWithLinks[] = [
                'serial_number' => $serialNumber,
                'link' => '/show-sites/' . $onsite->sites_id
            ];
        }
    }

    // Process Damages serial numbers and their corresponding sites_ids
    foreach ($damages as $damage) {
        $itemSerialNumbers = array_map('trim', explode(',', $damage->serial_numbers));
        
        foreach ($itemSerialNumbers as $serialNumber) {
            $serialNumbersWithLinks[] = [
                'serial_number' => $serialNumber,
                'link' => '/show-sites/' . $damage->sites_id
            ];
        }
    }

    // Process Items serial numbers
    foreach ($allItems as $item) {
        $itemSerialNumbers = array_map('trim', explode(',', $item->serial_numbers));
        
        foreach ($itemSerialNumbers as $serialNumber) {
            // If there is a sites_id in the Items model, use it for the link
            if (isset($item->sites_id)) {
                $serialNumbersWithLinks[] = [
                    'serial_number' => $serialNumber,
                    'link' => '/show-sites/' . $item->sites_id
                ];
            } else {
                // If no sites_id in Items, you can skip the link or leave it as null/empty
                $serialNumbersWithLinks[] = [
                    'serial_number' => $serialNumber,
                    'link' => '/show-items/' . $item->id
                ];
            }
        }
    }

    // Remove duplicates by serial_number (so no duplicates are included in the final list)
    $serialNumbersWithLinks = array_map("unserialize", array_unique(array_map("serialize", $serialNumbersWithLinks)));
    
    // Sort the array by serial_number in alphabetical order
    usort($serialNumbersWithLinks, function ($a, $b) {
        return strcmp($a['serial_number'], $b['serial_number']);
    });

    // Return the serial numbers with their links in the required format
    return response()->json([
        'serial_numbers' => $serialNumbersWithLinks
    ], 200);  
    });
    

    // end...

    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index')->middleware(AdminMiddleware::class);
    Route::get('/create-logs', [LogsController::class, 'create'])->name('logs.create')->middleware(AdminMiddleware::class);
    Route::get('/edit-logs/{logsId}', [LogsController::class, 'edit'])->name('logs.edit')->middleware(AdminMiddleware::class);
    Route::get('/show-logs/{logsId}', [LogsController::class, 'show'])->name('logs.show')->middleware(AdminMiddleware::class);
    Route::get('/delete-logs/{logsId}', [LogsController::class, 'delete'])->name('logs.delete')->middleware(AdminMiddleware::class);
    Route::get('/destroy-logs/{logsId}', [LogsController::class, 'destroy'])->name('logs.destroy')->middleware(AdminMiddleware::class);
    Route::post('/store-logs', [LogsController::class, 'store'])->name('logs.store')->middleware(AdminMiddleware::class);
    Route::post('/update-logs/{logsId}', [LogsController::class, 'update'])->name('logs.update')->middleware(AdminMiddleware::class);
    Route::post('/delete-all-bulk-data', [LogsController::class, 'bulkDelete'])->middleware(AdminMiddleware::class);

    // Logs Search
    Route::get('/logs-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $logs = Logs::when($search, function ($query) use ($search) {
            return $query->where('log', 'like', "%$search%");
        })->paginate(10);

        return view('logs.logs', compact('logs', 'search'));
    });

    // Logs Paginate
    Route::get('/logs-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the logs based on the 'paginate' value
        $logs = Logs::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated logs
        return view('logs.logs', compact('logs'));
    });

    // Logs Filter
    Route::get('/logs-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for logs
        $query = Logs::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $logs = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $logs = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $logs = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all logs without filtering
            $logs = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with logs and the selected date range
        return view('logs.logs', compact('logs', 'from', 'to'));
    });

    // end...

    Route::get('/types', [TypesController::class, 'index'])->name('types.index')->middleware(AdminMiddleware::class);
    Route::get('/create-types', [TypesController::class, 'create'])->name('types.create')->middleware(AdminMiddleware::class);
    Route::get('/edit-types/{typesId}', [TypesController::class, 'edit'])->name('types.edit')->middleware(AdminMiddleware::class);
    Route::get('/show-types/{typesId}', [TypesController::class, 'show'])->name('types.show')->middleware(AdminMiddleware::class);
    Route::get('/delete-types/{typesId}', [TypesController::class, 'delete'])->name('types.delete')->middleware(AdminMiddleware::class);
    Route::get('/destroy-types/{typesId}', [TypesController::class, 'destroy'])->name('types.destroy')->middleware(AdminMiddleware::class);
    Route::post('/store-types', [TypesController::class, 'store'])->name('types.store')->middleware(AdminMiddleware::class);
    Route::post('/update-types/{typesId}', [TypesController::class, 'update'])->name('types.update')->middleware(AdminMiddleware::class);
    Route::post('/types-delete-all-bulk-data', [TypesController::class, 'bulkDelete'])->middleware(AdminMiddleware::class);
    Route::post('/types-move-to-trash-all-bulk-data', [TypesController::class, 'bulkMoveToTrash'])->middleware(AdminMiddleware::class);
    Route::post('/types-restore-all-bulk-data', [TypesController::class, 'bulkRestore'])->middleware(AdminMiddleware::class);
    Route::get('/trash-types', [TypesController::class, 'trash'])->middleware(AdminMiddleware::class);
    Route::get('/restore-types/{typesId}', [TypesController::class, 'restore'])->name('types.restore')->middleware(AdminMiddleware::class);

    // Types Search
    Route::get('/types-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $types = Types::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('types.types', compact('types', 'search'));
    });

    // Types Paginate
    Route::get('/types-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the types based on the 'paginate' value
        $types = Types::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated types
        return view('types.types', compact('types'));
    });

    // Types Filter
    Route::get('/types-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for types
        $query = Types::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $types = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $types = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $types = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all types without filtering
            $types = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with types and the selected date range
        return view('types.types', compact('types', 'from', 'to'));
    });

    // end...

    Route::get('/items', [ItemsController::class, 'index'])->name('items.index')->middleware(AdminMiddleware::class);
    Route::get('/create-items', [ItemsController::class, 'create'])->name('items.create')->middleware(AdminMiddleware::class);
    Route::get('/edit-items/{itemsId}', [ItemsController::class, 'edit'])->name('items.edit')->middleware(AdminMiddleware::class);
    Route::get('/show-items/{itemsId}', [ItemsController::class, 'show'])->name('items.show')->middleware(AdminMiddleware::class);
    Route::get('/delete-items/{itemsId}', [ItemsController::class, 'delete'])->name('items.delete')->middleware(AdminMiddleware::class);
    Route::get('/destroy-items/{itemsId}', [ItemsController::class, 'destroy'])->name('items.destroy')->middleware(AdminMiddleware::class);
    Route::post('/store-items', [ItemsController::class, 'store'])->name('items.store')->middleware(AdminMiddleware::class);
    Route::post('/update-items/{itemsId}', [ItemsController::class, 'update'])->name('items.update')->middleware(AdminMiddleware::class);
    Route::post('/items-delete-all-bulk-data', [ItemsController::class, 'bulkDelete'])->middleware(AdminMiddleware::class);
    Route::post('/items-move-to-trash-all-bulk-data', [ItemsController::class, 'bulkMoveToTrash'])->middleware(AdminMiddleware::class);
    Route::post('/items-restore-all-bulk-data', [ItemsController::class, 'bulkRestore'])->middleware(AdminMiddleware::class);
    Route::get('/trash-items', [ItemsController::class, 'trash'])->middleware(AdminMiddleware::class);
    Route::get('/restore-items/{itemsId}', [ItemsController::class, 'restore'])->name('items.restore')->middleware(AdminMiddleware::class);

    // Items Search
    Route::get('/items-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $items = Items::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%")
                ->orWhere('itemId', 'like', "%$search%")
                ->orWhere('model', 'like', "%$search%")
                ->orWhere('brand', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%");

        })->paginate(10);

        return view('items.items', compact('items', 'search'));
    });

    // Items Paginate
    Route::get('/items-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the items based on the 'paginate' value
        $items = Items::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated items
        return view('items.items', compact('items'));
    });

    // Items Filter
    Route::get('/items-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for items
        $query = Items::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $items = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $items = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $items = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all items without filtering
            $items = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with items and the selected date range
        return view('items.items', compact('items', 'from', 'to'));
    });

    // end...

    Route::get('/sites', [SitesController::class, 'index'])->name('sites.index');
    Route::get('/create-sites', [SitesController::class, 'create'])->name('sites.create');
    Route::get('/edit-sites/{sitesId}', [SitesController::class, 'edit'])->name('sites.edit');
    Route::get('/show-sites/{sitesId}', [SitesController::class, 'show'])->name('sites.show');
    Route::get('/delete-sites/{sitesId}', [SitesController::class, 'delete'])->name('sites.delete');
    Route::get('/destroy-sites/{sitesId}', [SitesController::class, 'destroy'])->name('sites.destroy');
    Route::post('/store-sites', [SitesController::class, 'store'])->name('sites.store');
    Route::post('/update-sites/{sitesId}', [SitesController::class, 'update'])->name('sites.update');
    Route::post('/sites-delete-all-bulk-data', [SitesController::class, 'bulkDelete']);
    Route::post('/sites-move-to-trash-all-bulk-data', [SitesController::class, 'bulkMoveToTrash']);
    Route::post('/sites-restore-all-bulk-data', [SitesController::class, 'bulkRestore']);
    Route::get('/trash-sites', [SitesController::class, 'trash']);
    Route::get('/restore-sites/{sitesId}', [SitesController::class, 'restore'])->name('sites.restore');

    // Sites Search
    Route::get('/sites-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $sites = Sites::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%")
            ->orWhere('phonenumber', 'like', "%$search%");
        })->paginate(10);

        return view('sites.sites', compact('sites', 'search'));
    });

    // Sites Paginate
    Route::get('/sites-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the sites based on the 'paginate' value
        $sites = Sites::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated sites
        return view('sites.sites', compact('sites'));
    });

    // Sites Filter
    Route::get('/sites-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for sites
        $query = Sites::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $sites = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $sites = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $sites = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all sites without filtering
            $sites = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with sites and the selected date range
        return view('sites.sites', compact('sites', 'from', 'to'));
    });

    // end...

    Route::get('/technicians', [TechniciansController::class, 'index'])->name('technicians.index')->middleware(AdminMiddleware::class);
    Route::get('/create-technicians', [TechniciansController::class, 'create'])->name('technicians.create')->middleware(AdminMiddleware::class);
    Route::get('/edit-technicians/{techniciansId}', [TechniciansController::class, 'edit'])->name('technicians.edit')->middleware(AdminMiddleware::class);
    Route::get('/show-technicians/{techniciansId}', [TechniciansController::class, 'show'])->name('technicians.show')->middleware(AdminMiddleware::class);
    Route::get('/delete-technicians/{techniciansId}', [TechniciansController::class, 'delete'])->name('technicians.delete')->middleware(AdminMiddleware::class);
    Route::get('/destroy-technicians/{techniciansId}', [TechniciansController::class, 'destroy'])->name('technicians.destroy')->middleware(AdminMiddleware::class);
    Route::post('/store-technicians', [TechniciansController::class, 'store'])->name('technicians.store')->middleware(AdminMiddleware::class);
    Route::post('/update-technicians/{techniciansId}', [TechniciansController::class, 'update'])->name('technicians.update')->middleware(AdminMiddleware::class);
    Route::post('/technicians-delete-all-bulk-data', [TechniciansController::class, 'bulkDelete'])->middleware(AdminMiddleware::class);
    Route::post('/technicians-move-to-trash-all-bulk-data', [TechniciansController::class, 'bulkMoveToTrash'])->middleware(AdminMiddleware::class);
    Route::post('/technicians-restore-all-bulk-data', [TechniciansController::class, 'bulkRestore'])->middleware(AdminMiddleware::class);
    Route::get('/trash-technicians', [TechniciansController::class, 'trash'])->middleware(AdminMiddleware::class);
    Route::get('/restore-technicians/{techniciansId}', [TechniciansController::class, 'restore'])->name('technicians.restore')->middleware(AdminMiddleware::class);

    // Technicians Search
    Route::get('/technicians-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $technicians = User::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%");
        })->paginate(10);

        return view('technicians.technicians', compact('technicians', 'search'));
    });

    // Technicians Paginate
    Route::get('/technicians-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the technicians based on the 'paginate' value
        $technicians = Technicians::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated technicians
        return view('technicians.technicians', compact('technicians'));
    });

    // Technicians Filter
    Route::get('/technicians-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for technicians
        $query = Technicians::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $technicians = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $technicians = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $technicians = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all technicians without filtering
            $technicians = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with technicians and the selected date range
        return view('technicians.technicians', compact('technicians', 'from', 'to'));
    });

    // end...

    Route::get('/itemlogs', [ItemlogsController::class, 'index'])->name('itemlogs.index')->middleware(AdminMiddleware::class);
    Route::get('/create-itemlogs', [ItemlogsController::class, 'create'])->name('itemlogs.create')->middleware(AdminMiddleware::class);
    Route::get('/edit-itemlogs/{itemlogsId}', [ItemlogsController::class, 'edit'])->name('itemlogs.edit')->middleware(AdminMiddleware::class);
    Route::get('/show-itemlogs/{itemlogsId}', [ItemlogsController::class, 'show'])->name('itemlogs.show')->middleware(AdminMiddleware::class);
    Route::get('/delete-itemlogs/{itemlogsId}', [ItemlogsController::class, 'delete'])->name('itemlogs.delete')->middleware(AdminMiddleware::class);
    Route::get('/destroy-itemlogs/{itemlogsId}', [ItemlogsController::class, 'destroy'])->name('itemlogs.destroy')->middleware(AdminMiddleware::class);
    Route::post('/store-itemlogs', [ItemlogsController::class, 'store'])->name('itemlogs.store')->middleware(AdminMiddleware::class);
    Route::post('/update-itemlogs/{itemlogsId}', [ItemlogsController::class, 'update'])->name('itemlogs.update')->middleware(AdminMiddleware::class);
    Route::post('/itemlogs-delete-all-bulk-data', [ItemlogsController::class, 'bulkDelete'])->middleware(AdminMiddleware::class);
    Route::post('/itemlogs-move-to-trash-all-bulk-data', [ItemlogsController::class, 'bulkMoveToTrash'])->middleware(AdminMiddleware::class);
    Route::post('/itemlogs-restore-all-bulk-data', [ItemlogsController::class, 'bulkRestore'])->middleware(AdminMiddleware::class);
    Route::get('/trash-itemlogs', [ItemlogsController::class, 'trash'])->middleware(AdminMiddleware::class);
    Route::get('/restore-itemlogs/{itemlogsId}', [ItemlogsController::class, 'restore'])->name('itemlogs.restore')->middleware(AdminMiddleware::class);

    // Itemlogs Search
    Route::get('/itemlogs-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $itemlogs = Itemlogs::when($search, function ($query) use ($search) {
            return $query->whereHas('items', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        })->paginate(10);

        

        return view('itemlogs.itemlogs', compact('itemlogs', 'search'));
    });

    // Itemlogs Paginate
    Route::get('/itemlogs-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the itemlogs based on the 'paginate' value
        $itemlogs = Itemlogs::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated itemlogs
        return view('itemlogs.itemlogs', compact('itemlogs'));
    });

    // Itemlogs Filter
    Route::get('/itemlogs-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for itemlogs
        $query = Itemlogs::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $itemlogs = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $itemlogs = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $itemlogs = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all itemlogs without filtering
            $itemlogs = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with itemlogs and the selected date range
        return view('itemlogs.itemlogs', compact('itemlogs', 'from', 'to'));
    });

    // end...

    Route::get('/onsites', [OnsitesController::class, 'index'])->name('onsites.index');
    Route::get('/create-onsites', [OnsitesController::class, 'create'])->name('onsites.create');
    Route::get('/edit-onsites/{onsitesId}', [OnsitesController::class, 'edit'])->name('onsites.edit');
    Route::get('/show-onsites/{onsitesId}', [OnsitesController::class, 'show'])->name('onsites.show');
    Route::get('/delete-onsites/{onsitesId}', [OnsitesController::class, 'delete'])->name('onsites.delete');
    Route::get('/destroy-onsites/{onsitesId}', [OnsitesController::class, 'destroy'])->name('onsites.destroy');
    Route::post('/store-onsites', [OnsitesController::class, 'store'])->name('onsites.store');
    Route::post('/update-onsites/{onsitesId}', [OnsitesController::class, 'update'])->name('onsites.update');
    Route::post('/onsites-delete-all-bulk-data', [OnsitesController::class, 'bulkDelete']);
    Route::post('/onsites-move-to-trash-all-bulk-data', [OnsitesController::class, 'bulkMoveToTrash']);
    Route::post('/onsites-restore-all-bulk-data', [OnsitesController::class, 'bulkRestore']);
    Route::get('/trash-onsites', [OnsitesController::class, 'trash']);
    Route::get('/restore-onsites/{onsitesId}', [OnsitesController::class, 'restore'])->name('onsites.restore');

    // Onsites Search
    Route::get('/onsites-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $onsites = Onsites::when($search, function ($query) use ($search) {
            return $query->whereHas('sites', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        })->paginate(10);

        return view('onsites.onsites', compact('onsites', 'search'));
    });

    // Onsites Paginate
    Route::get('/onsites-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the onsites based on the 'paginate' value
        $onsites = Onsites::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated onsites
        return view('onsites.onsites', compact('onsites'));
    });

    // Onsites Filter
    Route::get('/onsites-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for onsites
        $query = Onsites::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $onsites = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $onsites = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $onsites = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all onsites without filtering
            $onsites = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with onsites and the selected date range
        return view('onsites.onsites', compact('onsites', 'from', 'to'));
    });

    // end...

    Route::get('/damages', [DamagesController::class, 'index'])->name('damages.index');
    Route::get('/create-damages', [DamagesController::class, 'create'])->name('damages.create');
    Route::get('/edit-damages/{damagesId}', [DamagesController::class, 'edit'])->name('damages.edit');
    Route::get('/show-damages/{damagesId}', [DamagesController::class, 'show'])->name('damages.show');
    Route::get('/delete-damages/{damagesId}', [DamagesController::class, 'delete'])->name('damages.delete');
    Route::get('/destroy-damages/{damagesId}', [DamagesController::class, 'destroy'])->name('damages.destroy');
    Route::post('/store-damages', [DamagesController::class, 'store'])->name('damages.store');
    Route::post('/update-damages/{damagesId}', [DamagesController::class, 'update'])->name('damages.update');
    Route::post('/damages-delete-all-bulk-data', [DamagesController::class, 'bulkDelete']);
    Route::post('/damages-move-to-trash-all-bulk-data', [DamagesController::class, 'bulkMoveToTrash']);
    Route::post('/damages-restore-all-bulk-data', [DamagesController::class, 'bulkRestore']);
    Route::get('/trash-damages', [DamagesController::class, 'trash']);
    Route::get('/restore-damages/{damagesId}', [DamagesController::class, 'restore'])->name('damages.restore');

    // Damages Search
    Route::get('/damages-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $damages = Damages::when($search, function ($query) use ($search) {
            return $query->whereHas('sites', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            });
        })->paginate(10);

        return view('damages.damages', compact('damages', 'search'));
    });

    // Damages Paginate
    Route::get('/damages-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the damages based on the 'paginate' value
        $damages = Damages::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated damages
        return view('damages.damages', compact('damages'));
    });

    // Damages Filter
    Route::get('/damages-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for damages
        $query = Damages::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $damages = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $damages = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $damages = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all damages without filtering
            $damages = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with damages and the selected date range
        return view('damages.damages', compact('damages', 'from', 'to'));
    });

    // end...

    Route::get('/deployedtechnicians', [DeployedtechniciansController::class, 'index'])->name('deployedtechnicians.index');
    Route::get('/create-deployedtechnicians', [DeployedtechniciansController::class, 'create'])->name('deployedtechnicians.create');
    Route::get('/edit-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'edit'])->name('deployedtechnicians.edit');
    Route::get('/show-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'show'])->name('deployedtechnicians.show');
    Route::get('/delete-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'delete'])->name('deployedtechnicians.delete');
    Route::get('/destroy-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'destroy'])->name('deployedtechnicians.destroy');
    Route::post('/store-deployedtechnicians', [DeployedtechniciansController::class, 'store'])->name('deployedtechnicians.store');
    Route::post('/update-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'update'])->name('deployedtechnicians.update');
    Route::post('/deployedtechnicians-delete-all-bulk-data', [DeployedtechniciansController::class, 'bulkDelete']);
    Route::post('/deployedtechnicians-move-to-trash-all-bulk-data', [DeployedtechniciansController::class, 'bulkMoveToTrash']);
    Route::post('/deployedtechnicians-restore-all-bulk-data', [DeployedtechniciansController::class, 'bulkRestore']);
    Route::get('/trash-deployedtechnicians', [DeployedtechniciansController::class, 'trash']);
    Route::get('/restore-deployedtechnicians/{deployedtechniciansId}', [DeployedtechniciansController::class, 'restore'])->name('deployedtechnicians.restore');

    // Deployedtechnicians Search
    Route::get('/deployedtechnicians-search', function (Request $request) {
        $search = $request->get('search');

        // Perform the search logic
        $deployedtechnicians = Deployedtechnicians::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', "%$search%");
        })->paginate(10);

        return view('deployedtechnicians.deployedtechnicians', compact('deployedtechnicians', 'search'));
    });

    // Deployedtechnicians Paginate
    Route::get('/deployedtechnicians-paginate', function (Request $request) {
        // Retrieve the 'paginate' parameter from the URL (e.g., ?paginate=10)
        $paginate = $request->input('paginate', 10); // Default to 10 if no paginate value is provided
    
        // Paginate the deployedtechnicians based on the 'paginate' value
        $deployedtechnicians = Deployedtechnicians::paginate($paginate); // Paginate with the specified number of items per page
    
        // Return the view with the paginated deployedtechnicians
        return view('deployedtechnicians.deployedtechnicians', compact('deployedtechnicians'));
    });

    // Deployedtechnicians Filter
    Route::get('/deployedtechnicians-filter', function (Request $request) {
        // Retrieve 'from' and 'to' dates from the URL
        $from = $request->input('from');
        $to = $request->input('to');
    
        // Default query for deployedtechnicians
        $query = Deployedtechnicians::query();
    
        // Convert dates to Carbon instances for better comparison
        $fromDate = $from ? Carbon::parse($from) : null;
        $toDate = $to ? Carbon::parse($to) : null;
    
        // Check if both 'from' and 'to' dates are provided
        if ($from && $to) {
            // If 'from' and 'to' are the same day (today)
            if ($fromDate->isToday() && $toDate->isToday()) {
                // Return results from today and include the 'from' date's data
                $deployedtechnicians = $query->whereDate('created_at', '=', Carbon::today())
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
            } else {
                // If 'from' date is greater than 'to' date, order ascending (from 'to' to 'from')
                if ($fromDate->gt($toDate)) {
                    $deployedtechnicians = $query->whereBetween('created_at', [$toDate, $fromDate])
                                   ->orderBy('created_at', 'asc')  // Ascending order
                                   ->paginate(10);
                } else {
                    // Otherwise, order descending (from 'from' to 'to')
                    $deployedtechnicians = $query->whereBetween('created_at', [$fromDate, $toDate])
                                   ->orderBy('created_at', 'desc')  // Descending order
                                   ->paginate(10);
                }
            }
        } else {
            // If 'from' or 'to' are missing, show all deployedtechnicians without filtering
            $deployedtechnicians = $query->paginate(10);  // Paginate results
        }
    
        // Return the view with deployedtechnicians and the selected date range
        return view('deployedtechnicians.deployedtechnicians', compact('deployedtechnicians', 'from', 'to'));
    });

    // end...

});

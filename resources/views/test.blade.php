<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container-fluid py-3">
        <div class="row">

            <!-- Left Side: Search & Product List -->
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Search Products</h5>
                    </div>
                    <div class="card-body">
                        <!-- Search Bar -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Search by Name, Barcode, or Product Code">
                            <button class="btn btn-outline-primary">Search</button>
                        </div>

                        <!-- Product List -->
                        <div class="list-group" style="max-height: 400px; overflow-y: auto;">
                            <!-- Example Product -->
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">Product A</h6>
                                    <small>Barcode: 12345 | Code: P001</small>
                                </div>
                                <button class="btn btn-sm btn-success">Add</button>
                            </div>
                            <!-- Repeat dynamically -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Added Items & Totals -->
            <div class="col-md-6 mt-3 mt-md-0">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Selected Items</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Example Row -->
                                <tr>
                                    <td>Product A</td>
                                    <td><input type="number" class="form-control form-control-sm" value="50"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="75"></td>
                                    <td><input type="number" class="form-control form-control-sm" value="1"></td>
                                    <td>$75</td>
                                    <td><button class="btn btn-sm btn-danger">X</button></td>
                                </tr>
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <div class="d-flex justify-content-end">
                            <div class="text-end">
                                <h6>Total Items: <span class="badge bg-primary">1</span></h6>
                                <h5>Grand Total: <span class="text-success">$75</span></h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button class="btn btn-primary">Save</button>
                        <button class="btn btn-secondary">Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
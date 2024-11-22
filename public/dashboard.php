<?php
// Start session and check if the user is logged in
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit;
}

// Include necessary files (DB connection, header, footer)
include '../includes/db.php';
include '../includes/header.php';
include '../includes/auth.php';

// Check if the user is an admin, if so, redirect them to the admin dashboard
if ($_SESSION['role'] === 'admin') {
    header('Location: admin.php');
    exit;
}

// Get the logged-in user ID
$user_id = $_SESSION['user_id'];

// Fetch user's exchange requests from the database
$requests_query = "SELECT * FROM exchange_requests WHERE user_id = '$user_id' ORDER BY created_at DESC";
$requests_result = $conn->query($requests_query);
// Count exchange requests by status
$status_counts_query = "
    SELECT 
        status, 
        COUNT(*) as count 
    FROM exchange_requests 
    WHERE user_id = '$user_id' 
    GROUP BY status";
$status_counts_result = $conn->query($status_counts_query);

// Initialize counts
$status_counts = [
    'Pending' => 0,
    'Approved' => 0,
    'Rejected' => 0
];

while ($row = $status_counts_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}

 // Fetch total number of transactions
 // Fetch total transactions for the user
$total_transactions_query = "
SELECT COUNT(*) as total 
FROM exchange_requests 
WHERE user_id = '$user_id'";
$total_transactions_result = $conn->query($total_transactions_query);

// Default total transactions to 0
$total_transactions = 0;

if ($row = $total_transactions_result->fetch_assoc()) {
$total_transactions = $row['total'];
}



?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Dashboard Content -->
<main class="container mx-auto p-4">
    

    <h1 class="text-3xl font-semibold text-center mb-8">Your Dashboard</h1>
    <!-- this is the transation tips--->
    <section class="mb-8">
    <h2 class="text-2xl font-bold mb-4">Transactions Overview</h2>
    <div class="grid grid-cols-3 gap-4 text-center">
        <div class="bg-blue-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Total Transactions</h3>
            <p class="text-3xl font-semibold"><?php echo $total_transactions; ?></p>
        </div>
        <div class="bg-green-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Approved</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['Approved']; ?></p>
        </div>
        <div class="bg-yellow-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Pending</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['Pending']; ?></p>
        </div>
        <div class="bg-red-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Rejected</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['Rejected']; ?></p>
        </div>
    </div>
</section>

<!--- this is chart start--->
    <section class="mb-8 ">
    <h2 class="text-2xl font-bold mb-4">Exchange Requests Overview</h2>
    <div class="bg-white p-6 rounded shadow-md">
        <canvas id="statusPolarChart" class="w-full h-96"></canvas>
    </div>
</section>


    <!-- New Exchange Request Section -->
    <section class="mb-8">
    <h2 class="text-2xl font-bold mb-4">Make a New Exchange Request</h2>
    
    <form action="submit_request.php" method="POST" class="space-y-4 bg-white p-6 rounded shadow-md" enctype="multipart/form-data">
        <div class="flex space-x-4">
            <!-- From Currency -->
            <div class="w-1/2">
                <label for="from_currency" class="block text-sm font-medium text-gray-700">From Currency</label>
                <select name="from_currency" id="from_currency" class="w-full border-gray-300 rounded-md border p-2 font-bold" required>
                    <option value="INR">INR</option>
                    <option value="FRW">FRW</option>
                    <!-- Add more currencies here -->
                </select>
            </div>

            <!-- To Currency -->
            <div class="w-1/2">
                <label for="to_currency" class="block text-sm font-medium text-gray-700">To Currency</label>
                <select name="to_currency" id="to_currency" class="w-full border-gray-300 rounded-md border p-2 font-bold" required>
                    <option value="INR">INR</option>
                    <option value="FRW">FRW</option>
                    <!-- Add more currencies here -->
                </select>
            </div>
        </div>

        <!-- Amount -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
            <input type="number" name="amount" id="amount" class="w-full border-gray-300 rounded-md border p-2 font-bold" required>
        </div>

        <!-- Payment Method -->
        <div>
            <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method</label>
            <select name="payment_method" id="payment_method" class="w-full border-gray-300 rounded-md p-2 border font-bold" required>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="PayPal">PayPal</option>
                <option value="Cash">Cash</option>
                <!-- Add more payment methods here -->
            </select>
        </div>

        <!-- Payment Screenshot -->
        <div>
            <label for="payment_screenshot" class="block text-sm font-medium text-gray-700">Payment Screenshot</label>
            <input type="file" name="payment_screenshot" id="payment_screenshot" class="w-full border-gray-300 rounded-md p-2 border" accept="image/*" required>
        </div>

        <!-- Hidden status field (default to 'Pending') -->
        <input type="hidden" name="status" value="Pending">

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full">Submit Request</button>
    </form>
</section>


    <!-- Your Previous Exchange Requests Section -->
    <section>
        <h2 class="text-2xl font-bold mb-4">Your Previous Exchange Requests</h2>
        
        <?php if ($requests_result->num_rows > 0): ?>
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">From Currency</th>
                        <th class="px-4 py-2 border">To Currency</th>
                        <th class="px-4 py-2 border">Amount</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Admin Screenshot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo $request['from_currency']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $request['to_currency']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $request['amount']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $request['status']; ?></td>
                            <td class="px-4 py-2 border"><?php echo $request['created_at']; ?></td>
                            <td class="px-4 py-2 border">
                                <?php if (!empty($request['admin_screenshot'])): ?>
                                <a href="uploads/payment_screenshots/<?php echo htmlspecialchars($request['admin_screenshot']); ?>" target="_blank" title="View Full Image">
            <img src="uploads/payment_screenshots/<?php echo htmlspecialchars($request['admin_screenshot']); ?>" alt="Admin Screenshot" class="max-w-[150px] h-40 rounded shadow">
        </a>
                                <?php else: ?>
                                <p class="text-center p-2 text-red-600 font-bold border">▲ : Wait for Admin payment screenshot !</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">You have no exchange requests yet.</p>
        <?php endif; ?>
    </section>

</main>
<?php include '../includes/footer.php'; ?>

<script>
    // Data for the Polar Area Chart
    const statusData = {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            data: [
                <?php echo $status_counts['Pending']; ?>,
                <?php echo $status_counts['Approved']; ?>,
                <?php echo $status_counts['Rejected']; ?>
            ],
            backgroundColor: [
                'rgba(255, 205, 86, 0.7)', // Pending - Yellow
                'rgba(75, 192, 192, 0.7)', // Approved - Green
                'rgba(255, 99, 132, 0.7)'  // Rejected - Red
            ],
            borderColor: [
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 1
        }]
    };

    // Polar Area Chart Configuration
    const config = {
        type: 'polarArea',
        data: statusData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    };

    // Render the Chart
    const ctx = document.getElementById('statusPolarChart').getContext('2d');
    new Chart(ctx, config);
</script>



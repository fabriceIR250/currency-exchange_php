<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php'); // Redirect to login if not an admin
    exit;
}

// Include necessary files
include '../includes/db.php';
include '../includes/header.php';

// Fetch exchange requests with usernames
$requests_query = "
    SELECT exchange_requests.*, users.username 
    FROM exchange_requests 
    JOIN users ON exchange_requests.user_id = users.id 
    ORDER BY exchange_requests.created_at DESC";
$requests_result = $conn->query($requests_query);

// Fetch current exchange rates
$rates_query = "SELECT * FROM exchange_rates";
$rates_result = $conn->query($rates_query);

// chart make rate chart
$status_counts_query = "
    SELECT 
        status, 
        COUNT(*) as count 
    FROM exchange_requests 
    GROUP BY status";
$status_counts_result = $conn->query($status_counts_query);

// Initialize the counts for each status
$status_counts = [
    'approved' => 0,
    'pending' => 0,
    'rejected' => 0
];

// Populate the counts from the query results
while ($row = $status_counts_result->fetch_assoc()) {
    $status = strtolower($row['status']); // Ensure case-insensitivity
    $status_counts[$status] = (int)$row['count'];
}

// Calculate total transactions
$total_transactions = array_sum($status_counts);

?>

<main class="container mx-auto p-4 ">
    <!-- Admin Navigation -->
    <nav class="bg-blue-600 text-white px-4 py-3 rounded mb-6 flex justify-between items-center">
        <h1 class="text-xl font-bold">Admin Dashboard</h1>
        <div>
            <a href="../public/cont_view.php" class="hover:underline text-white font-medium">View Contact Messages</a>
        </div>
    </nav>

    <?php if (isset($_GET['message'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <h1 class="text-3xl font-semibold text-center mb-8">Admin Dashboard</h1>

    <!-- Exchange Requests Section -->
    <section class="mb-8">
    <h2 class="text-2xl font-bold mb-4">Transactions Overview</h2>
    <div class="grid grid-cols-3 gap-4 text-center">
        <div class="bg-blue-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Total Transactions</h3>
            <p class="text-3xl font-semibold"><?php echo $total_transactions; ?></p>
        </div>
        <div class="bg-green-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Approved</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['approved']; ?></p>
        </div>
        <div class="bg-yellow-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Pending</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['pending']; ?></p>
        </div>
        <div class="bg-red-100 p-6 rounded shadow">
            <h3 class="text-lg font-bold">Rejected</h3>
            <p class="text-3xl font-semibold"><?php echo $status_counts['rejected']; ?></p>
        </div>
    </div>
</section>

    <!-- Radar Chart -->
     <div class="bg-blue-50 p-6 rounded shadow">
    <canvas id="statusDoughnutChart" width="400" height="400"></canvas>
    </div>
<!-- end this -->
    <section class="mb-8">
        <h2 class="text-2xl font-bold mb-4">User Exchange Requests</h2>
        
        <?php if ($requests_result->num_rows > 0): ?>
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">User ID</th>
                        <th class="px-4 py-2 border">Username</th>
                        <th class="px-4 py-2 border">Amount</th>
                        <th class="px-4 py-2 border">From Currency</th>
                        <th class="px-4 py-2 border">To Currency</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">User Payment Screenshot</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $requests_result->fetch_assoc()): ?>
                        <tr>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['user_id']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['username']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['amount']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['from_currency']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['to_currency']); ?></td>
                            <td class="px-4 py-2 border"><?php echo htmlspecialchars($request['status']); ?></td>
                            <td class="px-4 py-2 border">
                                <?php if (!empty($request['payment_screenshot'])): ?>
                                    <a href="uploads/payment_screenshots/<?php echo htmlspecialchars($request['payment_screenshot']); ?>" target="_blank" title="View Full Image">
                                        <img src="uploads/payment_screenshots/<?php echo htmlspecialchars($request['payment_screenshot']); ?>" alt="User Screenshot" class="max-w-[150px] h-32 rounded shadow">
                                    </a>
                                <?php else: ?>
                                    <p>No screenshot uploaded</p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-2 border">
                                <a title="APPROVE" href="approve_request.php?id=<?php echo $request['id']; ?>" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">✔</a>
                                <a title="REJECT" href="reject_request.php?id=<?php echo $request['id']; ?>" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">❌</a>
                                <button onclick="showUploadForm(<?php echo $request['id']; ?>)" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">📷 Screenshot</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No exchange requests found.</p>
        <?php endif; ?>
    </section>

    <!-- Admin Screenshot Upload Form (Hidden initially) -->
    <div id="uploadFormContainer" style="display: none;" class="fixed inset-0 flex items-center justify-center bg-gray-700 bg-opacity-50">
        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full">
            <h2 class="text-2xl font-bold mb-4">Upload Admin Screenshot</h2>

            <form action="admin_screenshot.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="request_id" id="request_id">

                <div>
                    <label for="admin_screenshot" class="block text-sm font-medium text-gray-700">Admin Screenshot</label>
                    <input type="file" name="admin_screenshot" id="admin_screenshot" class="w-full border-gray-300 rounded-md" accept="image/*" required>
                </div>

                <div class="mt-4 text-right">
                    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Upload Screenshot</button>
                    <button type="button" onclick="hideUploadForm()" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 ml-2">Cancel</button>
                </div>
            </form>
        </div>
    </div>

</main>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Doughnut Chart Data
const doughnutData = {
    labels: ['Approved', 'Pending', 'Rejected'],
    datasets: [
        {
            label: 'Transaction Status',
            data: [
                <?php echo $status_counts['approved']; ?>,
                <?php echo $status_counts['pending']; ?>,
                <?php echo $status_counts['rejected']; ?>
            ],
            backgroundColor: [
                'rgba(75, 192, 192, 0.6)', // Approved
                'rgba(255, 206, 86, 0.6)', // Pending
                'rgba(255, 99, 132, 0.6)'  // Rejected
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)', // Approved
                'rgba(255, 206, 86, 1)', // Pending
                'rgba(255, 99, 132, 1)'  // Rejected
            ],
            borderWidth: 1
        }
    ]
};

// Doughnut Chart Configuration
const doughnutConfig = {
    type: 'doughnut',
    data: doughnutData,
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top' // Position of the legend
            },
            tooltip: {
                callbacks: {
                    label: function (context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        return `${label}: ${value}`;
                    }
                }
            }
        }
    }
};

// Render Doughnut Chart
const doughnutCtx = document.getElementById('statusDoughnutChart').getContext('2d');
new Chart(doughnutCtx, doughnutConfig);
</script>


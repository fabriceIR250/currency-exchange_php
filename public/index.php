<?php
session_start();

// Include necessary files (DB connection, header, footer)
include '../includes/db.php';
include '../includes/header.php';

// Fetch exchange rates from the database (for conversion)
$rates_query = "SELECT * FROM exchange_rates";
$rates_result = $conn->query($rates_query);
$rates = [];
if ($rates_result->num_rows > 0) {
    while ($rate = $rates_result->fetch_assoc()) {
        $rates[$rate['from_currency']][$rate['to_currency']] = $rate['rate'];
    }
}

?>

<!-- Currency Converter Interface -->
<main class="container mx-auto p-4">

    <h1 class="text-3xl font-semibold text-center mb-8">Currency Exchange Converter</h1>

    <!-- Conversion Form -->
    <form action="" method="POST" class="space-y-4 bg-white p-6 rounded shadow-md">
        <div class="flex space-x-4">
            <!-- From Currency -->
            <div class="w-1/2">
                <label for="from_currency" class="block text-sm font-medium text-gray-700">From Currency</label>
                <select name="from_currency" id="from_currency" class="w-full border-gray-300 rounded-md">
                    <option value="INR">INR</option>
                    <option value="FRW">FRW</option>
                    <!-- Add more currencies here -->
                </select>
            </div>

            <!-- To Currency -->
            <div class="w-1/2">
                <label for="to_currency" class="block text-sm font-medium text-gray-700">To Currency</label>
                <select name="to_currency" id="to_currency" class="w-full border-gray-300 rounded-md">
                    <option value="INR">INR</option>
                    <option value="FRW">FRW</option>
                    <!-- Add more currencies here -->
                </select>
            </div>
        </div>

        <!-- Amount -->
        <div>
            <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
            <input type="number" name="amount" id="amount" class="w-full border-gray-300 rounded-md" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Convert</button>
    </form>

    <?php
    // Handle form submission and currency conversion
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $from_currency = $_POST['from_currency'];
        $to_currency = $_POST['to_currency'];
        $amount = $_POST['amount'];

        // Check if the rate exists
        if (isset($rates[$from_currency][$to_currency])) {
            $rate = $rates[$from_currency][$to_currency];
            $converted_amount = $amount * $rate;

            // Display the conversion result
            echo "<div class='mt-8 text-center'>
                    <h2 class='text-2xl font-semibold'>Conversion Result</h2>
                    <p class='text-lg'>${amount} ${from_currency} = " . round($converted_amount, 2) . " ${to_currency}</p>
                  </div>";
        } else {
            echo "<div class='mt-8 text-center text-red-600'>
                    <p class='text-lg'>Sorry, the conversion rate for ${from_currency} to ${to_currency} is not available.</p>
                  </div>";
        }
    }
    ?>

</main>

<?php include '../includes/footer.php'; ?>

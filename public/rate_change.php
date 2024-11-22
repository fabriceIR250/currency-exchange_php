<?php
// Start session and include database connection
session_start();
include '../includes/db.php';

// Handle form submission to update the exchange rates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the new rates from the form
    $inr_to_frw = $_POST['inr_to_frw'] ?? null;
    $frw_to_inr = $_POST['frw_to_inr'] ?? null;

    // Validate and update the INR to FRW rate
    if ($inr_to_frw !== null && is_numeric($inr_to_frw)) {
        $update_inr_frw = "UPDATE exchange_rates SET rate = '$inr_to_frw' WHERE from_currency = 'INR' AND to_currency = 'FRW'";
        if (!$conn->query($update_inr_frw)) {
            $message_inr_frw = "Failed to update INR to FRW exchange rate.";
        } else {
            $message_inr_frw = "INR to FRW exchange rate updated successfully!";
        }
    }

    // Validate and update the FRW to INR rate
    if ($frw_to_inr !== null && is_numeric($frw_to_inr)) {
        $update_frw_inr = "UPDATE exchange_rates SET rate = '$frw_to_inr' WHERE from_currency = 'FRW' AND to_currency = 'INR'";
        if (!$conn->query($update_frw_inr)) {
            $message_frw_inr = "Failed to update FRW to INR exchange rate.";
        } else {
            $message_frw_inr = "FRW to INR exchange rate updated successfully!";
        }
    }
}

// Fetch the current rates for INR to FRW and FRW to INR
$inr_to_frw_rate = 0;
$frw_to_inr_rate = 0;
$rate_query = "SELECT rate, from_currency, to_currency FROM exchange_rates WHERE (from_currency = 'INR' AND to_currency = 'FRW') OR (from_currency = 'FRW' AND to_currency = 'INR')";
$result = $conn->query($rate_query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($row['from_currency'] == 'INR' && $row['to_currency'] == 'FRW') {
            $inr_to_frw_rate = $row['rate'];
        } elseif ($row['from_currency'] == 'FRW' && $row['to_currency'] == 'INR') {
            $frw_to_inr_rate = $row['rate'];
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Exchange Rates</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center mb-6">Update Exchange Rates</h1>

        <!-- Display current rates -->
        <div class="text-center mb-4">
            <p class="text-lg">Current Exchange Rates:</p>
            <p class="text-lg">INR to FRW: <span class="font-semibold"><?php echo number_format($inr_to_frw_rate, 2); ?></span></p>
            <p class="text-lg">FRW to INR: <span class="font-semibold"><?php echo number_format($frw_to_inr_rate, 2); ?></span></p>
        </div>

        <!-- Display message for INR to FRW rate update -->
        <?php if (isset($message_inr_frw)): ?>
            <div class="text-center mb-4">
                <p class="text-lg <?php echo strpos($message_inr_frw, 'successfully') !== false ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $message_inr_frw; ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Display message for FRW to INR rate update -->
        <?php if (isset($message_frw_inr)): ?>
            <div class="text-center mb-4">
                <p class="text-lg <?php echo strpos($message_frw_inr, 'successfully') !== false ? 'text-green-600' : 'text-red-600'; ?>">
                    <?php echo $message_frw_inr; ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Update Rates Form -->
        <form action="" method="POST" class="max-w-md mx-auto bg-white p-6 rounded shadow-md">
            <!-- INR to FRW Rate -->
            <div class="mb-4">
                <label for="inr_to_frw" class="block text-sm font-medium text-gray-700">New Exchange Rate (INR to FRW)</label>
                <input type="text" name="inr_to_frw" id="inr_to_frw" 
                    class="w-full border-gray-300 rounded-md p-2" required 
                    value="<?php echo $inr_to_frw_rate; ?>" placeholder="Enter INR to FRW rate" />
            </div>

            <!-- FRW to INR Rate -->
            <div class="mb-4">
                <label for="frw_to_inr" class="block text-sm font-medium text-gray-700">New Exchange Rate (FRW to INR)</label>
                <input type="text" name="frw_to_inr" id="frw_to_inr" 
                    class="w-full border-gray-300 rounded-md p-2" required 
                    value="<?php echo $frw_to_inr_rate; ?>" placeholder="Enter FRW to INR rate" />
            </div>

            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                Update Rates
            </button>
        </form>
    </div>
</body>
</html>

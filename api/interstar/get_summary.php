<?php
include 'db_config.php';

$tiers = [
    'simple' => ['table' => 'qrcodegenerate', 'display' => 'SIMPLE'],
    'vip'    => ['table' => 'qrcodevip', 'display' => 'VIP'],
    'vvip'   => ['table' => 'qrcodevvip', 'display' => 'VVIP']
];

// Initialize totals
$totalGenerated = 0;
$totalVerifiedQty = 0;
$totalVerifiedCash = 0;
$totalPendingQty = 0;
$totalPendingCash = 0;
$totalEntryCount = 0;

foreach ($tiers as $name => $info) {
    $tbl = $info['table'];
    $displayName = $info['display'];
    
    // 1. Total Generated
    $totalRow = $conn->query("SELECT COUNT(*) as cnt FROM $tbl")->fetch_assoc();
    $totalCount = $totalRow['cnt'] ?? 0;
    $totalGenerated += $totalCount;

    // 2. Verified Stats (Count and Sum of Price)
    $vData = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tbl WHERE verified='Verified'")->fetch_assoc();
    $vQty = $vData['cnt'] ?? 0;
    $vCash = $vData['amt'] ?? 0;
    $totalVerifiedQty += $vQty;
    $totalVerifiedCash += $vCash;

    // 3. Pending Stats (Count and Sum of Price)
    $pData = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tbl WHERE verified='Pending'")->fetch_assoc();
    $pQty = $pData['cnt'] ?? 0;
    $pCash = $pData['amt'] ?? 0;
    $totalPendingQty += $pQty;
    $totalPendingCash += $pCash;

    // 4. Entry Count (count rows where entry = 1)
    $entryData = $conn->query("SELECT COUNT(*) as cnt FROM $tbl WHERE entry = 1")->fetch_assoc();
    $entryCount = $entryData['cnt'] ?? 0;
    $totalEntryCount += $entryCount;

    // 5. Get current unit price (most recent)
    $lastPriceRow = $conn->query("SELECT price FROM $tbl WHERE price > 0 ORDER BY id DESC LIMIT 1")->fetch_assoc();
    $unitPrice = $lastPriceRow['price'] ?? 0;

    echo "<tr>
            <td><b>" . $displayName . "</b></td>
            <td class='total-generated'>" . number_format($totalCount) . "</td>
            <td class='price-per-ticket'>" . number_format($unitPrice) . " Fbu</td>
            <td class='verified-qty'>" . number_format($vQty) . "</td>
            <td class='verified-cash'><span style='color:green; font-weight:bold;'>" . number_format($vCash) . " Fbu</span></td>
            <td class='pending-qty'>" . number_format($pQty) . "</td>
            <td class='pending-cash'><span style='color:orange; font-weight:bold;'>" . number_format($pCash) . " Fbu</span></td>
            <td class='entry-qty'><span style='color:blue; font-weight:bold;'>" . number_format($entryCount) . "</span></td>
          </tr>";
}

// Calculate average price per ticket (weighted average)
$avgPrice = $totalGenerated > 0 ? round(($totalVerifiedCash + $totalPendingCash) / $totalGenerated) : 0;

// Add total row with all columns
echo "<tr style='border-top: 3px solid #FB8500; font-weight: 800; background: #f0f0f0; font-size: 15px;'>
        <td><b>TOTAL / AVG</b></td>
        <td><b>" . number_format($totalGenerated) . "</b></td>
        <td><b>" . number_format($avgPrice) . " Fbu</b> <span style='font-size: 11px; font-weight: normal; color: #666;'>(avg)</span></td>
        <td><b>" . number_format($totalVerifiedQty) . "</b></td>
        <td><b><span style='color:green;'>" . number_format($totalVerifiedCash) . " Fbu</span></b></td>
        <td><b>" . number_format($totalPendingQty) . "</b></td>
        <td><b><span style='color:orange;'>" . number_format($totalPendingCash) . " Fbu</span></b></td>
        <td><b><span style='color:blue;'>" . number_format($totalEntryCount) . "</span></b></td>
      </tr>";

// Add a second total row with percentages
$verificationRate = $totalGenerated > 0 ? round(($totalVerifiedQty / $totalGenerated) * 100) : 0;
$entryRate = $totalGenerated > 0 ? round(($totalEntryCount / $totalGenerated) * 100) : 0;

echo "<tr style='background: #fafafa; border-bottom: 2px solid #ddd;'>
        <td><b>STATISTICS</b></td>
        <td colspan='2'>Total Tickets: <b>" . number_format($totalGenerated) . "</b></td>
        <td colspan='2'>Verification Rate: <b>" . $verificationRate . "%</b></td>
        <td colspan='2'>Pending: <b>" . number_format($totalPendingQty) . "</b></td>
        <td>Entry Rate: <b>" . $entryRate . "%</b></td>
      </tr>";

// Add grand total revenue row
$grandTotal = $totalVerifiedCash + $totalPendingCash;
echo "<tr style='background: #e8e8e8; font-weight: 800;'>
        <td><b>GRAND TOTAL REVENUE</b></td>
        <td colspan='4' style='text-align: right;'>Expected Revenue:</td>
        <td colspan='3' style='font-size: 18px; color: #FB8500;'><b>" . number_format($grandTotal) . " Fbu</b></td>
      </tr>";
?>
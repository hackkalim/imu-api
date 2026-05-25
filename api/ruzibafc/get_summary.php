<?php
include 'db_config.php';

// ============================================
// COMPLETE CATEGORY STRUCTURE WITH SUB-CATEGORIES
// ============================================

$tiers = [
    'simple' => [
        'display' => 'SIMPLE QR',
        'icon' => 'fa-ticket-alt',
        'color' => '#FB8500',
        'bg_color' => '#FFF4E5',
        'dark_color' => '#e67a00',
        'main_categories' => ['C0', 'C1', 'C2', 'C3', 'C4', 'C5', 'C6', 'C7', 'C8', 'C9'],
        'sub_categories' => ['_1', '_2', '_3', '_4', '_5'],
        'table_prefix' => 'qrcodegenerate'
    ],
    'vip' => [
        'display' => 'VIP QR',
        'icon' => 'fa-crown',
        'color' => '#9c27b0',
        'bg_color' => '#F3E5F5',
        'dark_color' => '#7b1fa2',
        'main_categories' => ['V0', 'V1', 'V2', 'V3', 'V4', 'V5', 'V6', 'V7', 'V8', 'V9'],
        'sub_categories' => ['_1', '_2', '_3', '_4', '_5'],
        'table_prefix' => 'qrcodevip'
    ],
    'vvip' => [
        'display' => 'VVIP QR',
        'icon' => 'fa-gem',
        'color' => '#FFD700',
        'bg_color' => '#FEF9E6',
        'dark_color' => '#e6c300',
        'main_categories' => ['W0', 'W1', 'W2', 'W3', 'W4', 'W5', 'W6', 'W7', 'W8', 'W9'],
        'sub_categories' => ['_1', '_2', '_3', '_4', '_5'],
        'table_prefix' => 'qrcodevvip'
    ]
];

// Function to get table name based on tier, main category, and sub-category
function getTableName($tier, $mainCat, $subCat) {
    $prefix = $tier['table_prefix'];
    $mainCode = $mainCat;
    
    // For _1 (main table) - use original naming
    if ($subCat === '_1') {
        if ($tier['display'] === 'SIMPLE QR') {
            if ($mainCat === 'C0') return 'qrcodegenerate';
            return 'qrcodegenerate' . strtolower($mainCat);
        } elseif ($tier['display'] === 'VIP QR') {
            if ($mainCat === 'V0') return 'qrcodevip';
            return 'qrcodevip' . strtolower($mainCat);
        } else { // VVIP
            if ($mainCat === 'W0') return 'qrcodevvip';
            return 'qrcodevvip' . strtolower($mainCat);
        }
    }
    
    // For _2 to _5 - use new naming convention
    if ($tier['display'] === 'SIMPLE QR') {
        return $prefix . $mainCat . $subCat;
    } elseif ($tier['display'] === 'VIP QR') {
        return $prefix . $mainCat . $subCat;
    } else {
        return $prefix . $mainCat . $subCat;
    }
}

// Initialize grand totals
$grandTotals = [
    'total_generated' => 0,
    'total_verified_qty' => 0,
    'total_verified_cash' => 0,
    'total_pending_qty' => 0,
    'total_pending_cash' => 0,
    'total_entry_count' => 0,
    'categories_with_data' => 0
];

// Store all data for display
$allData = [];

// Process each tier
foreach ($tiers as $tierKey => $tier) {
    $tierData = [
        'display' => $tier['display'],
        'icon' => $tier['icon'],
        'color' => $tier['color'],
        'bg_color' => $tier['bg_color'],
        'dark_color' => $tier['dark_color'],
        'main_categories' => []
    ];
    
    $tierTotals = [
        'total_generated' => 0,
        'total_verified_qty' => 0,
        'total_verified_cash' => 0,
        'total_pending_qty' => 0,
        'total_pending_cash' => 0,
        'total_entry_count' => 0
    ];
    
    foreach ($tier['main_categories'] as $mainCat) {
        $mainCatData = [
            'code' => $mainCat,
            'sub_categories' => [],
            'totals' => [
                'total_generated' => 0,
                'total_verified_qty' => 0,
                'total_verified_cash' => 0,
                'total_pending_qty' => 0,
                'total_pending_cash' => 0,
                'total_entry_count' => 0
            ]
        ];
        
        foreach ($tier['sub_categories'] as $subCat) {
            $tableName = getTableName($tier, $mainCat, $subCat);
            $subCatDisplay = $mainCat . $subCat;
            
            // Check if table exists
            $tableCheck = $GLOBALS['conn']->query("SHOW TABLES LIKE '$tableName'");
            $exists = ($tableCheck && $tableCheck->num_rows > 0);
            
            $subCatData = [
                'code' => $subCatDisplay,
                'table' => $tableName,
                'exists' => $exists,
                'total' => 0,
                'verified_qty' => 0,
                'verified_cash' => 0,
                'pending_qty' => 0,
                'pending_cash' => 0,
                'entry_count' => 0,
                'unit_price' => 0,
                'has_data' => false
            ];
            
            if ($exists) {
                // Get total count
                $totalResult = $GLOBALS['conn']->query("SELECT COUNT(*) as cnt FROM $tableName");
                $subCatData['total'] = $totalResult ? (int)$totalResult->fetch_assoc()['cnt'] : 0;
                
                // Get verified stats
                $verifiedResult = $GLOBALS['conn']->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tableName WHERE verified='Verified'");
                if ($verifiedResult) {
                    $vData = $verifiedResult->fetch_assoc();
                    $subCatData['verified_qty'] = (int)$vData['cnt'];
                    $subCatData['verified_cash'] = (float)$vData['amt'];
                }
                
                // Get pending stats
                $pendingResult = $GLOBALS['conn']->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tableName WHERE verified='Pending'");
                if ($pendingResult) {
                    $pData = $pendingResult->fetch_assoc();
                    $subCatData['pending_qty'] = (int)$pData['cnt'];
                    $subCatData['pending_cash'] = (float)$pData['amt'];
                }
                
                // Get entry count
                $entryResult = $GLOBALS['conn']->query("SELECT COUNT(*) as cnt FROM $tableName WHERE entry = 1");
                $subCatData['entry_count'] = $entryResult ? (int)$entryResult->fetch_assoc()['cnt'] : 0;
                
                // Get last price
                $priceResult = $GLOBALS['conn']->query("SELECT price FROM $tableName WHERE price > 0 ORDER BY id DESC LIMIT 1");
                if ($priceResult && $priceResult->num_rows > 0) {
                    $subCatData['unit_price'] = (float)$priceResult->fetch_assoc()['price'];
                }
                
                // Check if has data
                $subCatData['has_data'] = ($subCatData['total'] > 0);
                
                if ($subCatData['has_data']) {
                    $mainCatData['totals']['total_generated'] += $subCatData['total'];
                    $mainCatData['totals']['total_verified_qty'] += $subCatData['verified_qty'];
                    $mainCatData['totals']['total_verified_cash'] += $subCatData['verified_cash'];
                    $mainCatData['totals']['total_pending_qty'] += $subCatData['pending_qty'];
                    $mainCatData['totals']['total_pending_cash'] += $subCatData['pending_cash'];
                    $mainCatData['totals']['total_entry_count'] += $subCatData['entry_count'];
                    
                    $tierTotals['total_generated'] += $subCatData['total'];
                    $tierTotals['total_verified_qty'] += $subCatData['verified_qty'];
                    $tierTotals['total_verified_cash'] += $subCatData['verified_cash'];
                    $tierTotals['total_pending_qty'] += $subCatData['pending_qty'];
                    $tierTotals['total_pending_cash'] += $subCatData['pending_cash'];
                    $tierTotals['total_entry_count'] += $subCatData['entry_count'];
                    
                    $grandTotals['total_generated'] += $subCatData['total'];
                    $grandTotals['total_verified_qty'] += $subCatData['verified_qty'];
                    $grandTotals['total_verified_cash'] += $subCatData['verified_cash'];
                    $grandTotals['total_pending_qty'] += $subCatData['pending_qty'];
                    $grandTotals['total_pending_cash'] += $subCatData['pending_cash'];
                    $grandTotals['total_entry_count'] += $subCatData['entry_count'];
                    $grandTotals['categories_with_data']++;
                }
            }
            
            $mainCatData['sub_categories'][] = $subCatData;
        }
        
        $tierData['main_categories'][] = $mainCatData;
    }
    
    $tierData['totals'] = $tierTotals;
    $allData[] = $tierData;
}

// Calculate overall averages
$avgPrice = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_verified_cash'] + $grandTotals['total_pending_cash']) / $grandTotals['total_generated']) : 0;
$verificationRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_verified_qty'] / $grandTotals['total_generated']) * 100) : 0;
$pendingRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_pending_qty'] / $grandTotals['total_generated']) * 100) : 0;
$entryRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_entry_count'] / $grandTotals['total_generated']) * 100) : 0;
$totalRevenue = $grandTotals['total_verified_cash'] + $grandTotals['total_pending_cash'];

?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .summary-container {
            overflow-x: auto;
            border-radius: 20px;
            background: white;
            margin-bottom: 30px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
        }
        
        .summary-table th {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
            color: white;
            padding: 12px 8px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .summary-table th i {
            margin-right: 5px;
            font-size: 11px;
        }
        
        .summary-table td {
            padding: 8px 8px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        .tier-header {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .tier-header:hover {
            filter: brightness(0.98);
        }
        
        .tier-header td {
            font-weight: 800;
            font-size: 14px;
            border-top: 2px solid;
            border-bottom: 2px solid;
        }
        
        .main-cat-row {
            background: #fafafa;
            font-weight: 600;
        }
        
        .main-cat-row td {
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 8px;
        }
        
        .main-cat-code {
            font-weight: 700;
            font-size: 13px;
            padding-left: 25px !important;
        }
        
        .sub-cat-row {
            transition: all 0.2s ease;
        }
        
        .sub-cat-row:hover {
            background: #f5f5f5 !important;
        }
        
        .sub-cat-code {
            font-family: monospace;
            font-size: 11px;
            padding-left: 45px !important;
            color: #666;
        }
        
        .stat-number {
            font-weight: 700;
            font-size: 12px;
        }
        
        .verified-text {
            color: #2d6a4f;
            font-weight: 700;
        }
        
        .pending-text {
            color: #b85e00;
            font-weight: 700;
        }
        
        .entry-text {
            color: #1976d2;
            font-weight: 700;
        }
        
        .badge-empty {
            background: #f0f0f0;
            color: #999;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 6px;
        }
        
        .badge-active {
            background: #e8f5e9;
            color: #2d6a4f;
            padding: 2px 6px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: 600;
            margin-left: 6px;
        }
        
        .progress-bar-container {
            width: 60px;
            background: #f0f0f0;
            border-radius: 20px;
            overflow: hidden;
            display: inline-block;
        }
        
        .progress-bar {
            height: 5px;
            border-radius: 20px;
            transition: width 0.3s ease;
        }
        
        .grand-total-row {
            background: linear-gradient(135deg, #f8f9fa, #fff);
            border-top: 3px solid #FB8500;
            border-bottom: 2px solid #FB8500;
        }
        
        .grand-total-row td {
            font-weight: 800;
            padding: 12px 8px;
        }
        
        .expand-icon {
            cursor: pointer;
            margin-right: 8px;
            font-size: 12px;
            transition: transform 0.2s ease;
            display: inline-block;
        }
        
        .expand-icon.collapsed {
            transform: rotate(-90deg);
        }
        
        .sub-cat-group {
            display: table-row-group;
        }
        
        .sub-cat-group.hidden {
            display: none;
        }
        
        .stats-section {
            margin-top: 20px;
        }
        
        .stat-cards {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa, #fff);
            border-radius: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: white;
            border-radius: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            flex: 1;
            min-width: 140px;
            justify-content: center;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .footer-vertical {
            background: #f5f5f5;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-radius: 20px;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .summary-table th, .summary-table td {
                padding: 6px 4px;
                font-size: 9px;
            }
            .stat-card {
                padding: 6px 10px;
                min-width: 120px;
            }
            .stat-card div div:last-child {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<div class="summary-container">
    <table class="summary-table" id="summaryTable">
        <thead>
            <tr>
                <th style="width: 15%"><i class="fas fa-tag"></i> CATEGORY</th>
                <th style="width: 8%"><i class="fas fa-chart-line"></i> TOTAL QR</th>
                <th style="width: 8%"><i class="fas fa-tag"></i> PRICE/TKT</th>
                <th style="width: 10%"><i class="fas fa-check-circle"></i> VERIFIED</th>
                <th style="width: 12%"><i class="fas fa-money-bill-wave"></i> VERIFIED CASH</th>
                <th style="width: 8%"><i class="fas fa-clock"></i> PENDING</th>
                <th style="width: 12%"><i class="fas fa-hourglass-half"></i> PENDING CASH</th>
                <th style="width: 8%"><i class="fas fa-door-open"></i> ENTRY</th>
                <th style="width: 10%"><i class="fas fa-chart-simple"></i> %</th>
                <th style="width: 9%"><i class="fas fa-eye"></i> STATUS</th>
             </tr>
        </thead>
        <tbody>
            <?php foreach ($allData as $tier): ?>
                <!-- TIER HEADER -->
                <tr class="tier-header" style="background: <?php echo $tier['bg_color']; ?>;" data-tier="<?php echo $tier['display']; ?>">
                    <td style="color: <?php echo $tier['color']; ?>; font-size: 15px;">
                        <i class="fas <?php echo $tier['icon']; ?>" style="margin-right: 8px;"></i>
                        <strong><?php echo $tier['display']; ?></strong>
                        <span style="font-size: 10px; color: #666; margin-left: 8px;">
                            (<?php 
                                $activeMainCount = 0;
                                foreach ($tier['main_categories'] as $mc) {
                                    if ($mc['totals']['total_generated'] > 0) $activeMainCount++;
                                }
                                echo $activeMainCount;
                            ?>/<?php echo count($tier['main_categories']); ?> active)
                        </span>
                    </td>
                    <td class="stat-number" style="color: <?php echo $tier['color']; ?>;">600</td>
                    <td>-</td>
                    <td class="verified-text"><?php echo number_format($tier['totals']['total_verified_qty']); ?></td>
                    <td class="verified-text"><?php echo number_format($tier['totals']['total_verified_cash']); ?> Fbu</td>
                    <td class="pending-text"><?php echo number_format($tier['totals']['total_pending_qty']); ?></td>
                    <td class="pending-text"><?php echo number_format($tier['totals']['total_pending_cash']); ?> Fbu</td>
                    <td class="entry-text"><?php echo number_format($tier['totals']['total_entry_count']); ?></td>
                    <td>
                        <?php 
                        $tierRate = $tier['totals']['total_generated'] > 0 ? round(($tier['totals']['total_verified_qty'] / $tier['totals']['total_generated']) * 100) : 0;
                        ?>
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: <?php echo $tierRate; ?>%; background: <?php echo $tier['color']; ?>;"></div>
                        </div>
                        <span style="font-size: 10px; margin-left: 4px;"><?php echo $tierRate; ?>%</span>
                    </td>
                    <td><i class="fas fa-chart-line"></i> Active</td>
                </tr>
                
                <!-- MAIN CATEGORIES (C0, C1, etc.) -->
                <?php foreach ($tier['main_categories'] as $mainCat): ?>
                    <?php $hasData = $mainCat['totals']['total_generated'] > 0; ?>
                    <tr class="main-cat-row" data-main="<?php echo $mainCat['code']; ?>" data-tier="<?php echo $tier['display']; ?>">
                        <td class="main-cat-code">
                            <i class="fas fa-chevron-right expand-icon <?php echo $hasData ? '' : 'collapsed'; ?>" 
                               style="color: <?php echo $tier['color']; ?>; visibility: <?php echo $hasData ? 'visible' : 'hidden'; ?>;"
                               onclick="toggleSubCategories('<?php echo $tier['display']; ?>', '<?php echo $mainCat['code']; ?>')"></i>
                            <i class="fas fa-folder" style="color: <?php echo $tier['color']; ?>; margin-right: 6px;"></i>
                            <strong><?php echo $mainCat['code']; ?></strong>
                            <?php if (!$hasData): ?>
                                <span class="badge-empty">empty</span>
                            <?php else: ?>
                                <?php 
                                    $activeSubCount = 0;
                                    foreach ($mainCat['sub_categories'] as $sc) {
                                        if ($sc['has_data']) $activeSubCount++;
                                    }
                                ?>
                                <span class="badge-active"><?php echo $activeSubCount; ?>/5 active</span>
                            <?php endif; ?>
                        </td>
                        <td class="stat-number">600</td>
                        <td>-</td>
                        <td class="verified-text"><?php echo number_format($mainCat['totals']['total_verified_qty']); ?></td>
                        <td class="verified-text"><?php echo number_format($mainCat['totals']['total_verified_cash']); ?> Fbu</td>
                        <td class="pending-text"><?php echo number_format($mainCat['totals']['total_pending_qty']); ?></td>
                        <td class="pending-text"><?php echo number_format($mainCat['totals']['total_pending_cash']); ?> Fbu</td>
                        <td class="entry-text"><?php echo number_format($mainCat['totals']['total_entry_count']); ?></td>
                        <td>
                            <?php 
                            $catRate = $mainCat['totals']['total_generated'] > 0 ? round(($mainCat['totals']['total_verified_qty'] / $mainCat['totals']['total_generated']) * 100) : 0;
                            ?>
                            <div class="progress-bar-container">
                                <div class="progress-bar" style="width: <?php echo $catRate; ?>%; background: <?php echo $tier['color']; ?>;"></div>
                            </div>
                            <span style="font-size: 10px;"><?php echo $catRate; ?>%</span>
                        </td>
                        <td>
                            <?php if ($hasData): ?>
                                <i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Active
                            <?php else: ?>
                                <i class="fas fa-ban" style="color: #999;"></i> No Data
                            <?php endif; ?>
                         </td>
                    </tr>
                    
                    <!-- SUB-CATEGORIES (_1, _2, _3, _4, _5) -->
                    <tbody class="sub-cat-group" id="subcats-<?php echo $tier['display']; ?>-<?php echo $mainCat['code']; ?>" style="display: none;">
                        <?php foreach ($mainCat['sub_categories'] as $subCat): ?>
                            <tr class="sub-cat-row" style="background: <?php echo $subCat['has_data'] ? '#ffffff' : '#fafafa'; ?>;">
                                <td class="sub-cat-code">
                                    <i class="fas fa-qrcode" style="color: <?php echo $tier['color']; ?>; margin-right: 6px; font-size: 10px;"></i>
                                    <?php echo $subCat['code']; ?>
                                    <?php if (!$subCat['exists']): ?>
                                        <span class="badge-empty">not created</span>
                                    <?php elseif (!$subCat['has_data']): ?>
                                        <span class="badge-empty">empty</span>
                                    <?php else: ?>
                                        <span class="badge-active">active</span>
                                    <?php endif; ?>
                                </td>
                                <td class="stat-number"><?php echo $subCat['has_data'] ? number_format($subCat['total']) : '0'; ?></td>
                                <td><?php echo $subCat['unit_price'] > 0 ? number_format($subCat['unit_price']) . ' Fbu' : '-'; ?></td>
                                <td class="verified-text"><?php echo $subCat['has_data'] ? number_format($subCat['verified_qty']) : '0'; ?></td>
                                <td class="verified-text"><?php echo $subCat['has_data'] && $subCat['verified_cash'] > 0 ? number_format($subCat['verified_cash']) . ' Fbu' : '0 Fbu'; ?></td>
                                <td class="pending-text"><?php echo $subCat['has_data'] ? number_format($subCat['pending_qty']) : '0'; ?></td>
                                <td class="pending-text"><?php echo $subCat['has_data'] && $subCat['pending_cash'] > 0 ? number_format($subCat['pending_cash']) . ' Fbu' : '0 Fbu'; ?></td>
                                <td class="entry-text"><?php echo $subCat['has_data'] ? number_format($subCat['entry_count']) : '0'; ?></td>
                                <td>
                                    <?php if ($subCat['has_data']): ?>
                                        <?php $subRate = $subCat['total'] > 0 ? round(($subCat['verified_qty'] / $subCat['total']) * 100) : 0; ?>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width: <?php echo $subRate; ?>%; background: <?php echo $tier['color']; ?>;"></div>
                                        </div>
                                        <span style="font-size: 10px;"><?php echo $subRate; ?>%</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($subCat['has_data']): ?>
                                        <i class="fas fa-check" style="color: #2d6a4f;"></i> Active
                                    <?php elseif ($subCat['exists']): ?>
                                        <i class="fas fa-clock" style="color: #999;"></i> Empty
                                    <?php else: ?>
                                        <i class="fas fa-times" style="color: #dc3545;"></i> Missing
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endforeach; ?>
                
                <!-- Spacer between tiers -->
                <tr style="height: 3px;"><td colspan="10" style="border-bottom: 1px solid #e0e0e0; padding: 0;"></td></tr>
            <?php endforeach; ?>
            
            <!-- GRAND TOTAL ROW -->
            <tr class="grand-total-row">
                <td style="font-size: 16px; font-weight: 800;">
                    <i class="fas fa-chart-pie" style="color: #FB8500; margin-right: 8px;"></i>
                    GRAND TOTAL
                </td>
                <td style="font-size: 18px; font-weight: 800; color: #FB8500;"><?php echo number_format($grandTotals['total_generated']); ?></td>
                <td style="font-weight: 600;"><?php echo number_format($avgPrice); ?> Fbu</td>
                <td style="font-size: 16px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($grandTotals['total_verified_qty']); ?></td>
                <td style="font-size: 14px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($grandTotals['total_verified_cash']); ?> Fbu</td>
                <td style="font-size: 16px; font-weight: 800; color: #b85e00;"><?php echo number_format($grandTotals['total_pending_qty']); ?></td>
                <td style="font-size: 14px; font-weight: 800; color: #b85e00;"><?php echo number_format($grandTotals['total_pending_cash']); ?> Fbu</td>
                <td style="font-size: 16px; font-weight: 800; color: #1976d2;"><?php echo number_format($grandTotals['total_entry_count']); ?></td>
                <td>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $verificationRate; ?>%; background: #FB8500;"></div>
                    </div>
                    <span style="font-size: 10px;"><?php echo $verificationRate; ?>%</span>
                </td>
                <td><i class="fas fa-chart-line"></i> Complete</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- STATISTICS CARDS -->
<div class="stats-section">
    <div class="stat-cards">
        <div class="stat-card">
            <i class="fas fa-chart-line" style="color: #FB8500; font-size: 20px;"></i>
            <div>
                <div style="font-size: 10px; color: #666;">VERIFICATION RATE</div>
                <div style="font-size: 22px; font-weight: 800; color: #2d6a4f;"><?php echo $verificationRate; ?>%</div>
                <div style="font-size: 9px; color: #888;"><?php echo number_format($grandTotals['total_verified_qty']); ?> of <?php echo number_format($grandTotals['total_generated']); ?></div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-hourglass-half" style="color: #b85e00; font-size: 20px;"></i>
            <div>
                <div style="font-size: 10px; color: #666;">PENDING RATE</div>
                <div style="font-size: 22px; font-weight: 800; color: #b85e00;"><?php echo $pendingRate; ?>%</div>
                <div style="font-size: 9px; color: #888;"><?php echo number_format($grandTotals['total_pending_qty']); ?> tickets</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-door-open" style="color: #1976d2; font-size: 20px;"></i>
            <div>
                <div style="font-size: 10px; color: #666;">ENTRY RATE</div>
                <div style="font-size: 22px; font-weight: 800; color: #1976d2;"><?php echo $entryRate; ?>%</div>
                <div style="font-size: 9px; color: #888;"><?php echo number_format($grandTotals['total_entry_count']); ?> entries</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-money-bill-wave" style="color: #2d6a4f; font-size: 20px;"></i>
            <div>
                <div style="font-size: 10px; color: #666;">TOTAL REVENUE</div>
                <div style="font-size: 18px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($totalRevenue); ?> Fbu</div>
                <div style="font-size: 9px; color: #888;">Expected from all tickets</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-layer-group" style="color: #FB8500; font-size: 20px;"></i>
            <div>
                <div style="font-size: 10px; color: #666;">ACTIVE SUBCATS</div>
                <div style="font-size: 22px; font-weight: 800; color: #FB8500;"><?php echo $grandTotals['categories_with_data']; ?>/150</div>
                <div style="font-size: 9px; color: #888;">Sub-categories with data</div>
            </div>
        </div>
    </div>

    <!-- FOOTER SUMMARY -->
    <div class="footer-vertical">
        <i class="fas fa-chart-simple" style="color: #FB8500;"></i>
        <strong>Summary:</strong> 
        <?php echo number_format($grandTotals['total_generated']); ?> total tickets | 
        <?php echo number_format($grandTotals['total_verified_qty']); ?> verified (<?php echo $verificationRate; ?>%) | 
        <?php echo number_format($grandTotals['total_pending_qty']); ?> pending (<?php echo $pendingRate; ?>%) | 
        <?php echo number_format($grandTotals['total_entry_count']); ?> entries (<?php echo $entryRate; ?>%) | 
        <strong><?php echo number_format($totalRevenue); ?> Fbu</strong> total revenue
    </div>
</div>

<script>
    function toggleSubCategories(tier, mainCat) {
        const subcatGroup = document.getElementById('subcats-' + tier + '-' + mainCat);
        const icon = event.currentTarget;
        
        if (subcatGroup.style.display === 'none') {
            subcatGroup.style.display = '';
            icon.style.transform = 'rotate(90deg)';
        } else {
            subcatGroup.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>

</body>
</html>

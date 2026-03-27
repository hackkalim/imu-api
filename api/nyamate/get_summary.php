<?php
include 'db_config.php';

// Define ALL categories for each tier with full details
$tiers = [
    'simple' => [
        'display' => 'SIMPLE QR',
        'icon' => 'fa-ticket-alt',
        'color' => '#FB8500',
        'bg_color' => '#FFF4E5',
        'dark_color' => '#e67a00',
        'categories' => [
            ['code' => 'C0', 'table' => 'qrcodegenerate', 'prefix' => 'C0-'],
            ['code' => 'C1', 'table' => 'qrcodegeneratec1', 'prefix' => 'C1-'],
            ['code' => 'C2', 'table' => 'qrcodegeneratec2', 'prefix' => 'C2-'],
            ['code' => 'C3', 'table' => 'qrcodegeneratec3', 'prefix' => 'C3-'],
            ['code' => 'C4', 'table' => 'qrcodegeneratec4', 'prefix' => 'C4-'],
            ['code' => 'C5', 'table' => 'qrcodegeneratec5', 'prefix' => 'C5-'],
            ['code' => 'C6', 'table' => 'qrcodegeneratec6', 'prefix' => 'C6-'],
            ['code' => 'C7', 'table' => 'qrcodegeneratec7', 'prefix' => 'C7-'],
            ['code' => 'C8', 'table' => 'qrcodegeneratec8', 'prefix' => 'C8-'],
            ['code' => 'C9', 'table' => 'qrcodegeneratec9', 'prefix' => 'C9-']
        ]
    ],
    'vip' => [
        'display' => 'VIP QR',
        'icon' => 'fa-crown',
        'color' => '#9c27b0',
        'bg_color' => '#F3E5F5',
        'dark_color' => '#7b1fa2',
        'categories' => [
            ['code' => 'V0', 'table' => 'qrcodevip', 'prefix' => 'V0-'],
            ['code' => 'V1', 'table' => 'qrcodevipv1', 'prefix' => 'V1-'],
            ['code' => 'V2', 'table' => 'qrcodevipv2', 'prefix' => 'V2-'],
            ['code' => 'V3', 'table' => 'qrcodevipv3', 'prefix' => 'V3-'],
            ['code' => 'V4', 'table' => 'qrcodevipv4', 'prefix' => 'V4-'],
            ['code' => 'V5', 'table' => 'qrcodevipv5', 'prefix' => 'V5-'],
            ['code' => 'V6', 'table' => 'qrcodevipv6', 'prefix' => 'V6-'],
            ['code' => 'V7', 'table' => 'qrcodevipv7', 'prefix' => 'V7-'],
            ['code' => 'V8', 'table' => 'qrcodevipv8', 'prefix' => 'V8-'],
            ['code' => 'V9', 'table' => 'qrcodevipv9', 'prefix' => 'V9-']
        ]
    ],
    'vvip' => [
        'display' => 'VVIP QR',
        'icon' => 'fa-gem',
        'color' => '#FFD700',
        'bg_color' => '#FEF9E6',
        'dark_color' => '#e6c300',
        'categories' => [
            ['code' => 'W0', 'table' => 'qrcodevvip', 'prefix' => 'W0-'],
            ['code' => 'W1', 'table' => 'qrcodevvipw1', 'prefix' => 'W1-'],
            ['code' => 'W2', 'table' => 'qrcodevvipw2', 'prefix' => 'W2-'],
            ['code' => 'W3', 'table' => 'qrcodevvipw3', 'prefix' => 'W3-'],
            ['code' => 'W4', 'table' => 'qrcodevvipw4', 'prefix' => 'W4-'],
            ['code' => 'W5', 'table' => 'qrcodevvipw5', 'prefix' => 'W5-'],
            ['code' => 'W6', 'table' => 'qrcodevvipw6', 'prefix' => 'W6-'],
            ['code' => 'W7', 'table' => 'qrcodevvipw7', 'prefix' => 'W7-'],
            ['code' => 'W8', 'table' => 'qrcodevvipw8', 'prefix' => 'W8-'],
            ['code' => 'W9', 'table' => 'qrcodevvipw9', 'prefix' => 'W9-']
        ]
    ]
];

// Initialize totals
$grandTotals = [
    'total_generated' => 0,
    'total_verified_qty' => 0,
    'total_verified_cash' => 0,
    'total_pending_qty' => 0,
    'total_pending_cash' => 0,
    'total_entry_count' => 0,
    'total_price_sum' => 0,
    'categories_with_data' => 0
];

// Store all category data
$allCategoriesData = [];

// Process each tier
foreach ($tiers as $tierKey => $tier) {
    $tierData = [
        'display' => $tier['display'],
        'icon' => $tier['icon'],
        'color' => $tier['color'],
        'bg_color' => $tier['bg_color'],
        'dark_color' => $tier['dark_color'],
        'categories' => []
    ];
    
    $tierTotals = [
        'total_generated' => 0,
        'total_verified_qty' => 0,
        'total_verified_cash' => 0,
        'total_pending_qty' => 0,
        'total_pending_cash' => 0,
        'total_entry_count' => 0
    ];
    
    foreach ($tier['categories'] as $cat) {
        $categoryCode = $cat['code'];
        $tableName = $cat['table'];
        $prefix = $cat['prefix'];
        
        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE '$tableName'");
        $exists = ($tableCheck && $tableCheck->num_rows > 0);
        
        $categoryData = [
            'code' => $categoryCode,
            'prefix' => $prefix,
            'exists' => $exists,
            'total' => 0,
            'verified_qty' => 0,
            'verified_cash' => 0,
            'pending_qty' => 0,
            'pending_cash' => 0,
            'entry_count' => 0,
            'unit_price' => 0,
            'last_id' => 0,
            'has_data' => false
        ];
        
        if ($exists) {
            // Get total count
            $totalResult = $conn->query("SELECT COUNT(*) as cnt FROM $tableName");
            $categoryData['total'] = $totalResult ? (int)$totalResult->fetch_assoc()['cnt'] : 0;
            
            // Get last ID
            $lastIdResult = $conn->query("SELECT id FROM $tableName ORDER BY id DESC LIMIT 1");
            if ($lastIdResult && $lastIdResult->num_rows > 0) {
                $categoryData['last_id'] = (int)$lastIdResult->fetch_assoc()['id'];
            }
            
            // Get verified stats
            $verifiedResult = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tableName WHERE verified='Verified'");
            if ($verifiedResult) {
                $vData = $verifiedResult->fetch_assoc();
                $categoryData['verified_qty'] = (int)$vData['cnt'];
                $categoryData['verified_cash'] = (float)$vData['amt'];
            }
            
            // Get pending stats
            $pendingResult = $conn->query("SELECT COUNT(*) as cnt, COALESCE(SUM(price), 0) as amt FROM $tableName WHERE verified='Pending'");
            if ($pendingResult) {
                $pData = $pendingResult->fetch_assoc();
                $categoryData['pending_qty'] = (int)$pData['cnt'];
                $categoryData['pending_cash'] = (float)$pData['amt'];
            }
            
            // Get entry count
            $entryResult = $conn->query("SELECT COUNT(*) as cnt FROM $tableName WHERE entry = 1");
            $categoryData['entry_count'] = $entryResult ? (int)$entryResult->fetch_assoc()['cnt'] : 0;
            
            // Get last price
            $priceResult = $conn->query("SELECT price FROM $tableName WHERE price > 0 ORDER BY id DESC LIMIT 1");
            if ($priceResult && $priceResult->num_rows > 0) {
                $categoryData['unit_price'] = (float)$priceResult->fetch_assoc()['price'];
            }
            
            // Check if category has any data
            $categoryData['has_data'] = ($categoryData['total'] > 0);
            
            if ($categoryData['has_data']) {
                $tierTotals['total_generated'] += $categoryData['total'];
                $tierTotals['total_verified_qty'] += $categoryData['verified_qty'];
                $tierTotals['total_verified_cash'] += $categoryData['verified_cash'];
                $tierTotals['total_pending_qty'] += $categoryData['pending_qty'];
                $tierTotals['total_pending_cash'] += $categoryData['pending_cash'];
                $tierTotals['total_entry_count'] += $categoryData['entry_count'];
                
                $grandTotals['total_generated'] += $categoryData['total'];
                $grandTotals['total_verified_qty'] += $categoryData['verified_qty'];
                $grandTotals['total_verified_cash'] += $categoryData['verified_cash'];
                $grandTotals['total_pending_qty'] += $categoryData['pending_qty'];
                $grandTotals['total_pending_cash'] += $categoryData['pending_cash'];
                $grandTotals['total_entry_count'] += $categoryData['entry_count'];
                $grandTotals['categories_with_data']++;
            }
        }
        
        $tierData['categories'][] = $categoryData;
    }
    
    $tierData['totals'] = $tierTotals;
    $allCategoriesData[] = $tierData;
}

// Calculate overall averages
$avgPrice = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_verified_cash'] + $grandTotals['total_pending_cash']) / $grandTotals['total_generated']) : 0;
$verificationRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_verified_qty'] / $grandTotals['total_generated']) * 100) : 0;
$pendingRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_pending_qty'] / $grandTotals['total_generated']) * 100) : 0;
$entryRate = $grandTotals['total_generated'] > 0 ? round(($grandTotals['total_entry_count'] / $grandTotals['total_generated']) * 100) : 0;
$totalRevenue = $grandTotals['total_verified_cash'] + $grandTotals['total_pending_cash'];

?>

<!-- Table Design -->
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
    }
    
    .summary-table th {
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
        color: white;
        padding: 14px 12px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .summary-table th i {
        margin-right: 6px;
        font-size: 12px;
    }
    
    .summary-table td {
        padding: 12px 12px;
        font-size: 13px;
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
        font-size: 16px;
        border-top: 2px solid;
        border-bottom: 2px solid;
    }
    
    .category-row {
        transition: all 0.2s ease;
    }
    
    .category-row:hover {
        background: #fafafa !important;
        transform: translateX(5px);
    }
    
    .category-code {
        font-weight: 700;
        font-family: monospace;
        font-size: 14px;
    }
    
    .stat-number {
        font-weight: 700;
        font-size: 15px;
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
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .badge-active {
        background: #e8f5e9;
        color: #2d6a4f;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .progress-bar-container {
        width: 80px;
        background: #f0f0f0;
        border-radius: 20px;
        overflow: hidden;
        display: inline-block;
    }
    
    .progress-bar {
        height: 6px;
        border-radius: 20px;
        transition: width 0.3s ease;
    }
    
    .grand-total-row {
        background: linear-gradient(135deg, #f8f9fa, #fff);
        border-top: 3px solid #FB8500;
        border-bottom: 2px solid #FB8500;
    }
    
    /* Statistics Cards */
    .stats-section {
        margin-top: 30px;
        margin-bottom: 20px;
width: 100%;
    }
    
.stat-cards {
width: 100%;
border: 1px solid red;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
        align-items: center;
    gap: 15px;
    padding: 20px;
    background: linear-gradient(135deg, #f8f9fa, #fff);
    border-radius: 20px;
    margin-bottom: 20px;
}
    
    .stat-card {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        background: white;
        border-radius: 50px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        flex: 1;
        min-width: 180px;
        justify-content: center;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    
    /* Vertical Footer */
    .footer-vertical {
        background: #f5f5f5;
        padding: 20px;
        text-align: center;
        font-size: 13px;
        color: #666;
        border-radius: 20px;
        line-height: 1.8;
    }
    
    @media (max-width: 768px) {
        .summary-table th, .summary-table td {
            padding: 8px 6px;
            font-size: 10px;
        }
        
        .category-code {
            font-size: 11px;
        }
        
        .stat-number {
            font-size: 12px;
        }
        
        .stat-card {
            padding: 8px 12px;
            min-width: 140px;
        }
        
        .stat-card div div:first-child {
            font-size: 9px;
        }
        
        .stat-card div div:last-child {
            font-size: 14px;
        }
    }
</style>

<!-- TABLE - ONE HEADER ONLY -->
<div class="summary-container">
    <table class="summary-table">
        <thead>
             <tr>
                <th style="width: 12%"><i class="fas fa-tag"></i> CATEGORY</th>
                <th style="width: 10%"><i class="fas fa-chart-line"></i> TOTAL QR</th>
                <th style="width: 10%"><i class="fas fa-tag"></i> PRICE/TKT</th>
                <th style="width: 12%"><i class="fas fa-check-circle"></i> VERIFIED</th>
                <th style="width: 15%"><i class="fas fa-money-bill-wave"></i> VERIFIED CASH</th>
                <th style="width: 10%"><i class="fas fa-clock"></i> PENDING</th>
                <th style="width: 15%"><i class="fas fa-hourglass-half"></i> PENDING CASH</th>
                <th style="width: 8%"><i class="fas fa-door-open"></i> ENTRY</th>
                <th style="width: 8%"><i class="fas fa-chart-simple"></i> %</th>
              </tr>
        </thead>
        <tbody>
            <?php foreach ($allCategoriesData as $tier): ?>
                <!-- Tier Header -->
                <tr class="tier-header" style="background: <?php echo $tier['bg_color']; ?>;">
                    <td style="color: <?php echo $tier['color']; ?>; font-size: 18px;">
                        <i class="fas <?php echo $tier['icon']; ?>" style="margin-right: 10px;"></i>
                        <strong><?php echo $tier['display']; ?></strong>
                        <span style="font-size: 11px; color: #666; margin-left: 10px;">
                            (<?php echo count(array_filter($tier['categories'], fn($c) => $c['has_data'])); ?>/<?php echo count($tier['categories']); ?> active)
                        </span>
                     </td>
                    <td class="stat-number" style="color: <?php echo $tier['color']; ?>;"><?php echo number_format($tier['totals']['total_generated']); ?></td>
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
                        <span style="font-size: 11px; margin-left: 5px;"><?php echo $tierRate; ?>%</span>
                    </td>
                </tr>
                
                <!-- Individual Categories -->
                <?php foreach ($tier['categories'] as $cat): ?>
                    <tr class="category-row" style="background: <?php echo $cat['has_data'] ? '#ffffff' : '#fafafa'; ?>;">
                        <td style="padding-left: 45px;">
                            <i class="fas fa-qrcode" style="color: <?php echo $tier['color']; ?>; margin-right: 8px; font-size: 12px;"></i>
                            <span class="category-code"><?php echo $cat['code']; ?></span>
                            <?php if (!$cat['exists']): ?>
                                <span class="badge-empty">not created</span>
                            <?php elseif (!$cat['has_data']): ?>
                                <span class="badge-empty">empty</span>
                            <?php else: ?>
                                <span class="badge-active">active</span>
                            <?php endif; ?>
                        </td>
                        <td class="stat-number"><?php echo $cat['has_data'] ? number_format($cat['total']) : '0'; ?></td>
                        <td><?php echo $cat['unit_price'] > 0 ? number_format($cat['unit_price']) . ' Fbu' : '-'; ?></td>
                        <td class="verified-text"><?php echo $cat['has_data'] ? number_format($cat['verified_qty']) : '0'; ?></td>
                        <td class="verified-text"><?php echo $cat['has_data'] && $cat['verified_cash'] > 0 ? number_format($cat['verified_cash']) . ' Fbu' : '0 Fbu'; ?></td>
                        <td class="pending-text"><?php echo $cat['has_data'] ? number_format($cat['pending_qty']) : '0'; ?></td>
                        <td class="pending-text"><?php echo $cat['has_data'] && $cat['pending_cash'] > 0 ? number_format($cat['pending_cash']) . ' Fbu' : '0 Fbu'; ?></td>
                        <td class="entry-text"><?php echo $cat['has_data'] ? number_format($cat['entry_count']) : '0'; ?></td>
                        <td>
                            <?php if ($cat['has_data']): ?>
                                <?php $catRate = $cat['total'] > 0 ? round(($cat['verified_qty'] / $cat['total']) * 100) : 0; ?>
                                <div class="progress-bar-container">
                                    <div class="progress-bar" style="width: <?php echo $catRate; ?>%; background: <?php echo $tier['color']; ?>;"></div>
                                </div>
                                <span style="font-size: 11px;"><?php echo $catRate; ?>%</span>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Small spacer between tiers -->
                <tr style="height: 5px;"><td colspan="9" style="border-bottom: 1px solid #e0e0e0; padding: 0;">&nbsp;</td></tr>
            <?php endforeach; ?>
            
            <!-- GRAND TOTAL ROW -->
            <tr class="grand-total-row">
                <td style="font-size: 18px; font-weight: 800;">
                    <i class="fas fa-chart-pie" style="color: #FB8500; margin-right: 10px;"></i>
                    GRAND TOTAL
                </td>
                <td style="font-size: 20px; font-weight: 800; color: #FB8500;"><?php echo number_format($grandTotals['total_generated']); ?></td>
                <td style="font-weight: 600;"><?php echo number_format($avgPrice); ?> Fbu</td>
                <td style="font-size: 18px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($grandTotals['total_verified_qty']); ?></td>
                <td style="font-size: 16px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($grandTotals['total_verified_cash']); ?> Fbu</td>
                <td style="font-size: 18px; font-weight: 800; color: #b85e00;"><?php echo number_format($grandTotals['total_pending_qty']); ?></td>
                <td style="font-size: 16px; font-weight: 800; color: #b85e00;"><?php echo number_format($grandTotals['total_pending_cash']); ?> Fbu</td>
                <td style="font-size: 18px; font-weight: 800; color: #1976d2;"><?php echo number_format($grandTotals['total_entry_count']); ?></td>
                <td>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?php echo $verificationRate; ?>%; background: #FB8500;"></div>
                    </div>
                    <span style="font-size: 11px;"><?php echo $verificationRate; ?>%</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- STATISTICS CARDS -->
<div class="stats-section">
    <div class="stat-cards">
        <div class="stat-card">
            <i class="fas fa-chart-line" style="color: #FB8500; font-size: 24px;"></i>
            <div>
                <div style="font-size: 11px; color: #666;">VERIFICATION RATE</div>
                <div style="font-size: 28px; font-weight: 800; color: #2d6a4f;"><?php echo $verificationRate; ?>%</div>
                <div style="font-size: 10px; color: #888;"><?php echo number_format($grandTotals['total_verified_qty']); ?> of <?php echo number_format($grandTotals['total_generated']); ?></div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-hourglass-half" style="color: #b85e00; font-size: 24px;"></i>
            <div>
                <div style="font-size: 11px; color: #666;">PENDING RATE</div>
                <div style="font-size: 28px; font-weight: 800; color: #b85e00;"><?php echo $pendingRate; ?>%</div>
                <div style="font-size: 10px; color: #888;"><?php echo number_format($grandTotals['total_pending_qty']); ?> tickets</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-door-open" style="color: #1976d2; font-size: 24px;"></i>
            <div>
                <div style="font-size: 11px; color: #666;">ENTRY RATE</div>
                <div style="font-size: 28px; font-weight: 800; color: #1976d2;"><?php echo $entryRate; ?>%</div>
                <div style="font-size: 10px; color: #888;"><?php echo number_format($grandTotals['total_entry_count']); ?> entries</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-money-bill-wave" style="color: #2d6a4f; font-size: 24px;"></i>
            <div>
                <div style="font-size: 11px; color: #666;">TOTAL REVENUE</div>
                <div style="font-size: 24px; font-weight: 800; color: #2d6a4f;"><?php echo number_format($totalRevenue); ?> Fbu</div>
                <div style="font-size: 10px; color: #888;">Expected from all tickets</div>
            </div>
        </div>
        <div class="stat-card">
            <i class="fas fa-layer-group" style="color: #FB8500; font-size: 24px;"></i>
            <div>
                <div style="font-size: 11px; color: #666;">ACTIVE CATEGORIES</div>
                <div style="font-size: 28px; font-weight: 800; color: #FB8500;"><?php echo $grandTotals['categories_with_data']; ?>/30</div>
                <div style="font-size: 10px; color: #888;">Categories with data</div>
            </div>
        </div>
    </div>

    <!-- VERTICAL FOOTER - Like original -->
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
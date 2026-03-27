<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>IMU QR | Professional Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="icon" href="IMG_4043.jpg">
    
    <style>
        :root {
            --primary: #FB8500;
            --primary-hover: #e67a00;
            --dark: #1a1a2e;
            --bg: #f8f9fa;
            --white: #ffffff;
            --success: #2d6a4f;
            --pending: #ffb703;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg); 
            color: var(--dark); 
            line-height: 1.5;
        }

        /* Navigation Bar */
        header {
            background: var(--dark);
            color: var(--white);
            padding: 12px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-wrap: wrap;
            gap: 12px;
        }
        
        .logo { 
            font-size: 1.3rem; 
            font-weight: 800; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }
        .logo i { color: var(--primary); font-size: 1.5rem; }
        .logo span { color: var(--primary); }

        nav ul { 
            display: flex; 
            list-style: none; 
            gap: 6px; 
            flex-wrap: wrap; 
            justify-content: center;
        }
        nav ul li { 
            cursor: pointer; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-weight: 600; 
            transition: 0.3s; 
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        nav ul li i { font-size: 0.8rem; }
        nav ul li:hover, nav ul li.active { background: var(--primary); color: white; }

        /* Container */
        .container { 
            padding: 20px 4%; 
            max-width: 1400px; 
            margin: 0 auto; 
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Panels */
        .panel {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .panel-title { 
            font-size: 1.1rem; 
            font-weight: 800; 
            margin-bottom: 15px; 
            display: flex; 
            align-items: center; 
            gap: 8px;
            border-left: 4px solid var(--primary);
            padding-left: 12px;
            flex-wrap: wrap;
        }
        .panel-title i { color: var(--primary); font-size: 1.1rem; }

        /* Quick Links */
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .quick-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 16px;
            padding: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            display: block;
        }
        .quick-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(251, 133, 0, 0.1);
            border-color: var(--primary);
        }
        .quick-card i { font-size: 28px; margin-bottom: 8px; display: block; }
        .quick-card.simple i { color: #FB8500; }
        .quick-card.vip i { color: #9c27b0; }
        .quick-card.vvip i { color: #FFD700; }
        .quick-card h3 { font-size: 1rem; margin-bottom: 4px; }
        .quick-card p { font-size: 0.7rem; color: #666; }

        /* Tables */
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table th { 
            text-align: left; 
            padding: 10px 8px; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            color: #888; 
            border-bottom: 2px solid #f4f4f4; 
        }
        .summary-table td { 
            padding: 10px 8px; 
            font-size: 0.75rem; 
            border-bottom: 1px solid #f4f4f4; 
            vertical-align: middle;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 700;
            display: inline-block;
        }
        .badge.pending { background: #fff3cd; color: #856404; }
        .badge.verified { background: #d4edda; color: #155724; }

        /* Monitor Grid */
        .monitor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .monitor-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }
        .monitor-card .card-header {
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .monitor-card.simple .card-header { background: linear-gradient(135deg, #FB8500, #ff9e2c); }
        .monitor-card.vip .card-header { background: linear-gradient(135deg, #9c27b0, #7b1fa2); }
        .monitor-card.vvip .card-header { background: linear-gradient(135deg, #FFD700, #ffca3a); color: #1a1a2e; }
        .monitor-card.vvip .card-header h4 { color: #1a1a2e; }
        .monitor-card .card-header h4 { margin: 0; font-size: 0.9rem; font-weight: 700; color: white; }
        .monitor-card .card-header i { color: white; font-size: 1.1rem; }
        .count-badge { 
            background: rgba(255,255,255,0.2); 
            padding: 2px 8px; 
            border-radius: 20px; 
            font-size: 0.7rem; 
            margin-left: auto; 
            color: white; 
        }
        .table-container { 
            padding: 12px; 
            max-height: 260px; 
            overflow-y: auto; 
            -webkit-overflow-scrolling: touch;
        }
        .table-container table { width: 100%; }
        .table-container th { 
            font-size: 0.6rem; 
            padding: 6px; 
            background: #f8f9fa; 
            position: sticky;
            top: 0;
        }
        .table-container td { font-size: 0.65rem; padding: 6px; }
        .card-footer { 
            padding: 8px 12px; 
            background: #f8f9fa; 
            border-top: 1px solid #eee; 
            display: flex; 
            justify-content: space-between; 
            font-size: 0.65rem; 
            font-weight: 600; 
            flex-wrap: wrap;
            gap: 5px;
        }

        /* Buttons */
        .btn-tool {
            padding: 8px 14px;
            border-radius: 10px;
            border: none;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); }
        .btn-danger { background: #ffebee; color: #d32f2f; }
        .btn-danger:hover { background: #d32f2f; color: white; }
        .action-buttons { display: flex; gap: 8px; margin-bottom: 15px; flex-wrap: wrap; }

        /* Live Indicator */
        .live-indicator { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .pulse-dot { 
            width: 8px; 
            height: 8px; 
            background: #4CAF50; 
            border-radius: 50%; 
            display: inline-block; 
            animation: pulse 1.5s infinite; 
        }
        @keyframes pulse { 
            0% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); } 
            70% { box-shadow: 0 0 0 6px rgba(76, 175, 80, 0); } 
            100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); } 
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .monitor-grid { 
                grid-template-columns: repeat(2, 1fr); 
                gap: 12px;
            }
        }
        
        @media (max-width: 768px) {
            header { 
                flex-direction: column; 
                text-align: center; 
                padding: 12px 4%;
            }
            nav ul { 
                justify-content: center; 
                gap: 4px;
            }
            nav ul li { 
                padding: 5px 10px; 
                font-size: 0.7rem;
            }
            .container { padding: 15px 3%; }
            .panel { padding: 15px; border-radius: 16px; }
            .panel-title { font-size: 1rem; }
            
            .quick-links { 
                grid-template-columns: 1fr; 
                gap: 10px;
            }
            .quick-card { 
                padding: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
            }
            .quick-card i { 
                font-size: 1.5rem; 
                margin-bottom: 0;
            }
            .quick-card h3 { 
                font-size: 0.9rem; 
                margin-bottom: 0;
            }
            .quick-card p { 
                display: none;
            }
            
            .monitor-grid { 
                grid-template-columns: 1fr; 
                gap: 15px;
            }
            .monitor-card .card-header { 
                padding: 10px 12px;
            }
            .table-container { 
                max-height: 220px;
            }
            .card-footer { 
                flex-direction: column;
                text-align: center;
                gap: 6px;
            }
            
            .summary-table th, 
            .summary-table td { 
                padding: 8px 5px; 
                font-size: 0.65rem;
            }
            
            .action-buttons { 
                justify-content: center;
            }
            .btn-tool { 
                padding: 6px 12px; 
                font-size: 0.7rem;
            }
            
            .live-indicator { 
                justify-content: center;
                margin-top: 8px;
            }
            .panel-title { 
                flex-direction: column;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .logo { font-size: 1.1rem; }
            .logo i { font-size: 1.2rem; }
            
            nav ul li { 
                padding: 4px 8px; 
                font-size: 0.65rem;
            }
            
            .summary-table th, 
            .summary-table td { 
                padding: 6px 4px; 
                font-size: 0.6rem;
            }
            
            .table-container th, 
            .table-container td { 
                padding: 4px;
                font-size: 0.6rem;
            }
            
            .badge { 
                padding: 2px 6px; 
                font-size: 0.55rem;
            }
        }

        @media print {
            header, .quick-links, .action-buttons, .live-indicator, .btn-tool { display: none !important; }
            .panel { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }
            .monitor-card { break-inside: avoid; }
            .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        /* Scrollbar Styling */
        .table-container::-webkit-scrollbar {
            width: 4px;
        }
        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .table-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }
        
        /* Table responsive wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <i class="fas fa-qrcode"></i>
        IMU<span> QR</span>
    </div>
    <div style="font-size: 12px; opacity: 0.7;">Created by Mr Moses & Odon Bruno</div>
</header>

<div class="container">
    <!-- Home Tab -->
    <div id="home" class="tab-content active">
        <!-- Quick Access Links -->
        <div class="quick-links">
            <a href="simple/index.php" class="quick-card simple">
                <i class="fas fa-ticket-alt"></i>
                <h3>Simple QR</h3>
                <p>C0 - C9 Categories</p>
            </a>
            <a href="vip/index.php" class="quick-card vip">
                <i class="fas fa-crown"></i>
                <h3>VIP QR</h3>
                <p>V0 - V9 Categories</p>
            </a>
            <a href="vvip/index.php" class="quick-card vvip">
                <i class="fas fa-gem"></i>
                <h3>VVIP QR</h3>
                <p>W0 - W9 Categories</p>
            </a>
        </div>

        <!-- Sales Summary Panel -->
        <div class="panel">
            <div class="panel-title">
                <i class="fas fa-chart-bar"></i> Sales & Verification Summary
            </div>
            <div class="action-buttons">
                <button class="btn-tool btn-primary" onclick="printReport()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
            <div class="table-responsive">
                <table class="summary-table" style="width: 100%;">
                    <tbody id="summaryBody">
                        <tr><td colspan="8" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Monitor Panel -->
        <div class="panel">
            <div class="panel-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                <span><i class="fas fa-eye"></i> Global Live Monitor</span>
                <div class="live-indicator">
                    <span class="pulse-dot"></span>
                    <span style="font-size: 11px; color: #4CAF50; font-weight: 600;">LIVE</span>
                    <span style="background: #e3f2fd; color: #1976d2; padding: 3px 8px; border-radius: 20px; font-size: 10px;">
                        <i class="fas fa-sync-alt fa-spin"></i> Auto-refresh
                    </span>
                </div>
            </div>
            
            <div class="monitor-grid">
                <!-- Simple Card -->
                <div class="monitor-card simple">
                    <div class="card-header">
                        <i class="fas fa-qrcode"></i>
                        <h4>SIMPLE</h4>
                        <span class="count-badge" id="simpleCount">0</span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>ID</th><th>QR Code</th><th>Price</th><th>Status</th></tr></thead>
                            <tbody id="bodySimple"></tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <span><i class="fas fa-ticket-alt"></i> Total: <span id="simpleTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="simpleVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="simplePending">0</span></span>
                    </div>
                </div>

                <!-- VIP Card -->
                <div class="monitor-card vip">
                    <div class="card-header">
                        <i class="fas fa-crown"></i>
                        <h4>VIP</h4>
                        <span class="count-badge" id="vipCount">0</span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>ID</th><th>QR Code</th><th>Price</th><th>Status</th></tr></thead>
                            <tbody id="bodyVip"></tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <span><i class="fas fa-ticket-alt"></i> Total: <span id="vipTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="vipVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="vipPending">0</span></span>
                    </div>
                </div>

                <!-- VVIP Card -->
                <div class="monitor-card vvip">
                    <div class="card-header">
                        <i class="fas fa-gem"></i>
                        <h4>VVIP</h4>
                        <span class="count-badge" id="vvipCount">0</span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead><tr><th>ID</th><th>QR Code</th><th>Price</th><th>Status</th></tr></thead>
                            <tbody id="bodyVvip"></tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <span><i class="fas fa-ticket-alt"></i> Total: <span id="vvipTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="vvipVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="vvipPending">0</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Open tab function
    function openTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        if (event && event.currentTarget) event.currentTarget.classList.add('active');
        if (tabName === 'home') loadHomeData();
    }

    // Load all dashboard data
    function loadHomeData() {
        loadSummary();
        loadTierData('simple', 'Simple');
        loadTierData('vip', 'Vip');
        loadTierData('vvip', 'Vvip');
    }

    // Load summary table
    function loadSummary() {
        fetch('get_summary.php')
            .then(res => res.text())
            .then(data => {
                document.getElementById('summaryBody').innerHTML = data;
            })
            .catch(err => console.error('Summary error:', err));
    }

    // Load tier data for live monitor
    function loadTierData(tier, tableId) {
        fetch(`fetch_data.php?tier=${tier}&mode=minimal`)
            .then(res => res.text())
            .then(data => {
                document.getElementById(`body${tableId}`).innerHTML = data;
                calculateTierStats(tier);
            })
            .catch(err => console.error(`${tier} error:`, err));
    }

    // Calculate statistics for a tier
    function calculateTierStats(tier) {
        const bodyId = `body${tier.charAt(0).toUpperCase() + tier.slice(1)}`;
        const tableBody = document.getElementById(bodyId);
        if (!tableBody) return;
        
        const rows = tableBody.getElementsByTagName('tr');
        let total = 0, verified = 0, pending = 0;
        
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            if (cells.length > 0) {
                total++;
                const statusCell = cells[cells.length - 1];
                const statusText = statusCell.textContent.toLowerCase();
                if (statusText.includes('verified')) verified++;
                else if (statusText.includes('pending')) pending++;
            }
        }
        
        updateStatsDisplay(tier, total, verified, pending);
    }

    // Update statistics display
    function updateStatsDisplay(tier, total, verified, pending) {
        const countEl = document.getElementById(`${tier}Count`);
        const totalEl = document.getElementById(`${tier}Total`);
        const verifiedEl = document.getElementById(`${tier}Verified`);
        const pendingEl = document.getElementById(`${tier}Pending`);
        
        if (countEl) countEl.textContent = total;
        if (totalEl) totalEl.textContent = total;
        if (verifiedEl) verifiedEl.textContent = verified;
        if (pendingEl) pendingEl.textContent = pending;
    }

    // Reset all data
    function resetAllData() {
        if (confirm("⚠️ WARNING: This will permanently delete EVERY record in Simple, VIP, and VVIP tables. This cannot be undone!")) {
            const password = prompt("Please enter the Admin Password to confirm:");
            if (password === "hacker") {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "reset_all.php", true);
                xhr.onload = function() {
                    if (this.responseText.trim() === "success") {
                        alert("All tables have been cleared.");
                        loadHomeData();
                    } else {
                        alert("Error clearing data.");
                    }
                };
                xhr.send();
            } else {
                alert("Incorrect password.");
            }
        }
    }

    // Print report function
    function printReport() {
        window.print();
    }

    // Auto-refresh every 3 seconds
    let refreshInterval = setInterval(() => {
        const homeTab = document.getElementById('home');
        if (homeTab && homeTab.classList.contains('active')) {
            loadHomeData();
        }
    }, 3000);

    // Initialize on load
    window.onload = function() {
        loadHomeData();
    };
</script>

</body>
</html>

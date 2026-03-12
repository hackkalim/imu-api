<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMU QR | Professional Batch System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="icon" href="IMG_4043.jpg">
    
    <style>
        :root {
            --primary: #FB8500;
            --primary-hover: #e67a00;
            --dark: #1a1a1a;
            --bg: #f8f9fa;
            --white: #ffffff;
            --success: #2d6a4f;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--dark); }

        /* Navigation Bar */
        header {
            background: var(--dark); color: var(--white);
            padding: 15px 5%; display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
        }
        .logo { font-size: 22px; font-weight: 800; }
        .logo span { color: var(--primary); }

        nav ul { display: flex; list-style: none; gap: 20px; }
        nav ul li { 
            cursor: pointer; padding: 8px 16px; border-radius: 8px; 
            font-weight: 600; transition: 0.3s; font-size: 14px;
        }
        nav ul li:hover, nav ul li.active { background: var(--primary); color: white; }

        /* Dashboard Layout */
        .container { padding: 40px 5%; max-width: 1600px; margin: 0 auto; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .dashboard { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        .panel { background: var(--white); border-radius: 24px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); margin-bottom: 20px;}
        .panel-title { font-size: 18px; font-weight: 800; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .panel-title::before { content: ''; width: 4px; height: 20px; background: var(--primary); border-radius: 10px; }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 15px; font-size: 12px; text-transform: uppercase; color: #888; border-bottom: 2px solid #f4f4f4; }
        td { padding: 15px; font-size: 14px; border-bottom: 1px solid #f4f4f4; }
        
        .summary-table th { background: #fdfdfd; }

        /* Inputs & UI */
        .field { margin-bottom: 20px; }
        label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; opacity: 0.7; }
        input { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #e0e0e0; }
        .btn-main { width: 100%; background: var(--dark); color: white; border: none; padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-main:hover { background: var(--primary); }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
        .pending { background: #FFF4E5; color: #FF8800; }
        .verified { background: #E8F5E9; color: #2D6A4F; }

        #qr-guide { position: absolute; border: 2px dashed var(--primary); background: rgba(251, 133, 0, 0.2); pointer-events: none; display: none; }

        .btn-tool {
            padding: 10px 18px;
            border-radius: 10px;
            border: none;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        
        /* Update these in your index.php <style> section */
        .nav-link.active {
            background: #ff9800 !important;
            color: white !important;
        }

        .panel-title {
            border-left: 5px solid #ff9800;
            padding-left: 15px;
            font-weight: 800;
        }

        .btn-check {
            background: #ff9800;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-check:hover {
            background: #e68900;
        }

        /* FIXED: Print styles with page breaks */
        @media print {
            /* Hide navigation and buttons */
            header, nav, .btn-main, .btn-tool, #status-msg, .field, #qr-guide, .admin-actions, .live-indicator .update-badge {
                display: none !important;
            }
            
            body { 
                background: white; 
                margin: 0;
                padding: 0;
            }
            
            .container { 
                width: 100%; 
                padding: 0; 
                margin: 0; 
            }
            
            /* Show all panels */
            .panel { 
                box-shadow: none !important; 
                border: 1px solid #ddd !important; 
                width: 100% !important;
                margin: 0 !important;
                padding: 20px !important;
                page-break-inside: avoid;
            }
            
            .tab-content { 
                display: block !important; 
            }
            
            /* First panel (Sales Summary) on page 1 */
            .panel:first-of-type {
                page-break-after: always;
                page-break-inside: avoid;
            }
            
            /* Second panel (Live Monitor) on page 2 */
            .panel:last-of-type {
                page-break-before: always;
                page-break-inside: avoid;
            }
            
            /* Ensure monitor grid shows in print */
            .monitor-grid {
                display: grid !important;
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 15px;
                page-break-inside: avoid;
            }
            
            .monitor-card {
                border: 1px solid #ccc !important;
                box-shadow: none !important;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .monitor-card .card-header {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .pulse-dot, .fa-spin {
                animation: none !important;
            }
            
            .table-container {
                max-height: none !important;
                overflow: visible !important;
            }
            
            table {
                page-break-inside: auto;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            /* Force background colors to print */
            .card-header, .badge, .summary-table th {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* --- RESPONSIVE ENGINE --- */

        /* For Tablets and smaller laptops */
        @media (max-width: 1100px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                gap: 15px;
                padding: 15px 2%;
            }
        }

        /* For Mobile Phones */
        @media (max-width: 768px) {
            .container {
                padding: 15px 3%;
            }

            #home .panel div[style*="grid-template-columns"] {
                grid-template-columns: 1fr !important; 
                gap: 40px !important;
            }

            .panel {
                padding: 15px;
                overflow-x: auto;
                border-radius: 15px;
            }

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 10px;
            }

            nav ul li {
                font-size: 12px;
                padding: 6px 10px;
            }

            .logo {
                font-size: 18px;
            }

            .summary-table th {
                font-size: 10px;
                padding: 8px;
            }
            
            .summary-table td {
                font-size: 12px;
                padding: 8px;
            }
        }

        /* Touch targets optimization */
        button, .nav-link {
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Enhanced Live Monitor Styles */
        .monitor-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .monitor-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .monitor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(251, 133, 0, 0.1);
        }

        .monitor-card .card-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .monitor-card .table-container {
            padding: 15px;
            max-height: 300px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary) #f0f0f0;
        }

        .monitor-card .table-container::-webkit-scrollbar {
            width: 6px;
        }

        .monitor-card .table-container::-webkit-scrollbar-track {
            background: #f0f0f0;
            border-radius: 10px;
        }

        .monitor-card .table-container::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        .monitor-card .table-container::-webkit-scrollbar-thumb:hover {
            background: var(--primary-hover);
        }

        .monitor-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .monitor-card th {
            padding: 10px;
            font-size: 11px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--primary);
            position: sticky;
            top: 0;
            background: #f8f9fa;
        }

        .monitor-card td {
            padding: 10px;
            font-size: 12px;
            border-bottom: 1px solid #f0f0f0;
        }

        .monitor-card tbody tr {
            transition: all 0.2s ease;
        }

        .monitor-card tbody tr:hover {
            background: rgba(251, 133, 0, 0.05);
            transform: scale(1.02);
        }

        .monitor-card .card-footer {
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            font-size: 12px;
            display: flex;
            justify-content: space-between;
        }

        /* Live indicator pulse animation */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(76, 175, 80, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(76, 175, 80, 0);
            }
        }

        .pulse-dot {
            animation: pulse 1.5s infinite;
        }

        /* Status badges for monitor */
        .monitor-card .badge {
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .monitor-card .pending {
            background: #fff3cd;
            color: #856404;
        }

        .monitor-card .verified {
            background: #d4edda;
            color: #155724;
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .monitor-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .monitor-grid {
                grid-template-columns: 1fr;
            }
            
            .monitor-card .table-container {
                max-height: 250px;
            }
        }

        /* Statistics values styling */
        .stat-value {
            font-weight: 700;
            color: var(--dark);
            background: rgba(251, 133, 0, 0.1);
            padding: 2px 6px;
            border-radius: 12px;
            min-width: 30px;
            display: inline-block;
            text-align: center;
        }

        .card-footer span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-footer i {
            font-size: 14px;
        }


/* Print preview button styling */
@media screen {
    .btn-tool[onclick="printReport()"] {
        background: #e3f2fd !important;
        color: #1976d2 !important;
        transition: all 0.3s ease;
    }
    
    .btn-tool[onclick="printReport()"]:hover {
        background: #1976d2 !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
    }
}

</style>
</head>
<body>

<header>
    <div class="logo">IMU<span> QR</span></div>
    <div style="font-size: 12px; opacity: 0.7;">Created by Mr Moses & Odon Bruno</div>
</header>

<div class="container">
    
    <div id="home" class="tab-content active">
        <!-- Page 1: Sales & Verification Summary -->
        <div class="panel" id="summary-panel">
            <div class="panel-title">Sales & Verification Summary</div>
            <div class="admin-actions" style="margin-bottom: 20px; display: flex; gap: 10px;">
                <button class="btn-tool" style="background: #e3f2fd; color: #1976d2;" onclick="printReport()">📄 Print Report / PDF</button>
                <button class="btn-tool" style="background: #ffebee; color: #d32f2f; margin-left: auto;" onclick="resetAllData()">🗑️ Reset All Data</button>
            </div>
            <table class="summary-table">
                <thead>
                    <tr>
                        <th>Category</th> <th>Total Generated</th> <th>Price/Ticket</th> <th>Qty Verified</th> <th>Total Verified (Fbu)</th> <th>Qty Pending</th> <th>Total Pending (Fbu)</th> <th>Qty Entered</th>
                    </tr>
                </thead>
                <tbody id="summaryBody"></tbody>
            </table>
        </div>

        <!-- Page 2: Global Live Monitor -->
        <div class="panel" id="monitor-panel">
            <div class="panel-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="panel-title" style="display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-eye" style="color: var(--primary);"></i>
                    Global Live Monitor (Real-time)
                </div>
                <div class="live-indicator" style="display: flex; align-items: center; gap: 10px;">
                    <span class="pulse-dot" style="width: 10px; height: 10px; background: #4CAF50; border-radius: 50%; display: inline-block; animation: pulse 1.5s infinite;"></span>
                    <span style="font-size: 12px; color: #4CAF50; font-weight: 600;">LIVE</span>
                    <span class="update-badge" style="background: #e3f2fd; color: #1976d2; padding: 4px 8px; border-radius: 20px; font-size: 11px; font-weight: 600;">
                        <i class="fas fa-sync-alt fa-spin" style="margin-right: 4px;"></i> Auto-refresh
                    </span>
                </div>
            </div>
            
            <div class="monitor-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 25px;">
                <!-- Simple Card -->
                <div class="monitor-card">
                    <div class="card-header" style="background: linear-gradient(135deg, #FB8500 0%, #ff9e2c 100%); padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-qrcode" style="color: white; font-size: 20px;"></i>
                        <h4 style="color: white; margin: 0; font-size: 18px; font-weight: 700;">SIMPLE TICKETS</h4>
                        <span class="count-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 4px 10px; border-radius: 30px; font-size: 12px; margin-left: auto;" id="simpleCount">0</span>
                    </div>
                    <div class="table-container" style="padding: 15px; max-height: 300px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #FB8500;">ID</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #FB8500;">QR Code</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #FB8500;">Price</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #FB8500;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="bodySimple"></tbody>
                        </table>
                    </div>
                    <div class="card-footer" style="padding: 10px 15px; background: #f8f9fa; border-top: 1px solid #eee; font-size: 12px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-ticket-alt" style="color: #FB8500;"></i> Total: <span id="simpleTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="simpleVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="simplePending">0</span></span>
                    </div>
                </div>

                <!-- VIP Card -->
                <div class="monitor-card">
                    <div class="card-header" style="background: linear-gradient(135deg, #023047 0%, #0a4b6e 100%); padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-crown" style="color: #ffb703; font-size: 20px;"></i>
                        <h4 style="color: white; margin: 0; font-size: 18px; font-weight: 700;">VIP TICKETS</h4>
                        <span class="count-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 4px 10px; border-radius: 30px; font-size: 12px; margin-left: auto;" id="vipCount">0</span>
                    </div>
                    <div class="table-container" style="padding: 15px; max-height: 300px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #023047;">ID</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #023047;">QR Code</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #023047;">Price</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #023047;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="bodyVip"></tbody>
                        </table>
                    </div>
                    <div class="card-footer" style="padding: 10px 15px; background: #f8f9fa; border-top: 1px solid #eee; font-size: 12px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-ticket-alt" style="color: #023047;"></i> Total: <span id="vipTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="vipVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="vipPending">0</span></span>
                    </div>
                </div>

                <!-- VVIP Card -->
                <div class="monitor-card">
                    <div class="card-header" style="background: linear-gradient(135deg, #ffb703 0%, #ffca3a 100%); padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-star" style="color: #023047; font-size: 20px;"></i>
                        <h4 style="color: #023047; margin: 0; font-size: 18px; font-weight: 700;">VVIP TICKETS</h4>
                        <span class="count-badge" style="background: rgba(2, 48, 71, 0.2); color: #023047; padding: 4px 10px; border-radius: 30px; font-size: 12px; margin-left: auto;" id="vvipCount">0</span>
                    </div>
                    <div class="table-container" style="padding: 15px; max-height: 300px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="position: sticky; top: 0; background: #f8f9fa;">
                                <tr>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #ffb703;">ID</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #ffb703;">QR Code</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #ffb703;">Price</th>
                                    <th style="padding: 10px; font-size: 11px; color: #666; border-bottom: 2px solid #ffb703;">Status</th>
                                </tr>
                            </thead>
                            <tbody id="bodyVvip"></tbody>
                        </table>
                    </div>
                    <div class="card-footer" style="padding: 10px 15px; background: #f8f9fa; border-top: 1px solid #eee; font-size: 12px; display: flex; justify-content: space-between;">
                        <span><i class="fas fa-ticket-alt" style="color: #ffb703;"></i> Total: <span id="vvipTotal">0</span></span>
                        <span><i class="fas fa-check-circle" style="color: #2d6a4f;"></i> Verified: <span id="vvipVerified">0</span></span>
                        <span><i class="fas fa-clock" style="color: #b85e00;"></i> Pending: <span id="vvipPending">0</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tier-panel" class="tab-content">
        <div class="dashboard">
            <aside class="panel">
                <div class="panel-title" id="tier-title">Generator</div>
                <div class="field">
                    <label>Base Photo</label>
                    <input type="file" id="imageInput" accept="image/*">
                </div>
                <div id="preview-container" style="position: relative; background: #eee; cursor: crosshair;">
                    <canvas id="previewCanvas" style="width: 100%; display: block;"></canvas>
                    <div id="qr-guide"></div>
                </div>
                <div class="field" style="margin-top:15px">
                    <label>Quantity</label>
                    <input type="number" id="duplicateCount" placeholder="Amount...">
                </div>
                <div class="field">
                    <label>4. Ticket Price ($)</label>
                    <input type="number" id="ticketPrice" placeholder="Enter amount (e.g. 50)" value="">
                </div>
                <button class="btn-main" onclick="startGeneration()">Generate & Download</button>
                <div id="status-msg" style="margin-top:10px; font-weight:700; color:var(--primary)">System Ready</div>
            </aside>

            <main class="panel">
                <div class="panel-title">Manage Records</div>
                <table id="mainTable">
                    <thead>
                        <tr><th>ID</th><th>QR Code</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </main>
        </div>
    </div>
</div>

<div id="qrcode-hidden" style="display:none;"></div>
<canvas id="canvas" style="display:none;"></canvas>

<script>
    let currentTier = 'simple';
    let baseImg = null;
    let qrX = 0, qrY = 0;
    const qrSizePercent = 0.15;

    function openTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        
        if(tabName === 'home') {
            document.getElementById('home').classList.add('active');
            loadHomeData();
        } else {
            currentTier = tabName;
            document.getElementById('tier-panel').classList.add('active');
            document.getElementById('tier-title').innerText = tabName.toUpperCase() + " Generator";
            loadTierData();
        }
        event.currentTarget.classList.add('active');
    }

    // --- POSITIONING LOGIC ---
    const pCanvas = document.getElementById('previewCanvas');
    const pCtx = pCanvas.getContext('2d');
    const qrGuide = document.getElementById('qr-guide');

    document.getElementById('imageInput').onchange = function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            baseImg = new Image();
            baseImg.onload = function() {
                pCanvas.width = baseImg.width;
                pCanvas.height = baseImg.height;
                pCtx.drawImage(baseImg, 0, 0);
            };
            baseImg.src = event.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    };

    pCanvas.addEventListener('click', function(e) {
        if(!baseImg) return;
        const rect = pCanvas.getBoundingClientRect();
        const scaleX = pCanvas.width / rect.width;
        const scaleY = pCanvas.height / rect.height;
        qrX = (e.clientX - rect.left) * scaleX;
        qrY = (e.clientY - rect.top) * scaleY;

        const displaySize = rect.width * qrSizePercent;
        qrGuide.style.width = displaySize + "px";
        qrGuide.style.height = displaySize + "px";
        qrGuide.style.left = (e.clientX - rect.left - displaySize/2) + "px";
        qrGuide.style.top = (e.clientY - rect.top - displaySize/2) + "px";
        qrGuide.style.display = "block";
        
        qrX -= (pCanvas.width * qrSizePercent / 2);
        qrY -= (pCanvas.width * qrSizePercent / 2);
    });

    // --- GENERATION ---
    async function startGeneration() {
        const qty = parseInt(document.getElementById('duplicateCount').value);
        const price = document.getElementById('ticketPrice').value;
        
        if(!baseImg || !qty || !price) {
            alert("Please select image, quantity, and set a price.");
            return;
        }

        document.getElementById('status-msg').innerText = "Processing...";

        for(let i=0; i<qty; i++) {
            let prefix = "";
            if(currentTier === 'vip') prefix = "V";
            if(currentTier === 'vvip') prefix = "Vv";
            
            let uniqueID = prefix + (Math.floor(Math.random() * 9000000) + 1000000);
            
            document.getElementById('qrcode-hidden').innerHTML = "";
            new QRCode(document.getElementById("qrcode-hidden"), { text: uniqueID, width: 200, height: 200 });

            await new Promise(r => setTimeout(r, 400));
            const qrImg = document.querySelector('#qrcode-hidden img');
            
            drawAndDownload(uniqueID, qrImg);
            saveToDB(uniqueID, price); 

            document.getElementById('status-msg').innerText = `Generated ${i+1}/${qty}`;
            await new Promise(r => setTimeout(r, 600));
        }
        loadTierData();
    }

    function drawAndDownload(id, qrImg) {
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = baseImg.width; canvas.height = baseImg.height;
        ctx.drawImage(baseImg, 0, 0);
        const size = canvas.width * qrSizePercent;
        ctx.fillStyle = "white";
        ctx.fillRect(qrX, qrY, size, size);
        ctx.drawImage(qrImg, qrX+5, qrY+5, size-10, size-10);
        
        const link = document.createElement('a');
        link.download = `${currentTier}_${id}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }

    // --- DATA HANDLING ---
    function saveToDB(val, price) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "save_qr.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send(`qrcode=${val}&tier=${currentTier}&price=${price}`);
    }

    function loadTierData() {
        fetch(`fetch_data.php?tier=${currentTier}`)
            .then(res => res.text())
            .then(data => document.getElementById('tableBody').innerHTML = data);
    }

    // Function to fetch the Summary Table and Live Monitor with statistics
    function loadHomeData() {
        // 1. Update the Financial Summary Table (Table 1)
        const xhrSummary = new XMLHttpRequest();
        xhrSummary.open("GET", "get_summary.php", true);
        xhrSummary.onload = function() {
            if(this.status == 200) {
                document.getElementById('summaryBody').innerHTML = this.responseText;
            }
        };
        xhrSummary.send();

        // 2. Update the Live Monitor Lists and calculate statistics
        const tiers = ['simple', 'vip', 'vvip'];
        
        // Reset all stats to 0 first
        resetAllStats();
        
        // Fetch each tier's data
        tiers.forEach(t => {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", `fetch_data.php?tier=${t}&mode=minimal`, true);
            xhr.onload = function() {
                if(this.status == 200) {
                    const bodyId = `body${t.charAt(0).toUpperCase() + t.slice(1)}`;
                    document.getElementById(bodyId).innerHTML = this.responseText;
                    
                    // Calculate statistics after data is loaded
                    setTimeout(() => calculateTierStats(t), 100);
                }
            };
            xhr.send();
        });
    }

    // Reset all statistics to 0
    function resetAllStats() {
        const tiers = ['simple', 'vip', 'vvip'];
        tiers.forEach(t => {
            if(document.getElementById(`${t}Count`)) 
                document.getElementById(`${t}Count`).textContent = '0';
            if(document.getElementById(`${t}Total`)) 
                document.getElementById(`${t}Total`).textContent = '0';
            if(document.getElementById(`${t}Verified`)) 
                document.getElementById(`${t}Verified`).textContent = '0';
            if(document.getElementById(`${t}Pending`)) 
                document.getElementById(`${t}Pending`).textContent = '0';
        });
    }

    // Calculate statistics for a specific tier
    function calculateTierStats(tier) {
        const bodyId = `body${tier.charAt(0).toUpperCase() + tier.slice(1)}`;
        const tableBody = document.getElementById(bodyId);
        
        if (!tableBody) return;
        
        const rows = tableBody.getElementsByTagName('tr');
        
        // Check if there's a "no records" message
        if (rows.length === 1) {
            const firstRow = rows[0];
            const firstCell = firstRow.querySelector('td');
            if (firstCell && firstCell.getAttribute('colspan') === '4') {
                // No records, all stats are 0
                updateStatsDisplay(tier, 0, 0, 0);
                return;
            }
        }
        
        let total = 0;
        let verified = 0;
        let pending = 0;
        
        // Loop through each row
        for (let row of rows) {
            total++;
            
            // Find the status cell (last cell in the row)
            const cells = row.getElementsByTagName('td');
            if (cells.length > 0) {
                const lastCell = cells[cells.length - 1]; // Status cell
                const badge = lastCell.querySelector('.badge');
                
                if (badge) {
                    const status = badge.textContent.toLowerCase();
                    if (status.includes('verified')) {
                        verified++;
                    } else if (status.includes('pending')) {
                        pending++;
                    }
                } else {
                    const statusText = lastCell.textContent.toLowerCase();
                    if (statusText.includes('verified')) {
                        verified++;
                    } else if (statusText.includes('pending')) {
                        pending++;
                    }
                }
            }
        }
        
        // Update the display
        updateStatsDisplay(tier, total, verified, pending);
    }

    // Update the statistics display
    function updateStatsDisplay(tier, total, verified, pending) {
        // Update count badge in header
        const countElement = document.getElementById(`${tier}Count`);
        if (countElement) countElement.textContent = total;
        
        // Update footer stats
        const totalElement = document.getElementById(`${tier}Total`);
        const verifiedElement = document.getElementById(`${tier}Verified`);
        const pendingElement = document.getElementById(`${tier}Pending`);
        
        if (totalElement) totalElement.textContent = total;
        if (verifiedElement) verifiedElement.textContent = verified;
        if (pendingElement) pendingElement.textContent = pending;
    }

    function checkout(id, tier) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (this.responseText.trim() === "success") {
                loadTierData();
                if (document.getElementById('home').classList.contains('active')) {
                    loadHomeData();
                }
            }
        };
        xhr.send("id=" + id + "&tier=" + tier);
    }

    // --- PRINT TO PDF with page breaks ---
    // --- PROFESSIONAL PDF EXPORT WITH COLORS ---
function printReport() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    
    // Get current date and time
    const now = new Date();
    const dateStr = now.toLocaleDateString();
    const timeStr = now.toLocaleTimeString();
    
    // Get summary data from the table with original HTML structure
    const summaryTable = document.querySelector('.summary-table').cloneNode(true);
    
    // Get monitor data for each tier with original styling
    const simpleCard = document.querySelector('.monitor-card:first-child').cloneNode(true);
    const vipCard = document.querySelector('.monitor-card:nth-child(2)').cloneNode(true);
    const vvipCard = document.querySelector('.monitor-card:last-child').cloneNode(true);
    
    // Get statistics
    const simpleTotal = document.getElementById('simpleTotal')?.textContent || '0';
    const simpleVerified = document.getElementById('simpleVerified')?.textContent || '0';
    const simplePending = document.getElementById('simplePending')?.textContent || '0';
    
    const vipTotal = document.getElementById('vipTotal')?.textContent || '0';
    const vipVerified = document.getElementById('vipVerified')?.textContent || '0';
    const vipPending = document.getElementById('vipPending')?.textContent || '0';
    
    const vvipTotal = document.getElementById('vvipTotal')?.textContent || '0';
    const vvipVerified = document.getElementById('vvipVerified')?.textContent || '0';
    const vvipPending = document.getElementById('vvipPending')?.textContent || '0';
    
    // Create print-friendly HTML with ALL colors preserved
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>IMU QR - Sales Report</title>
            <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                
                body {
                    font-family: 'Plus Jakarta Sans', sans-serif;
                    background: white;
                    padding: 30px;
                    color: #333;
                }
                
                /* PRESERVE ALL COLORS IN PRINT */
                @media print {
                    body { 
                        -webkit-print-color-adjust: exact !important; 
                        print-color-adjust: exact !important; 
                    }
                    
                    .card-header, 
                    .badge, 
                    .summary-table th,
                    .monitor-card .card-header,
                    .btn-check,
                    .verified,
                    .pending {
                        -webkit-print-color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                }
                
                /* REPORT HEADER */
                .report-header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #FB8500;
                }
                
                .report-header h1 {
                    color: #FB8500;
                    font-size: 32px;
                    font-weight: 800;
                    margin-bottom: 10px;
                }
                
                .report-header h1 span {
                    color: #1a1a1a;
                }
                
                .report-header .date {
                    color: #666;
                    font-size: 14px;
                }
                
                .page-title {
                    font-size: 20px;
                    font-weight: 700;
                    margin: 30px 0 20px 0;
                    padding-left: 15px;
                    border-left: 5px solid #FB8500;
                }
                
                /* TABLE STYLES - KEEPING ORIGINAL COLORS */
                .summary-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                }
                
                .summary-table th {
                    background: linear-gradient(135deg, #FB8500 0%, #ff9e2c 100%) !important;
                    color: white !important;
                    padding: 12px;
                    text-align: left;
                    font-size: 13px;
                    font-weight: 600;
                }
                
                .summary-table td {
                    padding: 10px 12px;
                    border-bottom: 1px solid #e0e0e0;
                    font-size: 13px;
                }
                
                .summary-table tr:last-child {
                    background: #f8f9fa;
                    font-weight: 700;
                }
                
                /* MONITOR GRID - KEEPING ORIGINAL COLORS */
                .monitor-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 20px;
                    margin-bottom: 30px;
                }
                
                .monitor-card {
                    border: 1px solid #e0e0e0;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                }
                
                /* SIMPLE CARD - ORANGE */
                .monitor-card:first-child .card-header {
                    background: linear-gradient(135deg, #FB8500 0%, #ff9e2c 100%) !important;
                    color: white !important;
                    padding: 15px 20px;
                }
                
                /* VIP CARD - DARK BLUE */
                .monitor-card:nth-child(2) .card-header {
                    background: linear-gradient(135deg, #023047 0%, #0a4b6e 100%) !important;
                    color: white !important;
                    padding: 15px 20px;
                }
                
                .monitor-card:nth-child(2) .card-header i {
                    color: #ffb703 !important;
                }
                
                /* VVIP CARD - YELLOW */
                .monitor-card:last-child .card-header {
                    background: linear-gradient(135deg, #ffb703 0%, #ffca3a 100%) !important;
                    color: #023047 !important;
                    padding: 15px 20px;
                }
                
                .monitor-card .card-header h4 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 700;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }
                
                .count-badge {
                    background: rgba(255,255,255,0.2) !important;
                    color: white !important;
                    padding: 4px 10px;
                    border-radius: 30px;
                    font-size: 12px;
                    margin-left: auto;
                }
                
                .vvip .count-badge {
                    color: #023047 !important;
                }
                
                .table-container {
                    padding: 15px;
                }
                
                .monitor-card table {
                    width: 100%;
                    border-collapse: collapse;
                }
                
                .monitor-card th {
                    padding: 10px;
                    font-size: 11px;
                    font-weight: 600;
                    color: #666;
                    text-transform: uppercase;
                    border-bottom: 2px solid #FB8500;
                    background: #f8f9fa;
                }
                
                .monitor-card td {
                    padding: 10px;
                    font-size: 12px;
                    border-bottom: 1px solid #f0f0f0;
                }
                
                /* STATUS BADGES - KEEPING COLORS */
                .badge {
                    padding: 4px 8px;
                    border-radius: 20px;
                    font-size: 10px;
                    font-weight: 600;
                    display: inline-block;
                }
                
                .badge.pending {
                    background: #fff3cd !important;
                    color: #856404 !important;
                }
                
                .badge.verified {
                    background: #d4edda !important;
                    color: #155724 !important;
                }
                
                /* CARD FOOTER */
                .card-footer {
                    padding: 10px 15px;
                    background: #f8f9fa;
                    border-top: 1px solid #eee;
                    font-size: 12px;
                    display: flex;
                    justify-content: space-between;
                    font-weight: 600;
                }
                
                .card-footer span i {
                    margin-right: 5px;
                }
                
                /* PAGE BREAK */
                .page-break {
                    page-break-before: always;
                }
                
                /* FOOTER */
                .footer {
                    margin-top: 50px;
                    text-align: center;
                    color: #999;
                    font-size: 12px;
                    border-top: 1px dashed #ccc;
                    padding-top: 20px;
                }
                
                @media print {
                    body { 
                        padding: 15px;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    .page-break { 
                        page-break-before: always; 
                    }
                }
            </style>
        </head>
        <body>
            <!-- Page 1: Summary -->
            <div class="report-header">
                <h1>IMU <span>QR</span> ENTERPRISE</h1>
                <div class="date">
                    <i class="fas fa-calendar-alt" style="color: #FB8500;"></i> ${dateStr} at ${timeStr}
                </div>
            </div>
            
            <div class="page-title">
                <i class="fas fa-chart-bar" style="color: #FB8500; margin-right: 10px;"></i>
                Sales & Verification Summary
            </div>
            
            ${summaryTable.outerHTML}
            
            <!-- Page 2: Live Monitor -->
            <div class="page-break"></div>
            
            <div class="page-title">
                <i class="fas fa-eye" style="color: #FB8500; margin-right: 10px;"></i>
                Live Transaction Monitor
            </div>
            
            <div class="monitor-grid">
                ${simpleCard.outerHTML}
                ${vipCard.outerHTML}
                ${vvipCard.outerHTML}
            </div>
            
            <div class="footer">
                <i class="fas fa-qrcode" style="color: #FB8500;"></i>
                Generated by IMU QR Enterprise System • ${dateStr} • Page 1 of 2
                <i class="fas fa-qrcode" style="color: #FB8500; margin-left: 10px;"></i>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for everything to load then print
    setTimeout(() => {
        printWindow.print();
    }, 800);
}
    // --- GLOBAL RESET ---
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
                    }
                };
                xhr.send();
            } else {
                alert("Incorrect password.");
            }
        }
    }

    // Add Font Awesome
    const fontAwesome = document.createElement('link');
    fontAwesome.rel = 'stylesheet';
    fontAwesome.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
    document.head.appendChild(fontAwesome);

    // Initialize on page load
    window.onload = function() {
        resetAllStats();
        loadHomeData();
    };

    // AUTO-REFRESH
    setInterval(() => {
        const homeTab = document.getElementById('home');
        if (homeTab && homeTab.classList.contains('active')) {
            loadHomeData();
        }
    }, 1000);

    setInterval(() => {
        const homeTab = document.getElementById('home');
        if (homeTab && homeTab.classList.contains('active')) {
            loadHomeData();
        }
    }, 3000);
</script>
</body>
</html>
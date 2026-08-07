<?php
$title = "Cloud Sync & Backup";
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-xl-7">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3">
            <h1 class="h3" style="color: #1f1f1f; font-weight: 400;">Cloud Sync & Backup</h1>
        </div>

        <style>
            .google-card {
                background: #fff;
                border-radius: 28px;
                overflow: hidden;
                margin-bottom: 24px;
                border: none;
                box-shadow: 0 1px 2px 0 rgba(60, 64, 67, 0.3);
                padding: 32px;
            }
            .google-btn {
                background-color: #0b57d0;
                color: #fff;
                border-radius: 24px;
                padding: 10px 24px;
                font-weight: 500;
                border: none;
                transition: background-color 0.2s;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none;
                font-size: 14px;
                cursor: pointer;
            }
            .google-btn:hover {
                background-color: #0842a0;
            }
            .google-btn:disabled {
                background-color: #e3e3e3;
                color: #a0a0a0;
                cursor: not-allowed;
            }
            .status-icon {
                font-size: 48px;
                margin-bottom: 16px;
            }
            .sync-progress {
                height: 8px;
                border-radius: 4px;
                margin-top: 24px;
                margin-bottom: 8px;
            }
        </style>

        <div class="google-card text-center">
            <?php if (empty($settings['cloud_url']) || empty($settings['sync_api_key'])): ?>
                <span class="material-symbols-outlined text-warning status-icon">warning</span>
                <h4 class="fw-bold mb-2">Sync Not Configured</h4>
                <p class="text-muted mb-4">You need to configure your Cloud Server URL and Sync API Key in the settings before you can perform a backup.</p>
                <a href="<?= BASE_URL ?>/settings" class="google-btn mx-auto">Go to Settings</a>
            <?php else: ?>
                <span class="material-symbols-outlined text-primary status-icon" id="syncIcon">cloud_sync</span>
                <h4 class="fw-bold mb-2" id="syncTitle">Ready to Backup</h4>
                <p class="text-muted mb-4" id="syncSubtitle">Push your latest local sales and changes to the online server.</p>
                
                <div class="alert alert-info text-start d-inline-block text-start mb-4" style="border-radius: 16px; max-width: 400px; font-size: 14px;">
                    <strong>Cloud URL:</strong> <?= htmlspecialchars($settings['cloud_url']) ?>
                </div>

                <?php if (isset($totalUnsynced)): ?>
                    <div class="mb-4">
                        <?php if ($totalUnsynced > 0): ?>
                            <div class="text-warning fw-bold mb-2">
                                <span class="material-symbols-outlined align-middle" style="font-size: 20px;">pending</span>
                                <?= $totalUnsynced ?> Unsynced Local Records
                            </div>
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <?php foreach ($unsyncedCounts as $label => $count): ?>
                                    <span class="badge bg-light text-dark border"><?= $count ?> <?= $label ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-success fw-bold">
                                <span class="material-symbols-outlined align-middle" style="font-size: 20px;">check_circle</span>
                                All local records are synced!
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="mb-4 text-muted small fw-bold d-flex align-items-center justify-content-center gap-2" id="lastAutoSyncContainer">
                    <span class="material-symbols-outlined" style="font-size: 16px;">history</span>
                    Last Auto-Sync: <span id="lastAutoSyncTime">Never</span>
                </div>

                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <button class="google-btn" id="syncBtn" onclick="startSync()">
                        <span class="material-symbols-outlined">backup</span> Push Local Changes
                    </button>
                    <button class="google-btn bg-success border-0" id="pullBtn" onclick="startPull()">
                        <span class="material-symbols-outlined">download</span> Pull Cloud Updates
                    </button>
                </div>

                <div id="syncProgressContainer" class="d-none text-start mt-4">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span id="syncStatusText">Preparing sync...</span>
                        <span id="syncPercentage">0%</span>
                    </div>
                    <div class="progress sync-progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="syncProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
async function startSync() {
    const btn = document.getElementById('syncBtn');
    const progressContainer = document.getElementById('syncProgressContainer');
    const progressBar = document.getElementById('syncProgressBar');
    const statusText = document.getElementById('syncStatusText');
    const percentText = document.getElementById('syncPercentage');
    const icon = document.getElementById('syncIcon');
    const title = document.getElementById('syncTitle');

    btn.disabled = true;
    progressContainer.classList.remove('d-none');
    
    icon.classList.add('text-primary');
    icon.classList.remove('text-success', 'text-danger');
    icon.textContent = 'cloud_sync';
    
    title.textContent = 'Syncing...';
    statusText.textContent = 'Gathering local data...';
    progressBar.style.width = '20%';
    percentText.textContent = '20%';

    try {
        const response = await fetch('<?= BASE_URL ?>/sync/push', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            progressBar.style.width = '100%';
            percentText.textContent = '100%';
            statusText.textContent = 'Backup completed successfully!';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            
            icon.classList.remove('text-primary');
            icon.classList.add('text-success');
            icon.textContent = 'check_circle';
            title.textContent = 'Backup Complete';
        } else {
            throw new Error(data.message || 'Unknown error occurred.');
        }
    } catch (err) {
        progressBar.style.width = '100%';
        progressBar.classList.remove('progress-bar-animated');
        progressBar.classList.replace('bg-primary', 'bg-danger');
        
        statusText.textContent = 'Error: ' + err.message;
        statusText.classList.add('text-danger');
        percentText.textContent = 'Failed';
        
        icon.classList.remove('text-primary');
        icon.classList.add('text-danger');
        icon.textContent = 'error';
        title.textContent = 'Backup Failed';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">refresh</span> Try Again';
    }
}

async function startPull() {
    const btn = document.getElementById('pullBtn');
    const pushBtn = document.getElementById('syncBtn');
    const progressContainer = document.getElementById('syncProgressContainer');
    const progressBar = document.getElementById('syncProgressBar');
    const statusText = document.getElementById('syncStatusText');
    const percentText = document.getElementById('syncPercentage');
    const icon = document.getElementById('syncIcon');
    const title = document.getElementById('syncTitle');

    btn.disabled = true;
    pushBtn.disabled = true;
    progressContainer.classList.remove('d-none');
    
    icon.classList.add('text-primary');
    icon.classList.remove('text-success', 'text-danger');
    icon.textContent = 'cloud_download';
    
    title.textContent = 'Pulling...';
    statusText.textContent = 'Connecting to cloud...';
    statusText.classList.remove('text-danger');
    progressBar.classList.remove('bg-danger', 'bg-success');
    progressBar.classList.add('bg-primary', 'progress-bar-animated');
    progressBar.style.width = '30%';
    percentText.textContent = '30%';

    try {
        const response = await fetch('<?= BASE_URL ?>/sync/pull', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        
        if (data.success) {
            progressBar.style.width = '100%';
            percentText.textContent = '100%';
            statusText.textContent = data.message || 'Pull completed successfully!';
            progressBar.classList.remove('progress-bar-animated');
            progressBar.classList.add('bg-success');
            
            icon.classList.remove('text-primary');
            icon.classList.add('text-success');
            icon.textContent = 'check_circle';
            title.textContent = 'Pull Complete';
        } else {
            throw new Error(data.message || 'Unknown error occurred');
        }
    } catch (err) {
        console.error(err);
        progressBar.style.width = '100%';
        progressBar.classList.remove('progress-bar-animated');
        progressBar.classList.replace('bg-primary', 'bg-danger');
        
        statusText.textContent = 'Error: ' + err.message;
        statusText.classList.add('text-danger');
        percentText.textContent = 'Failed';
        
        icon.classList.remove('text-primary');
        icon.classList.add('text-danger');
        icon.textContent = 'error';
        title.textContent = 'Pull Failed';
    } finally {
        btn.disabled = false;
        pushBtn.disabled = false;
        btn.innerHTML = '<span class="material-symbols-outlined">refresh</span> Try Pulling Again';
    }
}

// Auto-Sync UI Updater
function updateLastAutoSyncTime() {
    const timeSpan = document.getElementById('lastAutoSyncTime');
    if (!timeSpan) return;

    const lastSyncStr = localStorage.getItem('last_auto_sync_time');
    if (!lastSyncStr) {
        timeSpan.textContent = 'Never';
        return;
    }

    const lastSync = parseInt(lastSyncStr, 10);
    const date = new Date(lastSync);
    timeSpan.textContent = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + ' (' + date.toLocaleDateString() + ')';
}

// Initial load
document.addEventListener('DOMContentLoaded', updateLastAutoSyncTime);

// Listen for background auto-sync completion
window.addEventListener('autoSyncCompleted', function(e) {
    updateLastAutoSyncTime();
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/main.php';
?>

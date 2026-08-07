/**
 * Auto-Sync Background Script
 * Runs silently in the background every X minutes to push data to the cloud.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 5 minutes in milliseconds
    const SYNC_INTERVAL = 5 * 60 * 1000; 
    let syncTimer = null;
    let isSyncing = false;

    // We use localStorage to track when the last auto-sync fired.
    // This ensures that if the user clicks around and reloads the page, 
    // the timer doesn't reset to 0 every single time.
    function checkAndRunSync() {
        const lastSyncStr = localStorage.getItem('last_auto_sync_time');
        const now = Date.now();
        
        if (!lastSyncStr) {
            // First time ever running
            runBackgroundSync();
            return;
        }

        const lastSync = parseInt(lastSyncStr, 10);
        if (now - lastSync >= SYNC_INTERVAL) {
            // It has been 5 minutes since the last sync
            runBackgroundSync();
        }
    }

    async function runBackgroundSync() {
        if (isSyncing) return;
        
        // Don't try to sync if there's no internet connection
        if (!navigator.onLine) {
            console.log("Auto-Sync: Offline. Skipping.");
            return;
        }

        isSyncing = true;
        console.log("Auto-Sync: Starting background backup...");

        try {
            const fetchUrl = (window.APP_BASE_URL || '') + '/sync/push';
            const response = await fetch(fetchUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    console.log("Auto-Sync: Push completed successfully.");
                    
                    // Now, silently pull any updates from the cloud!
                    console.log("Auto-Sync: Starting background pull...");
                    const pullUrl = (window.APP_BASE_URL || '') + '/sync/pull';
                    const pullResponse = await fetch(pullUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (pullResponse.ok) {
                        const pullData = await pullResponse.json();
                        if (pullData.success) {
                            console.log("Auto-Sync: Pull completed successfully.");
                        } else {
                            console.warn("Auto-Sync: Pull failed - ", pullData.message);
                        }
                    }

                    // Update the timestamp on success of the whole cycle
                    localStorage.setItem('last_auto_sync_time', Date.now().toString());
                    
                    // Dispatch a custom event so the Dashboard UI can update if it's currently open
                    window.dispatchEvent(new CustomEvent('autoSyncCompleted', { detail: data }));
                } else {
                    console.warn("Auto-Sync: Server returned error - ", data.message);
                }
            } else {
                console.warn("Auto-Sync: Network response was not ok. Status:", response.status);
            }
        } catch (error) {
            console.error("Auto-Sync: Fetch failed - ", error);
        } finally {
            isSyncing = false;
        }
    }

    // Check every 30 seconds to see if 5 minutes have passed since last sync
    syncTimer = setInterval(checkAndRunSync, 30000);
    
    // Also do an initial check right away when the page loads
    // We delay it by 5 seconds so it doesn't slow down the initial page render
    setTimeout(checkAndRunSync, 5000);
});
